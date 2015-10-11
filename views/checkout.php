<?php
/**
 * @var $view
 * @var Bixie\Cart\CartModule $cart
 */
$view->script('bixie-cart');

?>

<section id="bixie-checkout">

	<h1><?= __('Checkout') ?></h1>

	<div class="uk-margin">
		<cartlist is-checkout="1"></cartlist>
	</div>
	<form name="form" v-on="submit: checkoutSubmit">

		<checkout v-ref="checkout"></checkout>

	</form>
</section>
