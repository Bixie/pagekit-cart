<?php

use Pagekit\Application as App;

return [

    'name' => 'bixie/widget-cart',

    'label' => 'Cart',

    'events' => [

        'view.scripts' => function ($event, $scripts) use ($app) {
            $scripts->register('widget-cart', 'bixie/cart:app/bundle/widget-cart.js', ['~widgets']);
        },

        'view.data' => function ($event, $data) use ($app) {
            $data->add('$bix_cart', [
                'cart' => [],
                'countries' => App::module('system/intl')->getCountries()
            ]);
        }

    ],

    'render' => function ($widget) use ($app) {
		$cart = $app->module('bixie/cart');
		return $app['view']('bixie/cart/widget-cart.php', compact('cart', 'widget'));
    }

];
