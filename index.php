<?php

return [

	'name' => 'bixie/cart',

	'type' => 'extension',

	'main' => 'Bixie\\Cart\\CartModule',

	'autoload' => [

		'Bixie\\Cart\\' => 'src'

	],

	'nodes' => [

		'cart' => [
			'name' => '@cart',
			'label' => 'Cart',
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
				'Bixie\\Cart\\Controller\\CartApiController'
			]
		]

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

		'bixie/cart: manage settings' => [
			'title' => 'Manage settings'
		]

	],

	'settings' => '@cart/settings',

	'config' => [
		'mainpage_title' => '',
		'tags' => []
	],

	'events' => [

		'boot' => function ($event, $app) {

		},

		'view.scripts' => function ($event, $scripts) use ($app) {

			$scripts->register('uikit-lightbox', 'app/assets/uikit/js/components/lightbox.min.js', 'uikit');
		},

		'console.init' => function ($event, $console) {

			//$console->add(new \Bixie\Download\Console\Commands\DownloadTranslateCommand());

		}
	]

];
