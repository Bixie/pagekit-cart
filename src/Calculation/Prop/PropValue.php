<?php

namespace Bixie\Cart\Calculation\Prop;


use Bixie\PkFramework\Traits\JsonSerializableTrait;

class PropValue {

    use JsonSerializableTrait;

    /**
     * @var array
     */
    public $option;
    /**
     * @var Prop
     */
    public $prop;

    public function __construct (array $data = []) {
        $this->option = $data['option'];
        $this->prop = $data['prop'] instanceof Prop ? $data['prop'] : new Prop($data['prop']);
    }


}