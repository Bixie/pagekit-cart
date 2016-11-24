<?php
/**
 * @var $view
 * @var Bixie\Cart\Model\Order $order
 * @var Bixie\Cart\CartModule $cart
 * @var Bixie\Cart\Calculation\ItemCalculator[] $orderItems
 */

?>
<div class="uk-panel uk-panel-box">

	<h3 class="uk-panel-title"><?= __('Purchased items') ?></h3>

	<ul class="uk-list uk-list-line">
		<?php foreach ($orderItems as $orderItem) :
			$cartItem = $orderItem->getItem();
			?>
			<li>
				<div class="uk-grid uk-margin-top" data-uk-grid-margin="">
					<div class="uk-width-medium-2-3">
						<h3><a href="<?= $cartItem->item_url ?>"><?= $cartItem->title ?></a></h3>
					</div>
					<div class="uk-width-medium-1-3">
						<p>
						<?php if ($propValues = $cartItem->getPropValues()) : ?>
							<?php foreach ($propValues->texts() as $label => $value) : ?>
								<strong><?= $label ?></strong>: <?= $value ?>,&nbsp;
							<?php endforeach; ?>
						<?php endif; ?>
							<strong><?= __('Quantity') ?></strong>: <?= $cartItem->quantity ?>
						</p>
						<dl class="uk-description-list uk-description-list-horizontal">
							<?php if ($cart->config('vat_view') == 'incl') : ?>
								<dt><?= __('Price incl. VAT') ?></dt>
								<dd class="uk-text-right"><?= $cart->formatMoney($orderItem->total_bruto, $order->currency) ?></dd>
							<?php else : ?>
								<dt><?= __('Price excl. VAT') ?></dt>
								<dd class="uk-text-right"><?= $cart->formatMoney($orderItem->total_netto, $order->currency) ?></dd>
							<?php endif; ?>
						</dl>
					</div>
				</div>
				<?= $cartItem->getTemplate('bixie.cart.order_item') ?>
			</li>

		<?php endforeach; ?>
	</ul>

</div>

