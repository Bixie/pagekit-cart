<?php
/**
 * @var $view
 * @var Bixie\Cart\CartModule $cart
 */
$view->script('bixie-cart');

?>

<div id="bixie-checkout">

	<h1><?= __('Thank you') ?></h1>

	<div class="uk-margin">
		<?= $transaction_id ?>
	</div>

</div>
<pre>{{ $data | json}}</pre>
