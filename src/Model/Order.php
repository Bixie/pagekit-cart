<?php

namespace Bixie\Cart\Model;

use Bixie\Cart\Cart\CartItem;
use Pagekit\Application as App;
use Pagekit\Event\Event;
use Pagekit\System\Model\DataModelTrait;

/**
 * @Entity(tableClass="@cart_order")
 */
class Order implements \JsonSerializable {
	use DataModelTrait, OrderModelTrait;

	/* Order not yet payed. */
	const STATUS_PENDING = 0;

	/* Order payed and confirmed. */
	const STATUS_CONFIRMED = 1;

	/** @Column(type="integer") @Id */
	public $id;

	/** @Column(type="integer") */
	public $user_id = 0;

	/** @Column(type="integer") */
	public $status = 0;

	/** @Column(type="datetime") */
	public $created;

	/** @Column(type="string") */
	public $email;

	/** @Column(type="json_array") */
	public $cartItemsData;

	/** @Column(type="json_array") */
	public $payment;

	/** @Column(type="decimal") */
	public $total_netto = 0.00;

	/** @Column(type="decimal") */
	public $total_bruto = 0.00;

	/** @Column(type="string") */
	public $currency;

	/** @Column(type="string") */
	public $transaction_id;

	/**
	 * @BelongsTo(targetEntity="Pagekit\User\Model\User", keyFrom="user_id")
	 */
	public $user;

	/** @var array */
	protected static $properties = [
		'valid' => 'isValid',
		'cartItems' => 'getCartItems',
		'user_username' => 'getUserUsername',
		'user_name' => 'getUserName'
	];

	/**
	 * Creates a new instance of this model.
	 * @param  array $cartItems
	 * @param  array $checkout
	 * @return static Order
	 */
	public static function createNew ($cartItems, $checkout) {
		$user = App::auth()->getUser();//get from auth, fresh user
		return static::create([
			'user_id' => ($user ? $user->id : 0),
			'status' => self::STATUS_PENDING,
			'created' => new \DateTime(),
			'email' => $checkout['billing_address']['email'],
			'currency' => $checkout['currency'],
			'data' => $checkout,
			'cartItemsData' => $cartItems
		]);
	}

	public function isValid () {
		return $this->status == self::STATUS_CONFIRMED;
	}

	public function getUserName () {
		return $this->user ? $this->user->name : null;
	}

	public function getUserUsername () {
		return $this->user ? $this->user->username : null;
	}

	/**
	 * @return CartItem[]
	 */
	public function getCartItems () {
		static $cartItems;
		if (!$cartItems) {
			$cartItems = [];
			$bixieCart = App::bixieCart();
			foreach ($this->cartItemsData as $cartItemData) {
				$cartItems[] = $cartItemData instanceof CartItem ? $cartItemData : $bixieCart->load($cartItemData);
			}
		}
		return $cartItems;
	}

	public function calculateOrder () {
		$this->total_netto = 0;
		$this->total_bruto = 0;
		foreach ($this->getCartItems() as $cartItem) {

			$event = new Event('bixie.calculate.order');
			App::trigger($event, [$this, $cartItem]);

			$prices = $cartItem->calcPrices($this);

			$this->total_netto += $prices['netto'];
			$this->total_bruto += $prices['bruto'];
		}
		return $this;
	}

	public static function getStatuses () {
		return [
			self::STATUS_PENDING => __('Order pending'),
			self::STATUS_CONFIRMED => __('Order confirmed')
		];
	}

	public function getStatusText () {
		$statuses = self::getStatuses();
		return isset($statuses[$this->status]) ? $statuses[$this->status] : __('Unknown');
	}

	/**
	 * {@inheritdoc}
	 */
	public function jsonSerialize () {
		$data = [
//			'fileName' => basename($this->path),
//			'download' => $this->getDownloadLink(),
//			'url' => App::url('@download/id', ['id' => $this->id ?: 0], 'base')
		];

		return $this->toArray($data, ['cartItemsData']);
	}
}
