<?php

namespace Bixie\Cart\Model;

use Pagekit\Application as App;
use Pagekit\Database\ORM\ModelTrait;
use Pagekit\System\Model\DataModelTrait;

/**
 * @Entity(tableClass="@cart_product",eventPrefix="cart_product")
 */
class Product implements \JsonSerializable
{
	use  DataModelTrait, ProductModelTrait;

	/** @Column(type="integer") @Id */
	public $id;

	/** @Column(type="integer") */
	public $active = 0;

	/** @Column(type="string") */
	public $item_model;

	/** @Column(type="integer") */
	public $item_id;

	/** @Column(type="decimal") */
	public $price = 0.00;

	/** @Column(type="string") */
	public $vat;

	/** @Column(type="string") */
	public $currency;

	/** @var array */
	protected static $properties = [
		'item' => 'loadItemModel',
	];


	/**
	 * Creates a new instance of this model.
	 * @param  array $data
	 * @return static
	 */
	public static function createNew ($data = []) {
		$config = App::module('bixie/cart')->config();
		return static::create(array_merge([
			'vat' => $config['vat'],
			'currency' => $config['currency']
		], $data));
	}


	/**
	 * {@inheritdoc}
	 */
	public function jsonSerialize () {
		$data = [
		];

		return $this->toArray($data);
	}
}
