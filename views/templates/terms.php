<?php
/**
 * @var $view
 * @var string $title
 * @var string $content
 * @var Bixie\Cart\CartModule $cart
 */

?>

<div class="uk-modal-header">
	<h2><?= __($title) ?></h2>
</div>
<div class="uk-margin">
	<?= $content ?>
</div>
<div class="uk-modal-footer uk-text-right">
	<button class="uk-button uk-modal-close"><?= __('Close') ?></button>
</div>
