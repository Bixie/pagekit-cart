<?php

namespace Bixie\Cart\Calculation\Vat;


use Bixie\PkFramework\Traits\JsonSerializableTrait;

class VatObject {

    use JsonSerializableTrait;
    /**
     * @var string
     */
    public $description;
    /**
     * @var string
     */
    public $details;
    /**
     * Amount in cents for calulation base
     * @var float
     */
    public $base;
    /**
     * Amount in cents that is taxed
     * @var float
     */
    public $amount;
    /**
     * Rounded netto amount that is taxed
     * @var float
     */
    public $netto;
    /**
     * Rounded tax amount
     * @var float
     */
    public $vat;
    /**
     * @var string
     */
    public $type;
    /**
     * @var float
     */
    public $rate;
    /**
     * @var string
     */
    public $currency;


}