<?php

namespace Bixie\Cart\Controller;

use Bixie\Cart\Cart\CartFactory;
use Bixie\Cart\Model\Order;
use Bixie\Cart\Payment\PaymentException;
use Pagekit\Application as App;
use Bixie\Cart\Cart\CartItem;

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
     * @Request({"cartItems": "array", "cardData": "array", "checkout": "array"}, csrf=true)
     */
    public function checkoutAction($cartItemsData, $cardData, $checkout)
    {
		$cartItems = $this->saveAction($cartItemsData);

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

		} catch (PaymentException $e) {
			return ['error' => $e->getMessage()];
		}

		return [
			'cartItems' => array_values($cartItems),
			'succesurl' => App::url('@cart/paymentreturn', ['transaction_id' => $order->transaction_id]),
			'checkout' => $checkout,
			'order' => $order
		];
    }

}
