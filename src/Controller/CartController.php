<?php

namespace Bixie\Cart\Controller;

use Bixie\Cart\Model\Order;
use Pagekit\Application as App;
use Pagekit\User\Model\Role;

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
     * @Request({"filter": "array", "page":"int"})
     */
    public function ordersAction($filter = null, $page = 0)
    {
        return [
            '$view' => [
                'title' => __('Orders'),
                'name'  => 'bixie/cart/admin/orders.php'
            ],
            '$data' => [
				'statuses' => Order::getStatuses(),
				'config'   => [
                    'ordering' => $this->cart->config('ordering'),
                    'ordering_dir' => $this->cart->config('ordering_dir'),
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

		if (!$order = Order::where(compact('id'))->first()) {

			if ($id) {
				App::abort(404, __('Invalid file id.'));
			}

			App::abort(401, __('Orders cannot be created.'));

		}


		return [
			'$view' => [
				'title' => __('Edit order'),
				'name'  => 'bixie/cart/admin/order.php'
			],
			'$data' => [
				'statuses' => Order::getStatuses(),
				'config' => $this->cart->config(),
				'order'  => $order
			],
			'cart' => $this->cart,
			'order' => $order
		];

    }

    /**
     * @Access("system: manage settings")
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
                'config' => $config
            ],
			'gateways' => App::bixiePayment()->getGateways()
		];
    }
}
