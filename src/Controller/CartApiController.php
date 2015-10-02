<?php

namespace Bixie\Cart\Controller;

use Bixie\Cart\Cart\UserHelper;
use Pagekit\Application as App;
use Bixie\Cart\Cart\CartFactory;
use Bixie\Cart\Cart\MailHelper;
use Bixie\Cart\Model\Order;
use Pagekit\Application\Exception;
use Bixie\Cart\Payment\PaymentException;
use Bixie\Cart\Cart\CartItem;
use Pagekit\User\Model\User;

/**
 * @Route("cart", name="cart")
 */
class CartApiController
{
	/**
	 * @var \Bixie\Cart\CartModule
	 */
	protected $cart;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->cart = App::module('bixie/cart');
	}

    /**
     * @Route("/", methods="POST")
     * @Request({"cartItems": "array"}, csrf=true)
	 * @return CartItem[]
	 */
    public function saveAction($data)
    {
		/** @var CartFactory $bixieCart */
		/** @var CartItem $cartItem */
		$bixieCart = App::bixieCart()->loadFromSession();
		$ids = [];
		foreach ($data as $cartData) {
			if ($cartItem = $bixieCart->load($cartData)) {
				$ids[] = $cartItem->getId();
				$bixieCart[$cartItem->getId()] = $cartItem;
			}
		}
		foreach(array_diff($bixieCart->ids(), $ids) as $id) {
			unset($bixieCart[$id]);
		}

		$bixieCart->saveCartItems();

		return array_values($bixieCart->all());
    }

	/**
     * @Route("/checkout", methods="POST")
     * @Request({"cartItems": "array", "cardData": "array", "user": "array", "checkout": "array"}, csrf=true)
     */
    public function checkoutAction($cartItemsData, $cardData, $userData, $checkout)
    {
		$return = ['error'=> true, 'registererror' => '', 'checkouterror' => '', 'order' => []];

		$cartItems = $this->saveAction($cartItemsData);

		//register user?
		if (empty($userData['id']) && !empty($userData['username'])) {

			try {

				$userData['name'] = $checkout['billing_address']['firstName'] . ' ' . $checkout['billing_address']['lastName'];
				$userData['email'] = $checkout['billing_address']['email'];

				$userHelper = new UserHelper();
				$userHelper->createUser($userData, User::STATUS_ACTIVE);
				$userHelper->login(['username' => $userData['username'], 'password' => $userData['password']]);

			} catch (Exception $e) {
				$return['registererror'] = $e->getMessage();
				return $return;
			}

		}

		/** @var Order $order */
		if ($checkout['order_id'] > 0) {
			$order = Order::find($checkout['order_id']);
			$order->currency = $checkout['currency'];
			$order->cartItemsData = $cartItemsData;
			$order->calculateOrder();
		} else {
			$order = Order::createNew($cartItems, $checkout)->calculateOrder();
		}

		$payment_method = $checkout['payment']['method'];

		try {

			$paymentResponse = App::bixiePayment()->getPayment($payment_method, $cardData, $order);

			$redirectUrl = App::url('@cart/paymentreturn', ['transaction_id' => $order->transaction_id], true);

			if ($paymentResponse->isRedirect()) {
				$redirectUrl = $paymentResponse->getRedirectUrl();
			}

			if ($paymentResponse->isSuccessful()) {

				$order->status = Order::STATUS_CONFIRMED;

				//reset cart
				App::bixieCart()->reset();
				$cartItems = [];

				//send mail
				(new MailHelper($order))->sendMail();
			}

			$order->payment = $paymentResponse->getData();
			$order->save();

			return [
				'cartItems' => array_values($cartItems),
				'redirectUrl' => $redirectUrl,
				'checkout' => $checkout,
				'order' => $order
			];

		} catch (PaymentException $e) {
			$return['order'] = $order;
			$return['checkouterror'] = $e->getMessage();
			return $return;
		}


    }

	/**
	 * @Route("/terms", methods="GET")
	 */
	public function termsAction()
	{
		$title = $this->cart->config('terms.title');
		$content = App::content()->applyPlugins($this->cart->config('terms.content'), ['markdown' => $this->cart->config('terms.markdown_enabled')]);;
		return ['html' => App::view('bixie/cart/templates/terms.php', compact('content', 'title'), 'raw')];
	}

}
