<?php
/**
 * @var $view
 * @var string $title
 * @var ResponseInterface $response
 * @var Bixie\Cart\Model\Order $order
 * @var Bixie\Cart\CartModule $cart
 */

?>

<div id="bixie-paymentredirect">

	<h1><?= __($title) ?></h1>

	<div class="uk-panel uk-panel-box uk-margin">

		<div class="uk-alert"><?= __('Your payment requires redirect to an off-site payment page.') ?></div>

		<?php if ($response->getRedirectMethod() == 'GET') : ?>

			<p><a href="<?= $response->getRedirectUrl() ?>" class="uk-button uk-button-success">
					<i class="uk-icon-arrow-right uk-margin-small-right"></i><?= __('Redirect Now') ?></a></p>

		<?php elseif ($response->getRedirectMethod() == 'POST'): ?>
			<form method="POST" action="<?= $response->getRedirectUrl() ?>">
				<?php foreach ($response->getRedirectData() as $key => $value) : ?>

					<input type="hidden" name="<?= $key ?>" value="<?= $value ?>" />

				<?php endforeach; ?>

				<p>
					<button class="uk-button uk-button-success">
						<i class="uk-icon-arrow-right uk-margin-small-right"></i><?= __('Redirect Now') ?></button>
				</p>
			</form>
		<?php endif; ?>

	</div>
	<div class="uk-panel uk-panel-box uk-margin">

		<div class="uk-panel-badge uk-badge <?= ($order->status == 1 ? 'uk-badge-success' : 'uk-badge-warning')  ?>">
			<?= $order->getStatusText() ?></div>

		<dl class="uk-description-list uk-description-list-horizontal">
			<dt><?= __('Tansaction ID') ?></dt>
			<dd><?= $order->transaction_id ?></dd>
			<dt><?= __('Order date') ?></dt>
			<dd><?= $cart->formatDate($order->created, 'medium', $order->get('user_tz')) ?></dd>
		</dl>
		<dl class="uk-description-list uk-description-list-horizontal">
			<dt><?= __('Amount excl. VAT') ?></dt>
			<dd class="uk-text-right"><?= $cart->formatMoney($order->total_netto, $order->currency) ?></dd>
			<dt><?= __('VAT amount') ?></dt>
			<dd class="uk-text-right"><?= $cart->formatMoney($order->total_bruto - $order->total_netto, $order->currency) ?></dd>
			<dt><?= __('Amount incl. VAT') ?></dt>
			<dd class="uk-text-large uk-text-right"><?= $cart->formatMoney($order->total_bruto, $order->currency) ?></dd>
		</dl>
	</div>


</div>

