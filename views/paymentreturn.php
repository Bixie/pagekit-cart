<?php
/**
 * @var $view
 * @var string $title
 * @var string $content
 * @var Bixie\Cart\Model\Order $order
 * @var Bixie\Cart\CartModule $cart
 */

?>

<div id="bixie-checkout">

	<h1><?= __($title) ?></h1>

	<?= $content ?>

	<div class="uk-panel uk-panel-box uk-margin">

		<div class="uk-panel-badge uk-badge <?= ($order->status == 1 ? 'uk-badge-success' : 'uk-badge-warning')  ?>">
			<?= $order->getStatusText() ?></div>

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

