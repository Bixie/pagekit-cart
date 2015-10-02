<?php

namespace Bixie\Cart\Event;

use Bixie\Cart\Cart\CartItem;
use Bixie\Cart\Model\Order;
use Pagekit\Application as App;
use Pagekit\Database\ORM\ModelTrait;
use Pagekit\Event\Event;
use Pagekit\View\View;

interface ProductListenerInterface {

	/**
	 * add tab to product edit
	 * @param Event $event
	 * @param $scripts
	 */
	function onViewScripts ($event, $scripts) ;

	/**
	 * Product displayed in admin
	 * @param      $event
	 * @param View $view
	 */
	function onProductView ($event, View $view);

	/**
	 * product shown in frontend
	 * @param Event $event
	 * @param ModelTrait $item
	 * @param View $view
	 */
	function onProductPrepare ($event, $item, View $view);

	/**
	 * cartItem displayed to user/admin
	 * @param Event    $event
	 * @param Order    $order
	 * @param CartItem $cartItem
	 */
	function onOrderitem (Event $event, Order $order, CartItem $cartItem);

	/**
	 * purchase key calculated from the cartitem
	 * @param Event    $event
	 * @param Order    $order
	 * @param CartItem $cartItem
	 */
	function onCartPurchaseKey (Event $event, Order $order, CartItem $cartItem);

	/**
	 * CartItem calculated on checkout
	 * @param Event    $event
	 * @param Order    $order
	 * @param CartItem $cartItem
	 */
	function onCalculateOrder (Event $event, Order $order, CartItem $cartItem);

	/**
	 * save event
	 * @param Event $event
	 * @param ModelTrait $item
	 */
	function onProductChange ($event, $item);

	/**
	 * @param Event $event
	 * @param ModelTrait $item
	 */
	function onProductDeleted ($event, $item);

	/**
	 * {@inheritdoc}
	 */
	function subscribe ();
}
