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
			'controller' => 'Bixie\\Cart\\Controller\\CheckoutSiteController',
			'protected' => true,
			'frontpage' => false
		],

		'orders' => [
			'name' => '@cart/orders',
			'label' => 'Orders',
			'controller' => 'Bixie\\Cart\\Controller\\OrderSiteController',
			'protected' => true,
			'frontpage' => false
		]

	],

	'routes' => [

		'/cart' => [
			'name' => '@cart/admin',
			'controller' => [
				'Bixie\\Cart\\Controller\\CartController'
			]
		],
		'/api/cart' => [
			'name' => '@cart/api',
			'controller' => [
				'Bixie\\Cart\\Controller\\CartApiController',
				'Bixie\\Cart\\Controller\\OrderApiController',
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
			'url' => '@cart/admin/order',
			'access' => 'bixie/cart: manage cart',
			'active' => '@cart/admin*'
		],

		'cart: orders' => [
			'label' => 'Orders',
			'parent' => 'cart',
			'url' => '@cart/admin/order',
			'access' => 'bixie/cart: manage orders',
			'active' => '@cart/admin/order*'
		],

		'cart: settings' => [
			'label' => 'Settings',
			'parent' => 'cart',
			'url' => '@cart/admin/settings',
			'access' => 'bixie/cart: manage settings',
			'active' => '@cart/admin/settings'
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

	'settings' => '@cart/admin/settings',

	'config' => [
		'currency' => 'EUR',
		'vat' => 'high',
		'vat_view' => 'incl',
		'date_format' => 'M d Y, H:i:s',
		'addtocart' => [
			'show_vat' => true
		],
		'gateways' => [],
		'required_checkout' => [
			'billing_address.firstName',
			'billing_address.lastName',
			'billing_address.email',
			'billing_address.address1',
			'billing_address.postcode',
			'billing_address.city',
			'billing_address.country',
			'payment.method',
			'agreed'
		],
		'USDtoEUR' => 0.82481,
		'EURtoUSD' => 1.25415,
		'vatclasses' => [
			'none' => ['rate' => 0, 'name' => 'No taxes'],
			'low' => ['rate' => 6, 'name' => 'Low taxclass'],
			'high' => ['rate' => 21, 'name' => 'High taxclass']
		],
		'thankyou' => [
			'title' => 'Thank you for your order',
			'content' => '<p>Below are the details of your order.</p>'
		],
		'email' => [
			'admin_email' => 'admin@bixie.nl',
			'subject' => 'Order confirmation #$$transaction_id$$',
			'body' => '<p>Below are the details of your order.</p>'
		],
		'markdown_enabled' => false,
		'ordering' => 'title',
		'ordering_dir' => 'asc',
		'server_tz' => '+0200',
		'orders_per_page' => 20,
		'products_per_page' => 20
	],

	'events' => [

		'boot' => function ($event, $app) {
			$app->subscribe(new Bixie\Cart\Event\FileListener());
		},

		'view.data' => function ($event, $data) use ($app) {
			//todo only on when cart or module is active
			$cartItems = $app['bixieCart']->all();
			if (count($cartItems)) {
				$data->add('$cartItems', array_values($cartItems));
			}
			$data->add('$cart', [
				'config' => App::module('bixie/cart')->publicConfig()
			]);
		},

		'view.scripts' => function ($event, $scripts) use ($app) {
			$scripts->register('bixie-cart', 'bixie/cart:app/bundle/bixie-cart.js', ['vue']);
			//sections
			$scripts->register('download-section-cart', 'bixie/cart:app/bundle/download-section-cart.js', ['~bixie-downloads']);

			$scripts->register('uikit-accordion', 'app/assets/uikit/js/components/accordion.min.js', 'uikit');
			$scripts->register('uikit-lightbox', 'app/assets/uikit/js/components/lightbox.min.js', 'uikit');
		},

		'console.init' => function ($event, $console) {

			//$console->add(new \Bixie\Download\Console\Commands\DownloadTranslateCommand());

		}
	]

];
