<?php
use Pagekit\Application as App;

return [

	'name' => 'bixie/cart',

	'type' => 'extension',

	'main' => 'Bixie\\Cart\\CartModule',

	'autoload' => [

		'Bixie\\Cart\\' => 'src'

	],

	'nodes' => [

		'checkout' => [
			'name' => '@cart',
			'label' => 'Checkout',
			'controller' => 'Bixie\\Cart\\Controller\\SiteController',
			'protected' => true,
			'frontpage' => false
		]

	],

	'routes' => [

		'/cart' => [
			'name' => '@cart',
			'controller' => [
				'Bixie\\Cart\\Controller\\CartController'
			]
		],
		'/api/cart' => [
			'name' => '@cart/api',
			'controller' => [
				'Bixie\\Cart\\Controller\\CartApiController',
				'Bixie\\Cart\\Controller\\ProductApiController'
			]
		]

	],

	'widgets' => [

		'widgets/cart.php'

	],

	'resources' => [

		'bixie/cart:' => ''

	],

	'menu' => [

		'cart' => [
			'label' => 'Cart',
			'icon' => 'bixie/cart:icon.svg',
			'url' => '@cart/orders',
			'access' => 'bixie/cart: manage cart',
			'active' => '@cart/orders*'
		],

		'cart: orders' => [
			'label' => 'Orders',
			'parent' => 'cart',
			'url' => '@cart/orders',
			'access' => 'bixie/cart: manage orders',
			'active' => '@cart/orders*'
		],

		'cart: settings' => [
			'label' => 'Settings',
			'parent' => 'cart',
			'url' => '@cart/settings',
			'access' => 'bixie/cart: manage settings',
			'active' => '@cart/settings*'
		]

	],

	'permissions' => [

		'bixie/cart: manage cart' => [
			'title' => 'Manage cart'
		],

		'bixie/cart: manage orders' => [
			'title' => 'Manage orders'
		],

		'bixie/cart: manage products' => [
			'title' => 'Manage products'
		],

		'bixie/cart: manage settings' => [
			'title' => 'Manage settings'
		]

	],

	'settings' => '@cart/settings',

	'config' => [
		'ordering' => 'title',
		'ordering_dir' => 'asc',
		'orders_per_page' => 20,
		'products_per_page' => 20,
		'currency' => 'EUR',
		'vat' => 'high',
		'vat_view' => 'incl',
		'USDtoEUR' => 1.25415,
		'EURtoUSD' => 0.82481,
		'default_payment' => 'PAYPAL',
		'required_checkout' => ['invoice_address.name', 'invoice_address.address', 'invoice_address.zipcode', 'invoice_address.city', 'invoice_address.country', 'agreed'],
		'addtocart' => [
			'show_vat' => true
		],
		'vatclasses' => [
			'none' => ['rate' => 0, 'name' => 'No taxes'],
			'low' => ['rate' => 6, 'name' => 'Low taxclass'],
			'high' => ['rate' => 21, 'name' => 'High taxclass']
		]
	],

	'events' => [

		'boot' => function ($event, $app) {
			$app->subscribe(new Bixie\Cart\Event\FileListener());
		},

		'after@cart/checkout' => function ($event, $request) use ($app) {
			App::view()->data('$cart', [
				'config' => App::module('bixie/cart')->config()
			]);
		},

		'view.data' => function ($event, $data) use ($app) {
			$cartItems = $app['bixieCart']->all();
			if (count($cartItems)) {
				$data->add('$cartItems', array_values($cartItems));
			}

		},

		'view.scripts' => function ($event, $scripts) use ($app) {
			$scripts->register('bixie-cart', 'bixie/cart:app/bundle/bixie-cart.js', ['vue']);
			//sections
			$scripts->register('download-section-cart', 'bixie/cart:app/bundle/download-section-cart.js', ['~bixie-downloads']);

			$scripts->register('uikit-lightbox', 'app/assets/uikit/js/components/lightbox.min.js', 'uikit');
		},

		'console.init' => function ($event, $console) {

			//$console->add(new \Bixie\Download\Console\Commands\DownloadTranslateCommand());

		}
	]

];
