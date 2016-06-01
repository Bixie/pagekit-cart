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
    public function indexAction($filter = null, $page = 0)
    {
        return [
            '$view' => [
                'title' => __('Orders'),
                'name'  => 'bixie/cart/admin/orders.php'
            ],
            '$data' => [
				'statuses' => Order::getStatuses(),
				'config'   => [
                    'ordering' => 'created',
                    'ordering_dir' => 'desc',
                    'filter' => $filter,
                    'page'   => $page
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
		foreach ($order->getCartItems() as $cartItem) {
			$event = new Event('bixie.admin.orderitem');
			App::trigger($event, [$order, $cartItem]);
			$cartItem->setTemplate('bixie.admin.order', $event['bixie.admin.order'] ? : '');
			$cartItem->setTemplate('bixie.cart.order_item', $event['bixie.cart.order_item'] ? : '');
		}
		$users = App::db()->createQueryBuilder()
			->from('@system_user')
			->where('status = 1')
			->execute('id, username')
			->fetchAll();

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
