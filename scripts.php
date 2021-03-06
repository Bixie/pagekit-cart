<?php

return [

    'install' => function ($app) {

		$util = $app['db']->getUtility();

		if ($util->tableExists('@cart_order') === false) {
			$util->createTable('@cart_order', function ($table) {
                $table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
                $table->addColumn('user_id', 'integer', ['unsigned' => true, 'length' => 10, 'notnull' => false]);
                $table->addColumn('status', 'smallint');
                $table->addColumn('ext_key', 'string', ['length' => 255, 'notnull' => false]);
                $table->addColumn('reference', 'string', ['length' => 255, 'notnull' => false]);
                $table->addColumn('created', 'datetime');
                $table->addColumn('email', 'string', ['length' => 255]);
                $table->addColumn('cartItemsData', 'json_array');
                $table->addColumn('payment', 'json_array', ['notnull' => false]);
                $table->addColumn('total_netto', 'decimal', ['precision' => 9, 'scale' => 2]);
                $table->addColumn('total_bruto', 'decimal', ['precision' => 9, 'scale' => 2]);
                $table->addColumn('currency', 'string', ['length' => 16]);
                $table->addColumn('transaction_id', 'string', ['length' => 255, 'notnull' => false]);
                $table->addColumn('data', 'json_array', ['notnull' => false]);
                $table->setPrimaryKey(['id']);
                $table->addIndex(['user_id'], 'CART_ORDER_USER_ID');
                $table->addIndex(['status'], 'CART_ORDER_STATUS');
                $table->addIndex(['ext_key'], 'CART_ORDER_EXT_KEY');
                $table->addIndex(['email'], 'CART_ORDER_EMAIL');
                $table->addIndex(['total_netto'], 'CART_ORDER_TOTAL_NETTO');
                $table->addIndex(['total_bruto'], 'CART_ORDER_TOTAL_BRUTO');
                $table->addIndex(['transaction_id'], 'CART_ORDER_TRANSACTION_ID');
			});
		}

		if ($util->tableExists('@cart_product') === false) {
			$util->createTable('@cart_product', function ($table) {
				$table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
				$table->addColumn('active', 'smallint');
				$table->addColumn('item_model', 'string', ['length' => 128]);
				$table->addColumn('item_id', 'smallint');
				$table->addColumn('price', 'decimal', ['precision' => 9, 'scale' => 2]);
				$table->addColumn('vat', 'string', ['length' => 64, 'notnull' => false]);
				$table->addColumn('currency', 'string', ['length' => 16]);

				$table->addColumn('data', 'json_array', ['notnull' => false]);
				$table->setPrimaryKey(['id']);
				$table->addIndex(['active'], 'CART_PRODUCT_ACTIVE');
				$table->addIndex(['item_id'], 'CART_PRODUCT_ITEM_ID');
				$table->addIndex(['price'], 'CART_PRODUCT_PRICE');
			});
		}

    },

    'uninstall' => function ($app) {

        $util = $app['db']->getUtility();

        if ($util->tableExists('@cart_order')) {
            $util->dropTable('@cart_order');
        }

        if ($util->tableExists('@cart_product')) {
            $util->dropTable('@cart_product');
        }

		// remove the config
		$app['config']->remove('bixie/cart');

	}

];