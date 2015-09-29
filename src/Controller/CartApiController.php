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
		$return = ['error'=> true, 'registererror' => '', 'checkouterror' => ''];

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

		$order = Order::createNew($cartItems, $checkout)->calculateOrder();

		$payment_method = $checkout['payment']['method'];

		try {

			$payment = App::bixiePayment()->getPayment($payment_method, $cardData, $order);

			$order->status = Order::STATUS_CONFIRMED;
			$order->payment = $payment->getData();

			$order->save();

			//reset cart
			App::bixieCart()->reset();
			$cartItems = [];

			//send mail
			(new MailHelper($order))->sendMail();

			return [
				'cartItems' => array_values($cartItems),
				'succesurl' => App::url('@cart/paymentreturn', ['transaction_id' => $order->transaction_id]),
				'checkout' => $checkout,
				'order' => $order
			];

		} catch (PaymentException $e) {
			$return['checkouterror'] = $e->getMessage();
			return $return;
		}


    }

}
