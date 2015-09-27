<?php
/**
 * @var $view
 * @var Bixie\Cart\Model\Order $order
 * @var Bixie\Cart\CartModule $cart
<pre><?php print_r($order->payment); ?></pre>
 */

?>
<div class="uk-panel uk-panel-box">

	<h3 class="uk-panel-title"><?= __('Payment details') ?></h3>

	<dl class="uk-description-list">
		<dt><?= __('Payment status') ?></dt>
		<?php if (!empty($order->payment['paid'])) : ?>
			<dd class="uk-text-success"><?= __('Order paid') ?></dd>
		<?php else: ?>
			<dd class="uk-text-danger"><?= __('Order not paid') ?></dd>
		<?php endif; ?>
		<dt><?= __('Payment method') ?></dt>
		<dd><?= $order->get('payment.method') ?></dd>
		<dt><?= __('Payment ID') ?></dt>
		<dd><?= $order->payment['id'] ?></dd>
		<dt><?= __('Payment description') ?></dt>
		<dd><?= $order->payment['description'] ?></dd>
		<dt><?= __('Payment date') ?></dt>
		<dd><?= $cart->formatDate($order->payment['created']); ?></dd>
		<dt><?= __('Payment amount') ?></dt>
		<dd><?=  $cart->formatMoney(($order->payment['amount']/100), strtoupper($order->payment['currency'])) ?></dd>

		<?php if (isset($order->payment['source'])) : ?>
			<?php if (isset($order->payment['source']['brand'])) : ?>
				<dt><?= __('Credit card') ?></dt>
				<dd><i class="uk-icon-cc-<?= strtolower($order->payment['source']['brand']) ?> uk-icon-justify uk-margin-small-right"></i>
					<?= $order->payment['source']['brand'] ?></dd>
			<?php endif; ?>
			<?php if (isset($order->payment['source']['last4'])) : ?>
				<dt><?= __('Credit card info') ?></dt>
				<dd>xxxxxxxxx<?= $order->payment['source']['last4'] ?>, <?= $order->payment['source']['exp_month'] ?>-<?= $order->payment['source']['exp_year'] ?></dd>
			<?php endif; ?>
		<?php endif; ?>

		<?php if (isset($order->payment['failure_message'])) : ?>
			<dt><?= __('Failure message') ?></dt>
			<dd class="uk-text-danger"><?= $order->payment['failure_message'] ?></dd>
		<?php endif; ?>
	</dl>

</div>

