<?php


namespace Bixie\Cart\Payment;

use Bixie\Cart\Cart\CartHandler;
use Bixie\Cart\Cart\PaymentOption;
use Bixie\Cart\CartException;
use Bixie\Cart\CartModule;
use Omnipay\Common\CreditCard;
use Omnipay\Common\Message\ResponseInterface;
use Pagekit\Application as App;
use Bixie\Cart\Model\Order;
use Omnipay\Omnipay;
use Symfony\Component\Routing\Generator\UrlGenerator;

class PaymentHelper {

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
	}

    /**
     * @return array
     */
	public function getSettings () {
		$settings = [];
		foreach ($this->getGateways() as $gateway) {
			$shortName = $gateway->getShortName();
			if (!isset($this->config[$shortName])) {
				$this->config[$shortName] = [
				    'active' => false, 'title' => '', 'price' => 0, 'settings' => [], 'data' => ['image' => ['src' => '']]
                ];
			}
			$settings[$shortName] = array_merge($gateway->getDefaultParameters(), $this->config[$shortName]);

		}
		return $settings;
	}

    /**
     * @return PaymentOption[]
     */
	public function activeGatewaysData () {
		$gatewaysData = [];
        /** @var \Omnipay\Common\GatewayInterface $gateway */
        foreach ($this->getGateways() as $gateway) {
            $shortName = $gateway->getShortName();
			if (!empty($this->config[$shortName]['active'])) {
			    $config = array_merge(['title' => '', 'price' => 0, 'data' => []], $this->config[$shortName]);
                $card = in_array($shortName, ['Stripe']) ? true : false;
				$gatewaysData[] = new PaymentOption([
					'name' => $shortName,
					'title' => $config['title'] ?: $gateway->getName(),
                    'price' => $config['price'],
                    'settings' => ['card' => $card],
                    'data' => $config['data']
				]);
			}
		}
		return $gatewaysData;
	}

    /**
     * @param $payment_option_name
     * @return bool|PaymentOption
     */
    public function getPaymentOption ($payment_option_name) {
        foreach ($this->activeGatewaysData() as $paymentOption) {
            if ($paymentOption->name == $payment_option_name) {
                return $paymentOption;
            }
        }
        return false;
    }
    /**
     * @return array
     */
	public function getGateways () {
		if (!isset($this->gateways)) {
			$this->gateways = array_map(function($name) {
				return Omnipay::create($name);
			}, Omnipay::find());
		}
		return $this->gateways;
	}

    /**
     * @return array
     */
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
	 * @param CartHandler $cartHandler
	 * @param array  $cardData
	 * @param Order  $order
	 * @return ResponseInterface
	 * @throws PaymentException
	 */
	public function getPayment (CartHandler $cartHandler, Order &$order, $cardData = []) {
        $type = $cartHandler->getPaymentOption()->name;
		// Setup payment gateway
		$gateway = Omnipay::create($type);

		if (!isset($this->config[$type])) {
			throw new PaymentException("Payment method $type not found in configuration");
		}
		$gateway->initialize($this->config[$type]);
		$order->transaction_id = md5(time() . serialize($cardData) . serialize($order->toArray()));
		$order->set('payment.method_name', $gateway->getName());

		$card = new CreditCard(array_merge($cardData, $cartHandler->getBillingAddress()->toArray()));

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
				'returnUrl' => App::url('@cart/checkout/paymentreturn', ['transaction_id' => $order->transaction_id], UrlGenerator::ABSOLUTE_URL),
				'cancelUrl' => App::url('@cart/checkout', [], UrlGenerator::ABSOLUTE_URL),
				'notifyUrl' => '',
				'issuer' => '',
				'card' => $card->getParameters()
			];
            /** @var \Omnipay\Common\Message\ResponseInterface $response */
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
				throw new CartException($response->getMessage());
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