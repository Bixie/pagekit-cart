<?php
/**
 * Created by Matthijs on 22-11-2016.
 */


namespace Bixie\Cart\Calculation\Price;


use Bixie\PkFramework\Traits\JsonSerializableTrait;

class Quantity implements \JsonSerializable {

    use JsonSerializableTrait;
    /**
     * @var string
     */
    public $hash = '';
    /**
     * @var string
     */
    public $type = '';
    /**
     * @var array
     */
    public $quantities = [];

}