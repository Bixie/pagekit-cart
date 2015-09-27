<?php
/**
* @var $view
* @var Bixie\Cart\Model\Order $order
* @var Bixie\Cart\CartModule $cart
*/
$view->style('codemirror'); $view->script('admin-order', 'bixie/cart:app/bundle/admin-order.js', ['vue', 'editor']); ?>

<form id="order-edit" class="uk-form" name="form" v-on="submit: save | valid" v-cloak>

	<div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
		<div data-uk-margin>

			<h2 class="uk-margin-remove" v-if="order.id">{{ 'Edit order' | trans }} <em>{{
					order.title | trans }}</em></h2>


		</div>
		<div data-uk-margin>

			<a class="uk-button uk-margin-small-right" v-attr="href: $url.route('admin/cart/orders')">{{ order.id ?
				'Close' :
				'Cancel' | trans }}</a>
			<button class="uk-button uk-button-primary" type="submit">{{ 'Save' | trans }}</button>

		</div>
	</div>

	<ul class="uk-tab" v-el="tab">
		<li><a>{{ 'General' | trans }}</a></li>
	</ul>

	<div class="uk-switcher uk-margin" v-el="content">
		<div>
			<div class="uk-margin">
				<div class="uk-form-horizontal">
					<div class="uk-form-row">
						<label for="form-status" class="uk-form-label">{{ 'Order status' | trans }}</label>
						<div class="uk-form-controls">
							<select id="form-status" class="uk-form-width-medium" v-model="order.status" options="statusOptions"></select>
						</div>
					</div>
				</div>

				<div class="uk-panel uk-panel-box uk-margin">

					<div class="uk-panel-badge uk-badge {{ order.status == 1 ? 'uk-badge-success' : 'uk-badge-warning' }}">
						{{ getStatusText(order) }}
					</div>

					<h3 class="uk-panel-title"><?= __('Order details') ?></h3>

					<dl class="uk-description-list uk-description-list-horizontal">
						<dt><?= __('Tansaction ID') ?></dt>
						<dd><?= $order->transaction_id ?></dd>
						<dt><?= __('Order date') ?></dt>
						<dd><?= $cart->formatDate($order->created) ?></dd>
						<dt><?= __('Amount excl. VAT') ?></dt>
						<dd class="uk-text-right"><?= $cart->formatMoney($order->total_netto, $order->currency) ?></dd>
						<dt><?= __('VAT amount') ?></dt>
						<dd class="uk-text-right"><?= $cart->formatMoney($order->total_bruto - $order->total_netto, $order->currency) ?></dd>
						<dt><?= __('Amount incl. VAT') ?></dt>
						<dd class="uk-text-large uk-text-right"><?= $cart->formatMoney($order->total_bruto, $order->currency) ?></dd>
					</dl>
				</div>

				<div class="uk-margin">
					<?= $view->render('bixie/cart/templates/order_items.php', compact('cart', 'order')) ?>
				</div>

				<div class="uk-grid uk-grid-medium" data-uk-grid-match="{target: '.uk-panel'}" data-uk-grid-margin="">
					<div class="uk-width-medium-1-2">
						<?= $view->render('bixie/cart/templates/billing_address.php', compact('cart', 'order')) ?>
					</div>
					<div class="uk-width-medium-1-2">
						<?= $view->render('bixie/cart/templates/payment_details.php', compact('cart', 'order')) ?>
					</div>
				</div>


			</div>
		</div>
	</div>

</form>

