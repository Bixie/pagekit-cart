<?php
/**
 * @var $view
 * @var Bixie\Cart\Model\Order $order
 * @var Bixie\Cart\CartModule $cart
 * @var Bixie\Cart\Cart\CartItem $cartItem
 */

?>
<div class="uk-panel uk-panel-box">

	<h3 class="uk-panel-title"><?= __('Purchased items') ?></h3>

	<ul class="uk-list uk-list-line">
		<?php foreach ($order->cartItems as $cartItem) :
			$prices = $cartItem->calcPrices($order);
			?>
			<li>
				<div class="uk-grid" data-uk-grid-margin="">
					<div class="uk-width-medium-2-3">
						<h3><a href="<?= $cartItem->item_url ?>"><?= $cartItem->title ?></a></h3>
					</div>
					<div class="uk-width-medium-1-3">
						<dl class="uk-description-list uk-description-list-horizontal">
							<dt><?= __('Price excl. VAT') ?></dt>
							<dd class="uk-text-right"><?= $cart->formatMoney($prices['netto'], $order->currency) ?></dd>
						</dl>
					</div>
				</div>
				<?= $cartItem->getTemplate('bixie.cart.order_item') ?>
			</li>

		<?php endforeach; ?>
	</ul>

</div>

