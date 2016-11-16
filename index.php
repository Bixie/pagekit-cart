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
			'name' => '@cart/checkout',
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
		'checkout_type' => 'page',
		'addtocart' => [
			'show_vat' => true
		],
		'gateways' => [],
		'required_checkout' => [
			'checkout.billing_address.firstName',
			'checkout.billing_address.lastName',
			'checkout.billing_address.email',
			'checkout.billing_address.address1',
			'checkout.billing_address.postcode',
			'checkout.billing_address.city',
			'checkout.billing_address.country',
			'checkout.payment.method',
			'checkout.agreed'
		],
		'vatclasses' => [
			'none' => ['rate' => 0, 'name' => 'No taxes'],
			'low' => ['rate' => 6, 'name' => 'Low taxclass'],
			'high' => ['rate' => 21, 'name' => 'High taxclass']
		],
		'thankyou' => [
			'title' => 'Thank you for your order',
			'content' => '<p>Below are the details of your order with id $$transaction_id$$.</p>',
			'markdown_enabled' => false
		],
		'email' => [
			'admin_email' => '',
			'subject' => 'Order confirmation #$$transaction_id$$',
			'body' => '<p>Below are the details of your order.</p>',
			'markdown_enabled' => false
		],
		'terms' => [
			'title' => 'Terms and conditions',
			'content' => '<p>Please enter your terms and conditions here</p>',
			'markdown_enabled' => false
		],
		'orders_per_page' => 20,
		'validation_key' => 'awe57GfkhkHSa74wghsgbFdg9874vbrf93',
		'server_tz' => 'Europe/Amsterdam',
		'USDtoEUR' => 0.82481,
		'EURtoUSD' => 1.1204,
		'products_per_page' => 20
	],

	'events' => [

		'boot' => function ($event, $app) {
			$app->subscribe(
				new Bixie\Cart\Event\UserListener()
			);
		},

		'view.scripts' => function ($event, $scripts) use ($app) {
			$scripts->register('jstz', 'bixie/cart:assets/jstz-1.0.5.min.js');
			$scripts->register('bixie-cart', 'bixie/cart:app/bundle/bixie-cart.js',
                ['bixie-pkframework', 'uikit-form-select', 'uikit-tooltip', 'uikit-notify', 'uikit-form-password', 'jstz']
            );
        },

        'view.data' => function ($event, $data) use ($app) {
            $data->add('$bix_cart', [
                'cart' => [],
                'countries' => $app->module('system/intl')->getCountries(),
                'checkout_url' => $app['url']('@cart/checkout'),
                'login_url' => $app['url']('@user/login', ['redirect' => $app['url']->current()]),
                'user' => $app['user'],
                'config' => $app->module('bixie/cart')->publicConfig()
            ]);
        }
        ,

		'console.init' => function ($event, $console) {

			$console->add(new \Bixie\Cart\Console\Commands\TranslateCommand());

		}
	]

];
