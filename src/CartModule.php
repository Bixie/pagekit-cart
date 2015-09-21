<?php

namespace Bixie\Cart;

use Pagekit\Application as App;
use Pagekit\Module\Module;
use Bixie\Cart\Model\CartItem;

class CartModule extends Module {
	/**
	 * @var array
	 */
	protected $types;

	/**
	 * {@inheritdoc}
	 */
	public function main (App $app) {

	}

	public function getDownloadKey (CartItem $cartItem) {
		//todo
		$key = 'df.' . $cartItem->slug;
		return $key;
	}

	public function checkDownloadKey (CartItem $cartItem, $key) {
		//todo
		return $cartItem->id > 0;
	}

}
