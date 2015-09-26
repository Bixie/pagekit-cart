<?php
$cart->config('currency');
?>
<div id="cart-widget">

	<cartlist></cartlist>

	<div class="uk-form">
		<select v-model="filters.currency">
			<option value="EUR">{{ 'Euro' | trans }}</option>
			<option value="USD">{{ 'US Dollar' | trans }}</option>
		</select>
	</div>

	<div class="uk-margin">
		<a v-attr="href: checkout_url" class="uk-button uk-button-success">
			<i class="uk-icon-shopping-cart uk-margin-small-right"></i>{{ 'To checkout' | trans }}</a>
	</div>

</div>
