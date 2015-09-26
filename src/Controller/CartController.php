<?php

namespace Bixie\Cart\Controller;

use Pagekit\Application as App;
use Bixie\Cart\Cart\CartItem;
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
    public function ordersAction($filter = null, $page = 1)
    {
        return [
            '$view' => [
                'title' => __('Orders'),
                'name'  => 'bixie/cart/admin/orders.php'
            ],
            '$data' => [
				'statuses' => CartItem::getStatuses(),
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
        try {

            if (!$cartItem = CartItem::where(compact('id'))->first()) {

                if ($id) {
                    App::abort(404, __('Invalid file id.'));
                }


				$cartItem = CartItem::create([
					'status' => 1,
					'slug' => '',
					'data' => [],
					'tags' => [],
					'date' => new \DateTime()
				]);

				$cartItem->set('markdown', $this->cart->config('markdown'));

			}


            return [
                '$view' => [
                    'title' => $id ? __('Edit download') : __('Add download'),
                    'name'  => 'bixie/cart/admin/file.php'
                ],
                '$data' => [
					'statuses' => CartItem::getStatuses(),
					'roles'    => array_values(Role::findAll()),
					'config' => $this->cart->config(),
                	'cartItem'  => $cartItem
                ],
                'file' => $cartItem
            ];

        } catch (\Exception $e) {

            App::message()->error($e->getMessage());

            return App::redirect('@cart/cart');
        }
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
