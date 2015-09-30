<?php

namespace Bixie\Cart\Model;

use Pagekit\Util\Arr;
use Pagekit\Application as App;
use Pagekit\Database\ORM\ModelTrait;

trait OrderModelTrait
{
    use ModelTrait;

	/**
	 * Gets a payment value.
	 *
	 * @param  string $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function getPayment($key, $default = null)
	{
		return Arr::get((array) $this->payment, $key, $default);
	}

	/**
	 * Sets a data value.
	 *
	 * @param string $key
	 * @param mixed  $value
	 */
	public function setPayment($key, $value)
	{
		if (null === $this->payment) {
			$this->payment = [];
		}

		Arr::set($this->payment, $key, $value);
	}

	/**
	 * Sets a data value.
	 * @param string $key
	 * @return bool
	 */
	public function issetPayment($key)
	{
		return Arr::has((array) $this->payment, $key);
	}

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
