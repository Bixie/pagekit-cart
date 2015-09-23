<?php

namespace Bixie\Cart;

use Pagekit\Application as App;
use Pagekit\Module\Module;
use Bixie\Cart\Cart\CartFactory;

class CartModule extends Module {
	/**
	 * @var array
	 */
	protected $types;

	/**
	 * {@inheritdoc}
	 */
	public function main (App $app) {
		$app['bixieCart'] = function ($app) {
			return new CartFactory($app['session']);
		};

	}

	public function getDownloadKey ($cartItem) {
		//todo
		$key = 'df.' . $cartItem->slug;
		return $key;
	}

	public function checkDownloadKey ($cartItem, $key) {
		//todo
		return $cartItem->id > 0;
	}

}
