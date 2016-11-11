<?php
/**
* @var \Pagekit\View\View $view
* @var Bixie\Cart\Model\Order $order
* @var Bixie\Cart\CartModule $cart
* @var Bixie\Cart\Cart\CartItem $cartItem
*/
$view->style('codemirror'); $view->script('cart-order-edit', 'bixie/cart:app/bundle/admin-order.js', ['bixie-pkframework', 'editor', 'uikit-lightbox']);
$delivery_price = $order->get('delivery_option.price', 0);
$payment_price = $order->get('payment_option.price', 0);

?>

<form id="cart-order" class="uk-form" name="form" v-validator="form" @submit.prevent="save | valid" v-cloak>

	<div class="uk-margin uk-flex uk-flex-space-between uk-flex-wrap" data-uk-margin>
		<div data-uk-margin>

			<h2 class="uk-margin-remove" v-if="order.id">{{ 'Edit order' | trans }} <em>{{
					order.title | trans }}</em></h2>

		</div>
		<div data-uk-margin>

			<a class="uk-button uk-margin-small-right" :href="$url.route('admin/cart')">{{ order.id ?
				'Close' :
				'Cancel' | trans }}</a>
			<button class="uk-button uk-button-primary" type="submit">{{ 'Save' | trans }}</button>

		</div>
	</div>

	<ul class="uk-tab" data-uk-tab="connect:'#order-content'">
		<li><a>{{ 'Overview' | trans }}</a></li>
		<li><a>{{ 'Items' | trans }}</a></li>
		<li v-for="section in sections"><a>{{ section.label | trans }}</a></li>
	</ul>

	<div class="uk-switcher uk-margin" id="order-content">
		<div>
			<div class="uk-margin">

				<div class="uk-grid uk-grid-small" data-uk-grid-margin="">
					<div class="uk-width-medium-1-2 uk-form-horizontal">
						<div class="uk-form-row">
							<label for="form-status" class="uk-form-label">{{ 'Order status' | trans }}</label>

							<div class="uk-form-controls">
								<select id="form-status" class="uk-form-width-medium" v-model="order.status">
									<option v-for="option in statusOptions" :value="option.value">{{ option.text }}</option>
								</select>
							</div>
						</div>

						<div class="uk-form-row">
							<label for="form-status" class="uk-form-label">{{ 'User' | trans }}</label>

							<div class="uk-form-controls">
								<select id="form-status" class="uk-form-width-medium" v-model="order.user_id">
									<option v-for="option in userOptions" :value="option.value">{{ option.text }}</option>
								</select>
							</div>
						</div>
					</div>
					<div class="uk-width-medium-1-2">
						<div class="uk-form-row">
							<label for="form-status" class="uk-form-label">{{ 'Order comment' | trans }}</label>

							<div class="uk-form-controls">
                        <textarea name="comment" id="form-comment" cols="30" rows="5" class="uk-width-1-1"
								  v-model="order.data.comment" placeholder="{{ 'Add a comment (visible for user)' | trans }}"></textarea>
							</div>
						</div>
					</div>
				</div>


				<div class="uk-grid uk-margin" data-uk-grid-match="{target: '.uk-panel'}" data-uk-grid-margin="">
					<div class="<?= ($order->user_id ? 'uk-width-medium-1-3' : 'uk-width-2-3') ?>">
						<div class="uk-panel uk-panel-box">

							<div
								class="uk-panel-badge uk-badge {{ order.status == 1 ? 'uk-badge-success' : 'uk-badge-warning' }}">
								{{ getStatusText(order) }}
							</div>

							<h3 class="uk-panel-title"><?= __('Order details') ?></h3>

							<dl class="uk-description-list uk-description-list-horizontal">
								<dt><?= __('Tansaction ID') ?></dt>
								<dd><?= $order->transaction_id ?></dd>
								<dt><?= __('External key') ?></dt>
								<dd><?= $order->ext_key ?></dd>
								<dt><?= __('Email address') ?></dt>
								<dd><?= $order->email ?></dd>
								<dt><?= __('Order date') ?></dt>
								<dd>{{ order.created | date 'medium' }}</dd>
								<?php if ($order->reference) : ?>
									<dt><?= __('Order reference') ?></dt>
									<dd><?= $order->reference ?></dd>
								<?php endif; ?>
							</dl>

							<?php if ($delivery_option = $order->getDeliverOption()) : ?>
							<dl class="uk-description-list uk-description-list-horizontal">
								<dt><?= __('Delivery option') ?></dt>
								<dd><?= $delivery_option->id ?></dd>
								<dt><?= __('Business days') ?></dt>
								<dd><?=  $delivery_option->business_days ?> days</dd>
								<dt><?= __('Price') ?></dt>
								<dd><?= $cart->formatMoney($delivery_option->price,  $delivery_option->currency) ?></dd>
								<dt><?= __('ETA date') ?></dt>
								<dd><?= $cart->formatDate($delivery_option->eta_date, 'mediumDate', $order->get('user_tz')); ?></dd>
							</dl>
							<?php endif; ?>

							<dl class="uk-description-list uk-description-list-horizontal">
								<?php if ($delivery_price || $payment_price) : ?>
									<dt><?= __('Ordered items') ?></dt>
									<dd class="uk-text-right"><?= $cart->formatMoney(($order->total_netto - $delivery_price - $payment_price), $order->currency) ?></dd>
									<?php if ($delivery_price) : ?>
										<dt><?= __('Delivery costs') ?></dt>
										<dd class="uk-text-right"><?= $cart->formatMoney($delivery_price, $order->currency) ?></dd>
									<?php endif; ?>
									<?php if ($payment_price) : ?>
										<dt><?= __('Payment costs') ?></dt>
										<dd class="uk-text-right"><?= $cart->formatMoney($payment_price, $order->currency) ?></dd>
									<?php endif; ?>
								<?php endif; ?>
								<dt><?= __('Amount excl. VAT') ?></dt>
								<dd class="uk-text-right"><?= $cart->formatMoney($order->total_netto, $order->currency) ?></dd>
								<dt><?= __('VAT amount') ?></dt>
								<dd class="uk-text-right"><?= $cart->formatMoney($order->total_bruto - $order->total_netto, $order->currency) ?></dd>
								<dt><?= __('Amount incl. VAT') ?></dt>
								<dd class="uk-text-large uk-text-right"><?= $cart->formatMoney($order->total_bruto, $order->currency) ?></dd>
							</dl>
						</div>
					</div>
					<div class="uk-width-medium-1-3">
						<div class="uk-panel uk-panel-box">
							<?= $view->render('bixie/cart/templates/payment_details.php', compact('cart', 'order')) ?>
						</div>
					</div>
					<?php if ($order->user_id) : ?>
					<div class="uk-width-medium-1-3">
						<div class="uk-panel uk-panel-box">

							<div class="uk-panel-badge"><i class="uk-icon-user uk-icon-medium"></i></div>

							<dl class="uk-description-list">
								<dt><?= __('Username') ?></dt>
								<dd><?= $order->user->username ?></dd>
								<dt><?= __('Name') ?></dt>
								<dd><?= $order->user->name ?></dd>
								<dt><?= __('Email address') ?></dt>
								<dd><?= $order->user->email ?></dd>
							</dl>

							<a href="<?= $view->url('@user/edit', ['id' => $order->user_id]) ?>" class="uk-button">
								<?= __('To user profile') ?>
							</a>
						</div>
					</div>
					<?php endif; ?>
				</div>


				<div class="uk-margin">
					<?= $view->render('bixie/cart/templates/order_items.php', compact('cart', 'order')) ?>
				</div>

				<div class="uk-grid uk-grid-medium" data-uk-grid-match="{target: '.uk-panel'}" data-uk-grid-margin="">
					<div class="uk-width-medium-1-2">
						<div class="uk-panel uk-panel-box">

							<h3 class="uk-panel-title"><?= __('Delivery address') ?></h3>

							<?= $view->render('bixie/cart/templates/address.php', [
								'cart' => $cart, 'order' => $order, 'address' => $order->get('delivery_address')
							]) ?>

						</div>

					</div>
					<div class="uk-width-medium-1-2">
						<div class="uk-panel uk-panel-box">

							<h3 class="uk-panel-title"><?= __('Billing address') ?></h3>

							<?= $view->render('bixie/cart/templates/address.php', [
								'cart' => $cart, 'order' => $order, 'address' => $order->get('billing_address')
							]) ?>

						</div>

					</div>
				</div>


			</div>
		</div>
		<div>
			<div class="uk-margin">
				<ul class="uk-list uk-list-line">
					<?php foreach ($order->cartItems as $cartItem) :
						$prices = $cartItem->calcPrices($order);

						?>

						<li>
							<div class="uk-grid uk-grid-small" data-uk-grid-margin="">
								<div class="uk-width-medium-3-4">
									<h3><a class="uk-icon-external-link uk-icon-hover uk-margin-small-right"
										   href="<?= $cartItem->item_url ?>" target="_blank"></a>
										<?= $cartItem->title ?></h3>

									<dl class="uk-description-list uk-description-list-horizontal">
										<dt><?= __('Purchase key') ?></dt>
										<dd><em><?= $cartItem->purchaseKey($order) ?></em></dd>
									</dl>
									<?= $cartItem->getTemplate('bixie.cart.admin.order') ?>
								</div>
								<div class="uk-width-medium-1-4">
									<dl class="uk-description-list uk-description-list-horizontal">
										<dt><?= __('Price excl. VAT') ?></dt>
										<dd class="uk-text-right"><?= $cart->formatMoney($prices['netto'], $order->currency) ?></dd>
										<dt><?= __('VAT') ?></dt>
										<dd class="uk-text-right"><?= $cart->formatMoney($prices['vat'], $order->currency) ?></dd>
										<dt><?= __('Price incl. VAT') ?></dt>
										<dd class="uk-text-right"><?= $cart->formatMoney($prices['bruto'], $order->currency) ?></dd>
									</dl>
								</div>
							</div>
						</li>

					<?php endforeach; ?>
				</ul>

			</div>
		</div>
		<div v-for="section in sections">
			<component :is="section.name" :order.sync="order" :config="config" :form="form"></component>
		</div>
	</div>

</form>

