<?php

namespace Bixie\Cart\Model;

use Bixie\Cart\Cart\CartItem;
use Pagekit\Application as App;
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
	public $status = 0;

	/** @Column(type="datetime") */
	public $created;

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

	/** @var array */
	protected static $properties = [
		'valid' => 'isValid',
		'cartItems' => 'getCartItems'
	];

	/**
	 * Creates a new instance of this model.
	 * @param  array $cartItems
	 * @param  array $checkout
	 * @return static
	 */
	public static function createNew ($cartItems, $checkout) {
		return static::create([
			'status' => self::STATUS_PENDING,
			'created' => new \DateTime(),
			'currency' => $checkout['currency'],
			'data' => $checkout,
			'cartItemsData' => $cartItems
		]);
	}

	public function isValid () {
		return $this->status == self::STATUS_CONFIRMED;
	}

	public function getCartItems () {
		$cartItems = [];
		$bixieCart = App::bixieCart();
		foreach ($this->cartItemsData as $cartItemData) {
			$cartItems[] = $cartItemData instanceof CartItem ? $cartItemData : $bixieCart->load($cartItemData);
		}
		return $cartItems;
	}

	public function calculateOrder () {
		$this->total_netto = 0;
		$this->total_bruto = 0;
		foreach ($this->getCartItems() as $cartItem) {
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

		return $this->toArray($data);
	}
}
