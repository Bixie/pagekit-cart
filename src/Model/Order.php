<?php

namespace Bixie\Cart\Model;

use Bixie\Cart\Cart\CartItem;
use Bixie\Cart\Cart\CartItemCollection;
use Bixie\Cart\Cart\DeliveryOption;
use Bixie\Cart\Cart\PaymentOption;
use Pagekit\Application as App;
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
		'attachments' => 'getAttachments',
		'valid' => 'isValid',
		'url' => 'getUrl',
		'cartItems' => 'getCartItems',
		'user_username' => 'getUserUsername',
		'user_name' => 'getUserName',
		'status_text' => 'getStatusText',
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

	public function getAttachments () {
		return []; //todo get invoices
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
    public function getDeliveryOption() {
        $data = $this->get('delivery_option');
        if ($data['id']) {
            return new DeliveryOption($data);
        }
        return false;
    }

    /**
     * @return PaymentOption|bool
     */
    public function getPaymentOption() {
        if ($data = $this->get('payment_option')) {
            return new PaymentOption($data);
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
