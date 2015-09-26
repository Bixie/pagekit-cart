<?php

namespace Bixie\Cart\Controller;

use Pagekit\Application as App;
use Bixie\Cart\Model\CartItem;

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
		$i=0;

		return [
			'$view' => [
				'title' => __('Checkout'),
				'name' => 'bixie/cart/checkout.php',
				'link:feed' => [
					'rel' => 'alternate',
				]
			],
			'cart' => $this->cart
		];
	}

}