<?php

namespace Bixie\Cart\Calculation\Price;


use Bixie\Cart\Calculation\CalculationException;

class QuantityCollection implements \ArrayAccess, \IteratorAggregate, \Countable, \JsonSerializable  {


    /**
     * @var Quantity[]
     */
    protected $quantities = [];


    /**
     * Gets a Price.
     * @param  string $hash
     * @return mixed|null
     */
    public function getPrice ($hash) {
        return isset($this->quantities[$hash]) ? $this->quantities[$hash] : null;
    }

    /**
     * @return array
     */
    public function all () {
        return $this->quantities;
    }


    /**
     * @param Quantity $quantity
     * @return Quantity
     */
    public function add (Quantity $quantity) {
        $this->quantities[$quantity->hash] = $quantity;
        return $quantity;
    }

    /**
     * Unset a Price.
     * @param string|array $hash
     */
    public function remove ($hash) {
        foreach ((array)$hash as $_hash) {
            $this->offsetUnset($_hash);
        }
    }

    /**
     * Checks if a Price exists.
     * @param  string $hash
     * @return bool
     */
    public function offsetExists ($hash) {
        return isset($this->quantities[$hash]);
    }

    /**
     * Gets a Price by key.
     * @param  string $hash
     * @return bool
     */
    public function offsetGet ($hash) {
        return $this->getPrice($hash);
    }

    /**
     * Sets a $propValue.
     * @param string $name
     * @param Quantity $collection
     */
    public function offsetSet ($name, $collection) {
        if (!$collection instanceof Quantity) {
            throw new CalculationException("Collection must be a price collection");
        }
        $this->add($collection);
    }

    /**
     * Unset a Price.
     * @param string $hash
     */
    public function offsetUnset ($hash) {
        $this->remove($hash);
    }

    /**
     * Count cartItems.
     * @return int
     */
    public function count () {
        return count($this->quantities);
    }

    /**
     * Implements the IteratorAggregate.
     * @return \ArrayIterator
     */
    public function getIterator () {
        return new \ArrayIterator($this->quantities);
    }

    /**
     * @return Quantity[]
     */
    function jsonSerialize () {
        return $this->quantities;
    }
}
