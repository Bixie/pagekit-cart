<?php

namespace Bixie\Cart\Calculation\Price;


use Bixie\Cart\Calculation\Prop\PropValue;
use Bixie\Cart\Calculation\Prop\PropValueCollection;
use Pagekit\System\Model\DataModelTrait;

class PriceCollection implements \ArrayAccess, \IteratorAggregate, \Countable, \JsonSerializable  {

    use DataModelTrait;

    /**
     * @var Price[]
     */
    protected $prices = [];
    
    /**
     * @var PropValueCollection
     */
    public $propValues;

    /**
     * PriceCollection constructor.
     * @param PropValueCollection $propValues
     * @param array               $prices
     */
    public function __construct (PropValueCollection $propValues, $prices = []) {
        $this->propValues = $propValues;
        foreach ($prices as $price) {
            if (!$price instanceof Price) {
                $price = new Price($price);
            }
            $this->add($price);
        }
    }


    /**
     * Gets a Price.
     * @param  string $key
     * @return mixed|null
     */
    public function getPrice ($key) {
        return isset($this->prices[$key]) ? $this->prices[$key] : null;
    }

    /**
     * @return array
     */
    public function all () {
        return $this->prices;
    }


    /**
     * @param Price $price
     * @return Price
     */
    public function add (Price $price) {
        $this->prices[] = $price;
        return $price;
    }

    /**
     * Unset a Price.
     * @param string|array $key
     */
    public function remove ($key) {
        foreach ((array)$key as $_key) {
            $this->offsetUnset($_key);
        }
    }

    /**
     * Checks if a Price exists.
     * @param  string $key
     * @return bool
     */
    public function offsetExists ($key) {
        return isset($this->prices[$key]);
    }

    /**
     * Gets a Price by key.
     * @param  string $key
     * @return bool
     */
    public function offsetGet ($key) {
        return $this->getPrice($key);
    }

    /**
     * Sets a $propValue.
     * @param string $name
     * @param string $price
     */
    public function offsetSet ($name, $price) {
        if (!$price instanceof Price) {
            $price = new Price($price);
        }
        $this->add($price);
    }

    /**
     * Unset a Price.
     * @param string $key
     */
    public function offsetUnset ($key) {
        $this->remove($key);
    }

    /**
     * Count cartItems.
     * @return int
     */
    public function count () {
        return count($this->prices);
    }

    /**
     * Implements the IteratorAggregate.
     * @return \ArrayIterator
     */
    public function getIterator () {
        return new \ArrayIterator($this->prices);
    }

    /**
     * @return Price[]
     */
    function jsonSerialize () {
        return [
            'prices' => $this->prices,
            'propValues' => $this->propValues
        ];
    }

}
