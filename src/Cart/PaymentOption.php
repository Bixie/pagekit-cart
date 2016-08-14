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
     * @param \DateTime $eta_date
     * @param string    $tz
     * @return $this
     */
    public function setEtaDate ($eta_date, $tz = 'UTC') {
        if (is_string($eta_date)) {
            try {
                $eta_date = new \DateTime($eta_date, new \DateTimeZone($tz));
            } catch (\InvalidArgumentException $e) {
                throw new CartException(sprintf('Invalid ETA date', $eta_date));
            }
        }
        $this->eta_date = $eta_date;
        return $this;
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
