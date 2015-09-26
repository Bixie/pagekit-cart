<?php

namespace Bixie\Cart\Cart;

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
	 * {@inheritdoc}
	 */
	public function jsonSerialize () {
		return $this;
	}
}
