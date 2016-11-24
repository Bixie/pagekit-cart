<?php

namespace Bixie\Cart\Controller;

use Bixie\Cart\Calculation\OrderCalculator;
use Pagekit\Application as App;
use Bixie\Cart\Model\Order;
use Pagekit\Event\Event;

class OrderSiteController
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
	 * @Request({"filter": "array", "page":"int"})
	 */
	public function indexAction($filter = null, $page = 0)
	{

		$user = App::user();

		return [
			'$view' => [
				'title' => __('Your orders'),
				'name' => 'bixie/cart/orders.php'
			],
			'$data' => [
				'statuses' => Order::getStatuses(),
				'user' => $user,
				'config'   => [
					'edit_url' => App::url('@cart/orders/detail', ['transaction_id' => ':transaction_id']),
					'ordering' => 'created',
					'ordering_dir' => 'desc',
					'filter' => (object)$filter,
					'page'   => $page,
					'limit'   => $this->cart->config('orders_per_page')
				]
			],
			'user' => $user,
			'cart' => $this->cart,
			'node' => App::node()
		];
	}

	/**
	 * @Route("/detail/{transaction_id}")
	 * @Request({"transaction_id": "string"})
	 */
	public function detailAction($transaction_id = '')
	{

		$user = App::user();
		/** @var Order $order */
		if (!$order = Order::where(compact('transaction_id'))->related('user')->first()) {

			if ($transaction_id) {
				App::abort(404, __('Invalid transaction id.'));
			}

			App::abort(401, __('Transaction ID missing'));

		}

		if (!$user->hasAccess('cart: manage orders') && ($user->id && $user->id != $order->user_id)) {
			App::abort(403, __('No access to this order.'));
		}

        /** @var \Bixie\Invoicemaker\InvoicemakerModule $invoicemaker */
        $invoices = [];
        if ($invoicemaker = App::module('bixie/invoicemaker')) {
            $invoices = $invoicemaker->getByExternKey('game2art.order.' . $order->id);
        }

        /** @var OrderCalculator $calculator */
        $calculator = App::cartCalculator($order);

        $orderItems = $calculator->getItems();
        foreach ($orderItems as $orderItem) {
            $event = new Event('bixie.cart.admin.orderitem');
            App::trigger($event, [$order, $orderItem->getItem()]);
            $orderItem->getItem()->setTemplate('bixie.cart.order_item', $event['bixie.cart.order_item'] ? : '');
        }

		return [
			'$view' => [
				'title' => __('Details of order %transaction_id%', ['%transaction_id%' => $order->transaction_id]),
				'name' => 'bixie/cart/order.php'
			],
			'calculator' => $calculator,
			'orderItems' => $orderItems,
			'invoices' => $invoices,
			'order' => $order,
			'cart' => $this->cart,
			'node' => App::node()
		];
	}

	/**
	 * @Route("/findorder")
	 */
	public function findorderAction()
	{

		$user = App::user();

		if ($user->isAuthenticated()) {
			return App::redirect('@cart/orders');
		}

		return [
			'$view' => [
				'title' => __('Find your order'),
				'name' => 'bixie/cart/findorder.php'
			],
			'cart' => $this->cart,
			'node' => App::node()
		];
	}


}