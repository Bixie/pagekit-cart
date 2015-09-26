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

	/**
	 * @param string $type
	 * @param array  $cardData
	 * @param Order  $order
	 * @return bool|ResponseInterface
	 * @throws PaymentException
	 */
	public function getPayment ($type, $cardData, Order &$order) {
		// Setup payment gateway
		$gateway = Omnipay::create($type);

		if (!isset($this->config[$type])) {
			throw new PaymentException("Payment method $type not found in configuration");
		}
		$gateway->initialize($this->config[$type]);
		$order->transaction_id = md5(serialize($cardData) . serialize($order->toArray()));

		$card = new CreditCard(array_merge($cardData, $order->get('billing_address')));

		try {// Send purchase request
			$response = $gateway->purchase(
				[
					'amount' => $order->total_bruto,
					'currency' => $order->currency,
					'description' => __('Purchase %site%, #%transactionId%', [
						'%site%' => $this->app->config('system/site')['title'],
						'%transactionId%' => $order->transaction_id]),
					'transactionId' => $order->transaction_id,
//					'transactionReference' => $order->transaction_id,
//					'cardReference' => $order->transaction_id,
					'returnUrl' => App::url('@cart/paymentreturn', ['id' => $order->transaction_id]),
					'cancelUrl' => App::url('@cart/checkout'),
					'notifyUrl' => '',
					'issuer' => '',
					'card' => $card->getParameters()
				]
			)->send();

			// Process response
			if ($response->isSuccessful()) {

				// Payment was successful
				return $response;

			} elseif ($response->isRedirect()) {
				//todo
				// Redirect to offsite payment gateway
				$response->redirect();

			} else {

				// Payment failed
				throw new PaymentException($response->getMessage());
			}

		} catch (\Exception $e) {
			throw new PaymentException($e->getMessage(), $e->getCode(), $e);
		}
		return false;
	}

}