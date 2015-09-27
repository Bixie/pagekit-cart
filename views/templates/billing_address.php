<?php
/**
 * @var $view
 * @var Bixie\Cart\Model\Order $order
 * @var Bixie\Cart\CartModule $cart
 */

?>

<div class="uk-panel uk-panel-box">

	<h3 class="uk-panel-title"><?= __('Billing address') ?></h3>

	<dl class="uk-description-list">
		<dt><i class="uk-icon-user uk-icon-justify uk-margin-small-right"></i><?= __('First name') ?></dt>
		<dd><?= $order->get('billing_address.firstName') ?></dd>
		<dt><i class="uk-icon-user uk-icon-justify uk-margin-small-right"></i><?= __('Last name') ?></dt>
		<dd><?= $order->get('billing_address.lastName') ?></dd>
		<dt><i class="uk-icon-envelope-o uk-icon-justify uk-margin-small-right"></i><?= __('Email address') ?></dt>
		<dd><?= $order->get('billing_address.email') ?></dd>
		<?php if ($order->get('billing_address.phone')) : ?>
			<dt><i class="uk-icon-phone uk-icon-justify uk-margin-small-right"></i><?= __('Phone number') ?></dt>
			<dd><?= $order->get('billing_address.phone') ?></dd>
		<?php endif; ?>
		<dt><i class="uk-icon-building-o uk-icon-justify uk-margin-small-right"></i><?= __('Address') ?></dt>
		<dd><?= $order->get('billing_address.address1') ?></dd>
		<?php if ($order->get('billing_address.address2')) : ?>
			<dt><i class="uk-icon-building-o uk-icon-justify uk-margin-small-right"></i><?= __('Address 2') ?></dt>
			<dd><?= $order->get('billing_address.address2') ?></dd>
		<?php endif; ?>
		<dt><i class="uk-icon-map-pin uk-icon-justify uk-margin-small-right"></i><?= __('Zipcode') ?></dt>
		<dd><?= $order->get('billing_address.postcode') ?></dd>
		<dt><i class="uk-icon-map-o uk-icon-justify uk-margin-small-right"></i><?= __('City') ?></dt>
		<dd><?= $order->get('billing_address.city') ?></dd>
		<?php if ($order->get('billing_address.state')) : ?>
			<dt><i class="uk-icon-map-o uk-icon-justify uk-margin-small-right"></i><?= __('State') ?></dt>
			<dd><?= $order->get('billing_address.state') ?></dd>
		<?php endif; ?>
		<dt><i class="uk-icon-flag uk-icon-justify uk-margin-small-right"></i><?= __('Country') ?></dt>
		<dd><?= $order->get('billing_address.country') ?></dd>
	</dl>

</div>

