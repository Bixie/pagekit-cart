<?php

namespace Bixie\Cart\Cart;

use Bixie\Cart\CartException;
use Pagekit\System\Model\DataModelTrait;
use Pagekit\Util\Arr;

/**
 * DeliveryOption
 */
class DeliveryOption implements \JsonSerializable
{

    use DataModelTrait;

	/**
	 * @var string
	 */
	public $id = '';

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
	 * @var integer
	 */
	public $business_days = 0;

	/**
	 * @var \DateTime
	 */
	public $eta_date;

	/**
	 * CartItem constructor.
	 * @param array $data
	 */
	public function __construct (array $data) {
        foreach (get_object_vars($this) as $key => $default) {
            if ($key == 'eta_date') {
                $this->setEtaDate(Arr::get($data, $key, $default));
            } else {
                $this->$key = Arr::get($data, $key, $default);
            }
        }
	}

    /**
     * @param \DateTime $eta_date
     * @param string    $tz
     * @return $this
     * @throws CartException
     */
    public function setEtaDate ($eta_date, $tz = 'UTC') {
        if (is_string($eta_date)) {
            try {
                $etaDate = new \DateTime($eta_date, new \DateTimeZone($tz));
                if ($eta_date == '' && $this->business_days) {
                    //todo add non-bus days in range
                    $etaDate->add(new \DateInterval('P' . $this->business_days . 'D'));
                }

            } catch (\InvalidArgumentException $e) {
                throw new CartException(sprintf('Invalid ETA date', $eta_date));
            }
        } elseif (is_array($eta_date)) {
            try {
                $etaDate = new \DateTime($eta_date['date'], new \DateTimeZone($eta_date['timezone']));

            } catch (\InvalidArgumentException $e) {
                throw new CartException(sprintf('Invalid ETA date', $eta_date));
            }
        } elseif ($eta_date instanceof \DateTime) {
            $etaDate = $eta_date;
        } else {
            throw new CartException(sprintf('Invalid ETA date', $eta_date));
        }
        $this->eta_date = $etaDate;
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
        $this->eta_date = $this->eta_date ? $this->eta_date->format(\DateTime::ATOM) : '';
		return $this;
	}
}
