<?php
$view->script('bixie-cart-widget', 'bixie/cart:app/bundle/bixie-cart-widget.js', 'bixie-cart');
?>
<div id="cart-widget">
	<ul class="uk-subnav">
		<li class="" v-cloak>
			<a onclick="$bixCart.openCart()"><span class="uk-text-primary"><?= $widget->title ?><i
						class="uk-icon-shopping-cart uk-margin-small-left"></i></span>
				<span class="uk-text-small">{{ $bixCart.nr_items_format }}</span>
				<strong class="uk-text-small">{{{ $bixCart.total_formatted }}}</strong>

			</a>
		</li>
	</ul>

</div>
