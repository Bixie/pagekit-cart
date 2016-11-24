<?php

namespace Bixie\Cart\Cart;

use Bixie\Cart\CartException;
use Pagekit\System\Model\DataModelTrait;
use Pagekit\Util\Arr;

/**
 * PaymentOption
 */
class PaymentOption implements \JsonSerializable
{

    use DataModelTrait;

	/**
	 * @var string
	 */
	public $name = '';

    /**
     * @var string
     */
    public $title = '';

    /**
     * @var float
     */
    public $price = '';

    /**
     * @var string
     */
    public $currency = 'EUR';

    /**
	 * @var array
	 */
	public $settings = [];

	/**
	 * CartItem constructor.
	 * @param array $data
	 */
	public function __construct (array $data) {
        foreach (get_object_vars($this) as $key => $default) {
            $this->$key = Arr::get($data, $key, $default);
        }
	}


	/**
	 * {@inheritdoc}
	 */
	public function jsonSerialize () {
	    if (!isset($this->data)) {
	        $this->data = [
	            'image' => ['src' => '']
            ];
        }
		return $this;
	}
}
