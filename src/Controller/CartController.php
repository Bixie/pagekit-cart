<?php

namespace Bixie\Cart\Controller;

use Bixie\Cart\Model\Order;
use Pagekit\Application as App;
use Pagekit\Event\Event;

/**
 * @Access(admin=true)
 */
class CartController
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
	 * @Route("/", name="order")
	 * @Request({"filter": "array", "page":"int"})
     */
    public function indexAction($filter = [], $page = null)
    {
        return [
            '$view' => [
                'title' => __('Orders'),
                'name'  => 'bixie/cart/admin/orders.php'
            ],
            '$data' => [
				'statuses' => Order::getStatuses(),
                'config' => [
                    'filter' => (object) $filter,
                    'page' => $page
                ]
            ]
        ];
    }

    /**
     * @Route("/order/edit", name="order/edit")
     * @Access("bixie/cart: manage orders")
     * @Request({"id": "int"})
     */
    public function editAction($id = 0)
    {
		/** @var Order $order */
		if (!$order = Order::where(compact('id'))->related('user')->first()) {

			if ($id) {
				App::abort(404, __('Invalid file id.'));
			}

			App::abort(401, __('Orders cannot be created.'));

		}

        $cartItems = $order->getCartItems();
		foreach ($cartItems as $cartItem) {
			$event = new Event('bixie.cart.admin.orderitem');
			App::trigger($event, [$order, $cartItem]);
			$cartItem->setTemplate('bixie.cart.admin.order', $event['bixie.cart.admin.order'] ? : '');
			$cartItem->setTemplate('bixie.cart.order_item', $event['bixie.cart.order_item'] ? : '');
		}
		$users = App::db()->createQueryBuilder()
			->from('@system_user')
			->execute('id, username')
			->fetchAll();

        /** @var \Bixie\Invoicemaker\InvoicemakerModule $invoicemaker */
        $invoices = [];
        if ($invoicemaker = App::module('bixie/invoicemaker')) {
            $invoices = $invoicemaker->getByExternKey('game2art.order.' . $order->id);
        }

        return [
			'$view' => [
				'title' => __('Edit order'),
				'name'  => 'bixie/cart/admin/order.php'
			],
			'$data' => [
				'statuses' => Order::getStatuses(),
				'users' => $users,
				'config' => $this->cart->config(),
				'order'  => $order
			],
            'cartItems' => $cartItems,
            'invoices' => $invoices,
            'cart' => $this->cart,
			'order' => $order
		];

    }

    /**
     * @Access("system: access settings")
     */
    public function settingsAction()
    {
		$config = $this->cart->config();
		$config['gateways'] = App::bixiePayment()->getSettings();
        return [
            '$view' => [
                'title' => __('Cart Settings'),
                'name'  => 'bixie/cart/admin/settings.php'
            ],
            '$data' => [
                'config' => $config,
                'timezones' => $this->cart->getTimeZomes()
            ],
			'gateways' => App::bixiePayment()->getGateways()
		];
    }
}
