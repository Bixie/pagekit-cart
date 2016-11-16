
<?php
/**
 * @var $view
 * @var Pagekit\User\Model\User $user
 * @var Bixie\Cart\Model\Order $order
 * @var Bixie\Cart\CartModule $cart
 */

?>

<div style="margin: 20px 0">

	<h3><?= __('Account created') ?></h3>

	<p><?= __('Your account has been prepared! Now it only needs to be confirmed.') ?></p>

	<a href="<?= $view->url('@user/registration/activate', ['user' => $user->username, 'key' => $user->activation], \Pagekit\Routing\Generator\UrlGenerator::ABSOLUTE_URL) ?>">
		<?= __('Click this link to confirm your account') ?>
	</a>

	<p><?= __('The order with tranaction ID %transaction_id% will be accessable in your account.', ['%transaction_id%' => $order->transaction_id]) ?></p>

</div>

