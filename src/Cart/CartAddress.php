<?php

namespace Bixie\Cart\Cart;
use Bixie\PkFramework\Traits\JsonSerializableTrait;


/**
 * CartAddress
 */
class CartAddress implements \JsonSerializable
{

    use JsonSerializableTrait;
	/**
	 * @var string
	 */
	public $first_name;
	/**
	 * @var string
	 */
	public $middle_name;
	/**
	 * @var string
	 */
	public $last_name;
	/**
	 * @var string
	 */
	public $company_name;
	/**
	 * @var string
	 */
	public $email;
	/**
	 * @var string
	 */
	public $address1;
	/**
	 * @var string
	 */
	public $address2;
	/**
	 * @var string
	 */
	public $zipcode;
	/**
	 * @var string
	 */
	public $county;
	/**
	 * @var string
	 */
	public $state;
	/**
	 * @var string
	 */
	public $city;
	/**
	 * @var string
	 */
	public $country_code;
	/**
	 * @var string
	 */
	public $phone;
	/**
	 * @var string
	 */
	public $phone_ext;
	/**
	 * @var string
	 */
	public $mobile;

    public function fullName () {
        return ($this->first_name ? $this->first_name . ' ' : '') . ($this->middle_name ? $this->middle_name . ' ' : '') . $this->last_name;
    }

}
