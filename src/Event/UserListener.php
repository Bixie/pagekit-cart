<?php

namespace Bixie\Cart\Event;

use Bixie\Cart\Model\Order;
use Pagekit\Application as App;
use Pagekit\Event\EventSubscriberInterface;
use Pagekit\User\Model\User;

class UserListener implements EventSubscriberInterface {

	protected $request;


	public function onUserDeleted ($event, User $user) {
		foreach (Order::where(['user_id = :id'], [':id' => $user->id])->get() as $order) {
			$order->user_id = 0;;
			$order->save();
		}
	}


	/**
	 * {@inheritdoc}
	 */
	public function subscribe () {
		return [
			'model.user.deleted' => 'onUserDeleted'
		];
	}
}
