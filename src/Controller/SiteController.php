<?php

namespace Bixie\Cart\Controller;

use Bixie\Cart\Cart\MailHelper;
use Bixie\Cart\Model\Order;
use Pagekit\Application as App;
use Pagekit\Event\Event;

class SiteController
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
	 * @Route("/")
	 */
	public function checkoutAction()
	{
		return [
			'$view' => [
				'title' => __('Checkout'),
				'name' => 'bixie/cart/checkout.php'
			],
			'cart' => $this->cart
		];
	}

	/**
	 * @Route("/paymentreturn")
	 * @Request({"transaction_id": "string"})
	 */
	public function paymentreturnAction($transaction_id = '')
	{

		if ($transaction_id != App::session()->get('_bixiePayment.transaction_id')) {
			App::abort(401, __('Invalid request.'));
		}

		/** @var Order $order */
		if (!$order = Order::findByTransaction_id($transaction_id)) {
			App::abort(404, __('Order not found.'));
		}

		$content = '';
		if ($this->cart->config('thankyou.content')) {
			$content = App::content()->applyPlugins($this->cart->config('thankyou.content'), ['markdown' => $this->cart->config('markdown_enabled')]);
			$content = (new MailHelper($order))->replaceString($content);
		}

		foreach ($order->getCartItems() as $cartItem) {
			$event = new Event('bixie.cart.orderitem');
			App::trigger($event, [$order, $cartItem]);
			$cartItem->setTemplate('bixie.cart.order_item', $event['bixie.cart.order_item'] ? : '');
		}

		return [
			'$view' => [
				'title' => $this->cart->config('thankyou.title'),
				'name' => 'bixie/cart/paymentreturn.php'
			],
			'title' => $this->cart->config('thankyou.title'),
			'content' => $content,
			'order' => $order,
			'cart' => $this->cart
		];
	}

}