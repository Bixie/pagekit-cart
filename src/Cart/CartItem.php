<?php

namespace Bixie\Cart\Cart;

use Bixie\Cart\Model\Order;
use Pagekit\Application as App;

/**
 * CartItem
 */
class CartItem implements \JsonSerializable
{
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
	public $data = [];

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

	public function purchaseKey (Order $order) {
		if ($order->isValid()) {
			return sha1($order->status . $order->transaction_id . serialize($order->payment) . serialize($this));
		}
		return '';
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
