<?php

namespace Bixie\Cart\Model;

use Bixie\Cart\Cart\CartHandler;
use Bixie\Cart\Cart\CartItem;
use Bixie\Cart\Cart\CartItemCollection;
use Bixie\Cart\Cart\DeliveryOption;
use Pagekit\Application as App;
use Pagekit\Event\Event;
use Pagekit\System\Model\DataModelTrait;

/**
 * @Entity(tableClass="@cart_order",eventPrefix="cart_order")
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

    /** @Column(type="string") */
    public $ext_key;

    /** @Column(type="string") */
    public $reference;

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
		'url' => 'getUrl',
		'cartItems' => 'getCartItems',
		'user_username' => 'getUserUsername',
		'user_name' => 'getUserName'
	];

    public function getUrl () {
        return App::url('@cart/orders/detail', ['transaction_id' => $this->transaction_id]);
    }

	public function isValid () {
		return $this->status == self::STATUS_CONFIRMED;
	}

	public function getUserName () {
		return $this->user ? $this->user->name : 'Guest';
	}

	public function getUserUsername () {
		return $this->user ? $this->user->username : 'Guest';
	}

	/**
	 * @return CartItem[]
	 */
	public function getCartItems () {
        $cartItems = [];
        /** @var CartItemCollection $bixieCart */
        $bixieCart = App::bixieCart();
        foreach ((array)$this->cartItemsData as $cartItemData) {
            $cartItems[] = $cartItemData instanceof CartItem ? $cartItemData : $bixieCart->loadItemFromData($cartItemData);
        }
		return $cartItems;
	}

	public function calculateOrder () {
		$this->total_netto = 0;
		$this->total_bruto = 0;
        $vat_calc = ['none' => 0, 'low' => 0, 'high' => 0];
		foreach ($this->getCartItems() as $cartItem) {

			$event = new Event('bixie.calculate.order');
			App::trigger($event, [$this, $cartItem]);

			$prices = $cartItem->calcPrices($this);

			$this->total_netto += $prices['netto'];
            $vat_calc[$cartItem->vat] += $prices['netto'] * 100;
		}
		if ($delivery_price = $this->get('delivery_option.price', 0)) {
            $this->total_netto += $delivery_price;
            $vat_calc['high'] += $delivery_price * 100;
        }
		if ($payment_price = $this->get('payment_option.price', 0)) {
            $this->total_netto += $payment_price;
            $vat_calc['high'] += $payment_price * 100;
        }

        $this->total_bruto += (round(
                                App::cartCalcVat($vat_calc['high'], 'high')
                                + App::cartCalcVat($vat_calc['low'], 'low')) / 100) + $this->total_netto;
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
     * @return DeliveryOption|bool
     */
    public function getDeliverOption() {
        $data = $this->get('delivery_option');
        if ($data['id']) {
            return new DeliveryOption($data);
        }
        return false;
    }

    /**
	 * {@inheritdoc}
	 */
	public function jsonSerialize () {
		$data = [
		];

		return $this->toArray($data, ['cartItemsData']);
	}
}
