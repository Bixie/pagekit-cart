<?php


namespace Bixie\Cart\Payment;

use Bixie\Cart\CartModule;
use Omnipay\Common\CreditCard;
use Omnipay\Common\Message\ResponseInterface;
use Pagekit\Application as App;
use Bixie\Cart\Model\Order;
use Omnipay\Omnipay;

class PaymentHelper {

	const MODE_LIVE = 'live';
	const MODE_TEST = 'test';

	/**
	 * @var App
	 */
	protected $app;

	protected $config;

	protected $gateways;

	protected $mode;

	/**
	 * PaymentHelper constructor.
	 * @param App        $app
	 * @param CartModule $cart
	 */
	public function __construct (App $app, CartModule $cart) {
		$this->app = $app;
		$this->config = $cart->config('gateways');
		$this->mode = $cart->config('payment_live') ? PaymentHelper::MODE_LIVE : PaymentHelper::MODE_TEST;
	}

	/**
	 * @param mixed $mode
	 */
	public function setMode ($mode) {
		$this->mode = $mode;
	}

	/**
	 * @return mixed
	 */
	public function getMode () {
		return $this->mode;
	}

	public function getSettings () {
		$settings = [];
		foreach ($this->getGateways() as $gateway) {
			$shortName = $gateway->getShortName();
			if (!isset($this->config[$shortName])) {
				$this->config[$shortName] = ['active' => false];
			}
			$settings[$shortName] = array_merge($gateway->getDefaultParameters(), $this->config[$shortName]);

		}
		return $settings;
	}

	public function activeGatewaysData () {
		$gatewaysData = [];
		foreach ($this->getGateways() as $gateway) {
			if (!empty($this->config[$gateway->getShortName()]['active'])) {
				$gatewaysData[] = [
					'shortName' => $gateway->getShortName(),
					'name' => $gateway->getName()
				];
			}
		}
		return $gatewaysData;
	}

	public function getGateways () {
		if (!isset($this->gateways)) {
			$this->gateways = array_map(function($name) {
				return Omnipay::create($name);
			}, Omnipay::find());
		}
		return $this->gateways;
	}

	public function activeCheckoutData () {
		$checkouterror = $this->app['session']->get('_bixiePayment.checkouterror', '');
		$this->app['session']->remove('_bixiePayment.checkouterror');
		return [
			'checkouterror' => $checkouterror,
			'order_id' => $this->app['session']->get('_bixiePayment.order_id', 0),
			'checkout' => $this->app['session']->get('_bixiePayment.checkout')
		];
	}

	/**
	 * @param string $type
	 * @param array  $cardData
	 * @param Order  $order
	 * @return ResponseInterface
	 * @throws PaymentException
	 */
	public function getPayment ($type, $cardData, Order &$order) {
		// Setup payment gateway
		$gateway = Omnipay::create($type);

		if (!isset($this->config[$type])) {
			throw new PaymentException("Payment method $type not found in configuration");
		}
		$gateway->initialize($this->config[$type]);
		$order->transaction_id = md5(time() . serialize($cardData) . serialize($order->toArray()));
		$order->set('payment.method_name', $gateway->getName());

		$card = new CreditCard(array_merge($cardData, $order->get('billing_address')));

		try {// Send purchase request
			$params = [
				'amount' => $order->total_bruto,
				'currency' => $order->currency,
				'description' => __('Purchase %site%, #%transactionId%', [
					'%site%' => $this->app->config('system/site')['title'],
					'%transactionId%' => $order->transaction_id]),
				'transactionId' => $order->transaction_id,
				'transactionReference' => $order->transaction_id,
//					'cardReference' => $order->transaction_id,
				'returnUrl' => App::url('@cart/paymentreturn', ['transaction_id' => $order->transaction_id], true),
				'cancelUrl' => App::url('@cart/checkout', [], true),
				'notifyUrl' => '',
				'issuer' => '',
				'card' => $card->getParameters()
			];
			$response = $gateway->purchase($params)->send();
			$params['transactionReference'] = $response->getTransactionReference();

			$order->set('payment.message', $response->getMessage());
			// Process response
			if ($response->isSuccessful()) {

				// Payment was successful
				$order->set('payment.success', true);
				$this->app['session']->set('_bixiePayment.transaction_id', $order->transaction_id);
				$this->app['session']->remove('_bixiePayment.checkouterror');
				return $response;

			} elseif ($response->isRedirect()) {

				// Redirect to offsite payment gateway
				$this->app['session']->set('_bixiePayment.redirected', $type);
				$this->app['session']->set('_bixiePayment.checkout', $order->data);
				$this->app['session']->set('_bixiePayment.order_id', $order->id);
				$this->app['session']->set('_bixiePayment.params', $params);
				$this->app['session']->set('_bixiePayment.transaction_id', $order->transaction_id);
				return $response;

			} else {

				// Payment failed
				throw new \Exception($response->getMessage());
			}

		} catch (\Exception $e) {
			throw new PaymentException($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
	 * @param string $type
	 * @param Order  $order
	 * @return ResponseInterface
	 * @throws PaymentException
	 */
	public function getReturn ($type, Order &$order) {
		// Setup payment gateway
		if (!isset($this->config[$type])) {
			throw new PaymentException("Payment method $type not found in configuration");
		}

		try {

			$gateway = Omnipay::create($type);

			$gateway->initialize($this->config[$type]);

			$params = $this->app['session']->get('_bixiePayment.params');

			$params['clientIp'] = $this->app['request']->getClientIp();

			$response = $gateway->completePurchase($params)->send();

			// Process response
			if ($response->isSuccessful()) {

				// Payment was successful
				$order->set('payment.success', true);
				$this->app['session']->remove('_bixiePayment.checkout');
				$this->app['session']->remove('_bixiePayment.order_id');
				$this->app['session']->remove('_bixiePayment.redirected');
				$this->app['session']->remove('_bixiePayment.params');
				$this->app['session']->remove('_bixiePayment.checkouterror');
				return $response;

			} elseif ($response->isRedirect()) {

				$this->app['session']->set('_bixiePayment.transaction_id', $order->transaction_id);
				// Redirect to offsite payment gateway
				$this->app['session']->set('_bixiePayment.redirected', $type);
				$this->app['session']->set('_bixiePayment.checkout', $order->data);
				$this->app['session']->set('_bixiePayment.order_id', $order->id);
				$this->app['session']->set('_bixiePayment.params', $params);
				return $response;

			} else {

				// Payment failed
				throw new \Exception($response->getMessage());
			}
		} catch (\Exception $e) {
			$this->app['session']->set('_bixiePayment.checkouterror', $e->getMessage());
			throw new PaymentException($e->getMessage(), $e->getCode(), $e);
		}

	}

}