<?php

namespace Bixie\Cart\Cart;

use Bixie\Cart\Model\Order;
use Bixie\Cart\Calculation\Price\Quantity;
use Bixie\Cart\Calculation\Price\QuantityCollection;
use Bixie\Cart\Calculation\Prop\PropModelTrait;
use Pagekit\Application as App;
use Pagekit\Event\Event;
use Pagekit\System\Model\DataModelTrait;

/**
 * CartItem
 */
class CartItem implements \JsonSerializable
{
	use DataModelTrait, PropModelTrait;

	/**
	 * @var string
	 */
	public $id;
	/**
	 * @var string
	 */
	public $sku;
    /**
     * @var string
     */
    public $title;
	/**
	 * @var integer
	 */
	public $item_id;
	/**
	 * @var string
	 */
	public $item_model;
	/**
	 * @var string
	 */
	public $item_url;
	/**
	 * @var float
	 */
	public $price;
    /**
     * @var integer
     */
    public $quantity;
	/**
	 * @var string
	 */
	public $currency;
	/**
	 * @var string
	 */
	public $vat;
	/**
	 * @var array
	 */
	public $quantity_data;
	/**
	 * @var array
	 */
	protected $templates = [];

	/**
	 * @var array
	 */
	protected $item;

	/**
	 * CartItem constructor.
	 * @param array $data
	 */
	public function __construct (array $data) {
		foreach ($data as $key => $value) {
			$this->$key = $value;
		}

	}

	/**
	 * @return string
	 */
	public function getId () {
		return $this->id;
	}

    /**
     * @return QuantityCollection
     */
    public function getQuantityPrices () {
        $quantities = new QuantityCollection();
        foreach ($this->quantity_data as $hash => $quantity_data) {
            $quantities[$hash] = new Quantity($quantity_data);
        }
        return $quantities;
	}

	/**
	 * @return mixed
	 */
	public function loadItemModel () {
		if (empty($this->item) && $this->item_id && class_exists($this->item_model)) {
			$item = call_user_func([$this->item_model, 'find'], $this->item_id);
			return $item;
		}
		return null;
	}


	/**
	 * @param Order $order
	 * @return string
	 */
	public function purchaseKey (Order $order) {
		$event = new Event('bixie.cart.purchaseKey');
		App::trigger($event, [$order, $this]);

		if ($order->isValid() && !$event['invalidPurchaseKey']) {
			return sha1($order->status . $order->transaction_id . serialize($order->payment) . $this->getId());
		}
		return '';
	}

	/**
	 * @param $name
	 * @param $content
	 */
	public function setTemplate ($name, $content) {
		$this->templates[$name] = $content;
	}

	/**
	 * @param $name
	 * @return string
	 */
	public function getTemplate ($name) {
		return isset($this->templates[$name]) ? $this->templates[$name] : '';
	}

	/**
	 * {@inheritdoc}
	 */
	public function jsonSerialize () {
		return $this;
	}
}
