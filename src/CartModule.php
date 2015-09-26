<?php

namespace Bixie\Cart;

use Bixie\Cart\Payment\PaymentHelper;
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
		$app['bixiePayment'] = function ($app) {
			return new PaymentHelper($app, $this);
		};

	}

	public function publicConfig () {
		$config = $this->config();
		unset($config['gateways']);
		return $config;
	}

	public function checkDownloadKey ($cartItem, $key) {
		//todo
		return $cartItem->id > 0;
	}

}
