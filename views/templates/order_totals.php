<?php
/**
 * @var $view
 * @var Bixie\Cart\Calculation\OrderCalculator $calculator
 * @var Bixie\Cart\CartModule $cart
 */

?>
<dl class="uk-description-list uk-description-list-horizontal">
	<?php if ($calculator->delivery_netto || $calculator->payment_netto) : ?>
		<dt><?= __('Ordered items') ?></dt>
		<dd class="uk-text-right">
			<span class="uk-margin-large-right"><?= $cart->formatMoney($calculator->items_netto, $calculator->currency) ?></span>
		</dd>
		<?php if ($calculator->delivery_netto) : ?>
			<dt><?= __('Delivery costs') ?></dt>
			<dd class="uk-text-right">
				<span class="uk-margin-large-right"><?= $cart->formatMoney($calculator->delivery_netto, $calculator->currency) ?></span>
			</dd>
		<?php endif; ?>
		<?php if ($calculator->payment_netto) : ?>
			<dt><?= __('Payment costs') ?></dt>
			<dd class="uk-text-right">
				<span class="uk-margin-large-right"><?= $cart->formatMoney($calculator->payment_netto, $calculator->currency) ?></span>
			</dd>
		<?php endif; ?>
	<?php endif; ?>
	<dt><?= __('Amount excl. VAT') ?></dt>
	<dd class="uk-text-right">
		<span style="border-top: 1px solid #ccc;"><?= $cart->formatMoney($calculator->total_netto, $calculator->currency) ?></span>
	</dd>
	<dt><?= __('VAT amount') ?></dt>
	<dd class="uk-text-right">
		<span class="uk-margin-large-right"><?= $cart->formatMoney($calculator->total_vat, $calculator->currency) ?></span>
	</dd>
	<dt><?= __('Amount incl. VAT') ?></dt>
	<dd class="uk-text-large uk-text-right">
		<span style="border-top: 1px solid #ccc;"><?= $cart->formatMoney($calculator->total_bruto, $calculator->currency) ?></span>
	</dd>
</dl>
