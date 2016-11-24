<?php


namespace Bixie\Cart\Calculation\Price;


use Bixie\PkFramework\Traits\JsonSerializableTrait;

class CalculationObject {

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
     * @var int
     */
    public $quantity;
    /**
     * @var float
     */
    public $cost_price;
    /**
     * @var float
     */
    public $netto;
    /**
     * @var float
     */
    public $vat;
    /**
     * @var float
     */
    public $bruto;
    /**
     * @var float
     */
    public $vat_rate;
    /**
     * @var string
     */
    public $vat_type;
    /**
     * @var string
     */
    public $currency;

}