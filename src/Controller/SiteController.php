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
    public function indexAction()
    {
        if (!App::node()->hasAccess(App::user())) {
            App::abort(403, __('Insufficient User Rights.'));
        }

//        $query = CartItem::where(['status = ?'], ['1'])->where(function ($query) {
//			return $query->where('roles IS NULL')->whereInSet('roles', App::user()->roles, false, 'OR');
//		})->orderBy($this->cart->config('ordering'), $this->cart->config('ordering_dir'));
//
//		foreach ($cartItems = $query->get() as $file) {
//			$file->content = App::content()->applyPlugins($file->content, ['file' => $file, 'markdown' => $file->get('markdown')]);
//        }
		$cartItems = [];
		return [
            '$view' => [
                'title' => App::node()->title,
                'name' => 'bixie/cart/cart.php'
            ],
      		'cart' => $this->cart,
			'config' => $this->cart->config(),
            'cartItems' => $cartItems
        ];
    }

}
