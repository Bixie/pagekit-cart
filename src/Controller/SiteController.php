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
				'name' => 'bixie/cart/checkout.php'
			],
			'cart' => $this->cart
		];
	}

	/**
	 * @Route("/paymentreturn")
	 * @Request({"id": "string"})
	 */
	public function paymentreturnAction($id = '')
	{
		$transaction_id = $id;

		return [
			'$view' => [
				'title' => __('Thank you'),
				'name' => 'bixie/cart/paymentreturn.php'
			],
			'transaction_id' => $transaction_id,
			'cart' => $this->cart
		];
	}

}