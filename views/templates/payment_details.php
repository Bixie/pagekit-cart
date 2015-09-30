<?php
/**
 * @var $view
 * @var Bixie\Cart\Model\Order $order
 * @var Bixie\Cart\CartModule $cart
<pre><?php print_r($order->payment); ?></pre>
 */
use Pagekit\Application as App;

?>
<div class="uk-panel uk-panel-box">

	<h3 class="uk-panel-title"><?= __('Payment details') ?></h3>

	<dl class="uk-description-list">
		<dt><?= __('Payment status') ?></dt>
		<?php if ($order->get('payment.success')) : ?>
			<dd class="uk-text-success"><?= __('Order paid') ?></dd>
		<?php else : ?>
			<dd class="uk-text-danger"><?= __('Order not paid') ?></dd>
		<?php endif; ?>
		<?php if ($order->issetPayment('PAYMENTINFO_0_PAYMENTSTATUS')) : ?>
		<dt><?= __('Payment status') ?></dt>
			<?php if ($order->getPayment('PAYMENTINFO_0_PAYMENTSTATUS') == 'Completed') : ?>
				<dd class="uk-text-success"><?= __('Order paid') ?></dd>
			<?php else : ?>
				<dd class="uk-text-danger"><?= $order->getPayment('PAYMENTINFO_0_PAYMENTSTATUS') ?></dd>
			<?php endif; ?>
		<?php endif; ?>
		<dt><?= __('Payment method') ?></dt>
		<dd><?= $order->get('payment.method_name') ?></dd>
		<?php if ($order->get('payment.message')) : ?>
		<dt><?= __('Payment message') ?></dt>
		<dd><?= $order->get('payment.message') ?></dd>
		<?php endif; ?>

		<?php if ($order->issetPayment('id')) : ?>
			<dt><?= __('Payment ID') ?></dt>
			<dd><?= $order->getPayment('id') ?></dd>
		<?php endif; ?>
		<?php if ($order->issetPayment('PAYMENTINFO_0_TRANSACTIONID')) : ?>
			<dt><?= __('Payment ID') ?></dt>
			<dd><?= $order->getPayment('PAYMENTINFO_0_TRANSACTIONID') ?></dd>
		<?php endif; ?>

		<?php if ($order->issetPayment('description')) : ?>
			<dt><?= __('Payment description') ?></dt>
			<dd><?= $order->getPayment('description') ?></dd>
		<?php endif; ?>

		<?php if ($order->issetPayment('created')) : ?>
			<dt><?= __('Payment date') ?></dt>
			<dd><?= $cart->formatDate($order->getPayment('created'), 'medium', $order->get('user_tz')); ?></dd>
		<?php endif; ?>
		<?php if ($order->issetPayment('createdDatetime')) : ?>
			<dt><?= __('Payment date') ?></dt>
			<dd><?= $cart->formatDate($order->getPayment('createdDatetime'), 'medium', $order->get('user_tz')); ?></dd>
		<?php endif; ?>
		<?php if ($order->issetPayment('PAYMENTINFO_0_ORDERTIME')) : ?>
			<dt><?= __('Payment date') ?></dt>
			<dd><?= $cart->formatDate($order->getPayment('PAYMENTINFO_0_ORDERTIME'), 'medium', $order->get('user_tz')); ?></dd>
		<?php endif; ?>

		<?php if ($order->issetPayment('amount') && $order->issetPayment('currency')) : ?>
			<dt><?= __('Payment amount') ?></dt>
			<dd><?=  $cart->formatMoney(($order->getPayment('amount')/100), strtoupper($order->getPayment('currency'))) ?></dd>
		<?php endif; ?>
		<?php if ($order->issetPayment('amount') && $order->issetPayment('locale')) : ?>
			<dt><?= __('Payment amount') ?></dt>
			<dd><?=  $cart->formatMoney(($order->getPayment('amount')), 'EUR') ?></dd>
		<?php endif; ?>
		<?php if ($order->issetPayment('PAYMENTINFO_0_AMT') && $order->issetPayment('PAYMENTINFO_0_CURRENCYCODE')) : ?>
			<dt><?= __('Payment amount') ?></dt>
			<dd><?=  $cart->formatMoney($order->getPayment('PAYMENTINFO_0_AMT'), $order->getPayment('PAYMENTINFO_0_CURRENCYCODE')) ?></dd>
		<?php endif; ?>

		<?php if (App::user()->isAdministrator()) : ?>
			<?php if ($order->issetPayment('PAYMENTINFO_0_FEEAMT')) : ?>
				<dt><?= __('Fee amount') ?></dt>
				<dd><?=  $cart->formatMoney($order->getPayment('PAYMENTINFO_0_FEEAMT'), strtoupper($order->getPayment('PAYMENTINFO_0_CURRENCYCODE'))) ?></dd>
			<?php endif; ?>
			<?php if ($order->issetPayment('PAYMENTINFO_0_PENDINGREASON')) : ?>
				<dt><?= __('Pending reason') ?></dt>
				<dd><?=  $order->getPayment('PAYMENTINFO_0_PENDINGREASON') ?></dd>
			<?php endif; ?>
			<?php if ($order->issetPayment('PAYMENTINFO_0_REASONCODE')) : ?>
				<dt><?= __('Pending reason') ?></dt>
				<dd><?=  $order->getPayment('PAYMENTINFO_0_REASONCODE') ?></dd>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ($order->issetPayment('source')) : ?>
			<?php if ($order->issetPayment('source.brand')) : ?>
				<dt><?= __('Credit card') ?></dt>
				<dd><i class="uk-icon-cc-<?= strtolower($order->getPayment('source.brand')) ?> uk-icon-justify uk-margin-small-right"></i>
					<?= $order->getPayment('source.brand') ?></dd>
			<?php endif; ?>
			<?php if ($order->issetPayment('source.last4')) : ?>
				<dt><?= __('Credit card info') ?></dt>
				<dd>xxxx xxxx xxxx <?= $order->getPayment('source.last4') ?>, <?= $order->getPayment('source.exp_month') ?>-<?= $order->getPayment('source.exp_year') ?></dd>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ($order->issetPayment('failure_message') && $order->getPayment('failure_message')) : ?>
			<dt><?= __('Failure message') ?></dt>
			<dd class="uk-text-danger"><?= $order->getPayment('failure_message') ?></dd>
		<?php endif; ?>
	</dl>

</div>

