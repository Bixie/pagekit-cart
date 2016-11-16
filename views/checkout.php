<?php
/**
 * @var $view
 * @var Bixie\Cart\CartModule $cart
 */
$view->script('bixie-cart');

?>

<section id="bix-cart-checkout">

	<h1><?= __('Checkout') ?></h1>

	<partial :name="checkout_template"></partial>

</section>
