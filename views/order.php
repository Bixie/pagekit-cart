<?php
/**
 * @var $view
 * @var Bixie\Invoicemaker\Model\Invoice[] $invoices
 * @var Bixie\Cart\Cart\CartItem[] $cartItems
 * @var Bixie\Cart\Model\Order $order
 * @var Bixie\Cart\CartModule $cart
 */
$delivery_price = $order->get('delivery_option.price', 0);
$payment_price = $order->get('payment_option.price', 0);

?>

<section id="bixie-order">

	<h1><?= __('Order details') ?></h1>

	<div class="uk-panel uk-panel-box uk-margin">

		<div class="uk-panel-badge uk-badge <?= ($order->status == 1 ? 'uk-badge-success' : 'uk-badge-warning')  ?>">
			<?= $order->getStatusText() ?></div>

		<dl class="uk-description-list uk-description-list-horizontal">
			<dt><?= __('Tansaction ID') ?></dt>
			<dd><?= $order->transaction_id ?></dd>
			<dt><?= __('Order date') ?></dt>
			<dd><?= $cart->formatDate($order->created, 'medium', $order->get('user_tz')) ?></dd>
			<?php if ($delivery_option = $order->getDeliveryOption()) : ?>
				<dt><?= __('Estimated delivery date') ?></dt>
				<dd><?= $cart->formatDate($delivery_option->eta_date, 'mediumDate', $order->get('user_tz')); ?></dd>
			<?php endif; ?>

			<?php if ($order->reference) : ?>
				<dt><?= __('Order reference') ?></dt>
				<dd><?= $order->reference ?></dd>
			<?php endif; ?>
		</dl>

		<h3 class="uk-panel-title"><?= __('Order details') ?></h3>

		<div class="uk-grid" data-uk-grid-margin>
			<div class="uk-width-medium-2-3">
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
				<dl class="uk-description-list uk-description-list-horizontal">
					<dt><?= __('Order comment') ?></dt>
					<dd><?= $order->get('comment') ? nl2br($order->get('comment')) : '-' ?></dd>
				</dl>

				<?php if (count($invoices)) : ?>
					<dl class="uk-description-list uk-description-list-horizontal">
						<dt><?= __('Invoice') ?></dt>
						<?php foreach ($invoices as $invoice) : ?>
							<dd>
								<a href="<?= $invoice->getPdfUrl() ?>" download>
									<i class="uk-icon-download uk-margin-small-right"></i>
									<?= $invoice->invoice_number ?>
								</a>
							</dd>
						<?php endforeach; ?>
					</dl>
				<?php endif; ?>
			</div>
			<div class="uk-width-medium-1-3">
				<?= $view->render('bixie/cart/templates/payment_details.php', compact('cart', 'order')) ?>
			</div>
		</div>
	</div>

	<div class="uk-margin">
		<?= $view->render('bixie/cart/templates/order_items.php', compact('cart', 'order', 'cartItems')) ?>
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

</section>
