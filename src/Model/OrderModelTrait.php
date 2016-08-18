<?php

namespace Bixie\Cart\Model;

use Bixie\Cart\Cart\CartHandler;
use Pagekit\Util\Arr;
use Pagekit\Application as App;
use Pagekit\Database\ORM\ModelTrait;

trait OrderModelTrait
{
    use ModelTrait {
        create as modelCreate;
    }

    /**
     * Proxy for emailsender to create empty orders
     * @param array $data
     * @return static
     */
    public static function create ($data = []) {
        return static::modelCreate($data);
    }

    /**
     * Creates a new instance of this model.
     * @param  CartHandler $cartHandler
     * @param array        $data
     * @return static Order
     */
    public static function createFromHandler (CartHandler $cartHandler, $data = []) {
        $user = App::auth()->getUser();//get from auth, fresh user
        $order_data = !empty($data['data']) ? array_merge($data['data'], $cartHandler->getOrderData()): $cartHandler->getOrderData();
        return static::modelCreate(array_merge($data, [
            'user_id' => ($user ? $user->id : 0),
            'status' => self::STATUS_PENDING,
            'created' => new \DateTime(),
            'email' => $cartHandler->getDeliveryAddress()->email,
            'currency' => $cartHandler->getCurrency(),
            'data' => $order_data,
            'cartItemsData' => $cartHandler->getItems()
        ]));
    }

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
