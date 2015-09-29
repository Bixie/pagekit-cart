<?php

namespace Bixie\Cart\Cart;

use Bixie\Cart\Model\Order;
use Pagekit\Application as App;
use Pagekit\Event\Event;
use Pagekit\System\Model\DataModelTrait;

/**
 * CartItem
 */
class CartItem implements \JsonSerializable
{
	use DataModelTrait;

	/**
	 * @var string
	 */
	public $id;
	/**
	 * @var integer
	 */
	public $product_id;
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
	public $item_title;
	/**
	 * @var string
	 */
	public $item_url;
	/**
	 * @var float
	 */
	public $price;
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
	 * @return array
	 */
	public function calcPrices (Order $order) {

		$netto = $this->convertPrice($this->price, $order);
		$vatclass = App::module('bixie/cart')->config('vatclasses.' . $this->vat);
		$bruto = (round(($netto * 100) * (($vatclass['rate'] / 100) + 1))) / 100;

		return [
			'netto' => $netto,
			'bruto' => $bruto,
			'vat' => $bruto - $netto,
			'vatclass' => $vatclass
		];
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
	 * @param float $price
	 * @param Order $order
	 * @return float
	 */
	protected function convertPrice ($price, Order $order) {

		if ($this->currency !== $order->currency) {
			$factor = App::module('bixie/cart')->config($this->currency . 'to' . $order->currency);
			$price = (round(($price * 100) * ($factor ? : 1))) / 100;
		}
		return $price;
	}

	/**
	 * {@inheritdoc}
	 */
	public function jsonSerialize () {
		return $this;
	}
}
