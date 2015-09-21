<?php

return [

    'install' => function ($app) {

		$util = $app['db']->getUtility();

		if ($util->tableExists('@cart_order') === false) {
			$util->createTable('@cart_order_file', function ($table) {
				$table->addColumn('id', 'integer', ['unsigned' => true, 'length' => 10, 'autoincrement' => true]);
				$table->addColumn('status', 'smallint');
				$table->addColumn('created', 'datetime');
				$table->addColumn('data', 'json_array', ['notnull' => false]);
				$table->setPrimaryKey(['id']);
			});
		}

    },

    'uninstall' => function ($app) {

        $util = $app['db']->getUtility();

        if ($util->tableExists('@cart_order')) {
            $util->dropTable('@cart_order');
        }

		// remove the config
		$app['config']->remove('bixie/cart');

	}

];