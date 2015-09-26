<?php

namespace Bixie\Cart\Controller;

use Bixie\Cart\Cart\CartFactory;
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
     * @Request({"cartItems": "array", "checkout": "array"}, csrf=true)
     */
    public function checkoutAction($cartItemsData, $checkout)
    {
		$cartItems = $this->saveAction($cartItemsData);

		return ['cartItems' => array_values($cartItems), 'checkout' => $checkout];
    }

}
