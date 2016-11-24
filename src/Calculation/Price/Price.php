<?php

namespace Bixie\Cart\Calculation\Price;


use Bixie\PkFramework\Traits\JsonSerializableTrait;

class Price implements \JsonSerializable {

    use JsonSerializableTrait;

    public $min_quantity = 1;

    public $max_quantity = 1;

    public $price = 0;

    public $currency = 'EUR';

    /**
     * unique identifier
     * @return string
     */
    public function getKey () {
        return join('-', [$this->currency, $this->min_quantity, $this->max_quantity]);
    }

}