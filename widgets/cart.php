<?php


return [

    'name' => 'bixie/widget-cart',

    'label' => 'Cart',

    'events' => [

        'view.scripts' => function ($event, $scripts) use ($app) {
            $scripts->register('widget-cart', 'bixie/cart:app/bundle/widget-cart.js', ['~widgets']);
        }

    ],

    'render' => function ($widget) use ($app) {
		$cart = $app->module('bixie/cart');

//		$app->on('view.data', function ($event, $data) use ($form, $formmaker) {
//			$data->add('$formmaker', [
//				'config' => $formmaker->publicConfig(),
//				'formitem' => $form,
//				'fields' => array_values($form->fields)
//			]);
//		});
//
//		$app->on('view.styles', function ($event, $styles) use ($app, $formmaker) {
//			$formmaker->typeStyles($styles);
//		});

		return $app['view']('bixie/cart/widget-cart.php', compact('cart'));
    }

];
