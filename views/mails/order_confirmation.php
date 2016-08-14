
<?php
/**
 * @var $view
 * @var Bixie\Cart\Model\Order $order
 * @var Bixie\Cart\CartModule $cart
 */

?>

<div style="margin: 20px 0">

	<h3><?= __('Order details') ?></h3>

	<p><?= $order->getStatusText() ?></p>


	<dl>
		<dt><?= __('Tansaction ID') ?></dt>
		<dd><?= $order->transaction_id ?></dd>
		<dt><?= __('Order date') ?></dt>
		<dd><?= $cart->formatDate($order->created, 'medium', $order->get('user_tz')) ?></dd>
		<dt><?= __('Amount excl. VAT') ?></dt>
		<dd><?= $cart->formatMoney($order->total_netto, $order->currency) ?></dd>
		<dt><?= __('VAT amount') ?></dt>
		<dd><?= $cart->formatMoney($order->total_bruto - $order->total_netto, $order->currency) ?></dd>
		<dt><?= __('Amount incl. VAT') ?></dt>
		<dd><?= $cart->formatMoney($order->total_bruto, $order->currency) ?></dd>
	</dl>
</div>

<div style="margin: 20px 0">
	<?= $view->render('bixie/cart/templates/order_items.php', compact('cart', 'order')) ?>
</div>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr>
		<td width="50%" style="font-size: 16px; line-height: 24px; font-family: Helvetica, Arial, sans-serif; color: #666;">
			<?= $view->render('bixie/cart/templates/address.php', compact('cart', 'order')) ?>
		</td>
		<td width="50%" style="font-size: 16px; line-height: 24px; font-family: Helvetica, Arial, sans-serif; color: #666;">
			<?= $view->render('bixie/cart/templates/payment_details.php', compact('cart', 'order')) ?>
		</td>
	</tr>
</table>
