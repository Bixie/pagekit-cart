<?php
/**
 * @var $view
 * @var Pagekit\User\Model\User $user
 * @var Bixie\Cart\CartModule $cart
 */
$view->script('bixie-orders', 'bixie/cart:app/bundle/orders.js', ['vue', 'uikit-pagination', 'uikit-form-select']);
?>

<section id="bixie-orders">

	<h1><?= __('Your orders') ?></h1>

	<?php if ($user->isAnonymous()) : ?>

		<p><?= __('You are not logged in. If you have an account, please log in to see your orders.') ?></p>

		<p><?= __('If you don\'t have an account yet, click "Find your order" to search for your order.') ?></p>

		<div class="uk-margin uk-flex uk-flex-space-around" data-uk-margin="">
			<a class="uk-button" href="<?= $view->url('@user/login')?>">
				<i class="uk-icon-key uk-margin-small-right"></i><?= __('Login') ?></a>

			<a class="uk-button uk-button-primary uk-margin-small-left" href="<?= $view->url('@cart/orders/findorder')?>">
				<i class="uk-icon-search uk-margin-small-right"></i><?= __('Find your order') ?></a>

		</div>

	<?php else: ?>

		<div class="uk-margin" v-cloak>

			<form class="uk-search">
			    <input class="uk-search-field" style="min-width: 400px" type="search" v-model="config.filter.search">
			</form>

		</div>

		<div class="uk-grid uk-grid-small uk-text-bold">
			<div class="uk-width-medium-2-6" v-order="transaction_id: config.filter.order">{{ 'Transaction ID' | trans }}</div>
			<div class="uk-width-1-2 uk-width-medium-1-6" v-order="created: config.filter.order">{{ 'Date' | trans }}</div>
			<div class="uk-width-1-2 uk-width-medium-1-6 uk-text-center">
				<input-filter title="{{ 'Status' | trans }}" value="{{@ config.filter.status}}" options="{{ statusOptions }}"></input-filter>
			</div>
			<div class="uk-width-medium-2-6 uk-hidden-small">{{ 'Items' | trans }}</div>
		</div>

		<ul class="uk-list uk-margin-small-top uk-list-striped">
			<li v-repeat="order: orders">
				<div class="uk-grid uk-grid-small">
					<div class="uk-width-medium-2-6">
						<a v-attr="href: $url.route(config.edit_url, { transaction_id: order.transaction_id })">{{ order.transaction_id }}</a>
					</div>
					<div class="uk-width-1-3 uk-width-medium-1-6">{{ order.created | date }}</div>
					<div class="uk-width-1-3 uk-width-medium-1-6 uk-text-center">
				<span title="{{ getStatusText(order) }}" class="uk-icon-circle" v-class="
                                uk-text-danger: order.status == 0,
                                uk-text-success: order.status == 1"></span>
					</div>
					<div class="uk-width-medium-2-6">{{ cartItems(order) }}</div>
				</div>
			</li>
		</ul>

		<p v-show="!orders" class="uk-text-center"><i class="uk-icon-circle-o-notch uk-icon-spin"></i></p>

		<h3 class="uk-h1 uk-text-muted uk-text-center"
			v-show="orders && !orders.length">{{ 'No orders found.' | trans }}</h3>

		<v-pagination page="{{@ config.page }}" pages="{{ pages }}" v-show="pages > 1"></v-pagination>


	<?php endif; ?>

</section>
