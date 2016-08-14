<?php
/**
 * @var $view
 * @var array $address
 * @var Bixie\Cart\Model\Order $order
 * @var Bixie\Cart\CartModule $cart
 */

?>

<dl class="uk-description-list-horizontal">
	<dt><?= __('First name') ?></dt>
	<dd><?= $address['first_name'] ?></dd>
	<?php if ($address['middle_name']) : ?>
		<dt><?= __('Middle name') ?></dt>
		<dd><?= $address['middle_name'] ?></dd>
	<?php endif; ?>
	<dt><?= __('Last name') ?></dt>
	<dd><?= $address['last_name'] ?></dd>
	<dt><?= __('Email address') ?></dt>
	<dd><?= $address['email'] ?></dd>
	<?php if ($address['phone']) : ?>
		<dt><?= __('Phone number') ?></dt>
		<dd><?= $address['phone'] ?></dd>
	<?php endif; ?>
	<?php if ($address['mobile']) : ?>
		<dt><?= __('Mobile phone') ?></dt>
		<dd><?= $address['mobile'] ?></dd>
	<?php endif; ?>
	<dt><?= __('Address') ?></dt>
	<dd><?= $address['address1'] ?></dd>
	<?php if ($address['address2']) : ?>
		<dt><?= __('Address 2') ?></dt>
		<dd><?= $address['address2'] ?></dd>
	<?php endif; ?>
	<dt><?= __('Zipcode') ?></dt>
	<dd><?= $address['zipcode'] ?></dd>
	<dt><?= __('City') ?></dt>
	<dd><?= $address['city'] ?></dd>
	<?php if ($address['county']) : ?>
		<dt><?= __('County') ?></dt>
		<dd><?= $address['county'] ?></dd>
	<?php endif; ?>
	<?php if ($address['state']) : ?>
		<dt><?= __('State') ?></dt>
		<dd><?= $address['state'] ?></dd>
	<?php endif; ?>
	<dt><?= __('Country') ?></dt>
	<dd><?= $address['country_code'] ?></dd>
</dl>

