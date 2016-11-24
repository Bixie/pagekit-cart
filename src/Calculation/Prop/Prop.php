<?php

namespace Bixie\Cart\Calculation\Prop;


use Bixie\PkFramework\Traits\JsonSerializableTrait;

class Prop {

    use JsonSerializableTrait;

    public $name = '';

    public $label = '';

    public $options = [];

    public $config = [
        'price_prop' => true
    ];

    public $ordering = 0;

}