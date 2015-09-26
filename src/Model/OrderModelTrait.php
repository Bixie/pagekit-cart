<?php

namespace Bixie\Cart\Model;

use Bixie\Cart\Cart\CartItem;
use Pagekit\Application as App;
use Pagekit\Database\ORM\ModelTrait;

trait OrderModelTrait
{
    use ModelTrait;

	/**
	 * @param $transaction_id
	 * @return mixed
	 */
	public static function findByTransaction_id ($transaction_id) {
		return static::where(['transaction_id' => $transaction_id])->first();
	}

    /**
     * @Saving
     */
    public static function saving($event, Order $order)
    {


    }

}
