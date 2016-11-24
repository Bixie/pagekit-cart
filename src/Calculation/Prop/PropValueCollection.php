<?php

namespace Bixie\Cart\Calculation\Prop;


class PropValueCollection implements \ArrayAccess, \IteratorAggregate, \Countable, \JsonSerializable  {

    /**
     * @var PropValue[]
     */
    protected $propValues = [];

    public function __construct ($propvalues = []) {
        foreach ($propvalues as $propValue) {
            if (!$propValue instanceof PropValue) {
                 $propValue = new PropValue($propValue);
            }
            $this->add($propValue, false);
        }
        $this->sort();
    }

    public function hash () {
        $str = json_encode($this->values() ?: new \stdClass);
        return md5($str);
    }


    /**
     * Gets a PropValue.
     * @param  string $name
     * @return mixed|null
     */
    public function getPropValue ($name) {
        return isset($this->propValues[$name]) ? $this->propValues[$name] : null;
    }

    /**
     */
    public function sort () {
        uasort($this->propValues, function ($a, $b) {
            if ($a->prop->ordering == $b->prop->ordering) {
                return 0;
            }
            return ($a->prop->ordering < $b->prop->ordering) ? -1 : 1;
        });
    }

    /**
     * @return array
     */
    public function all () {
        return $this->propValues;
    }

    /**
     * @return array
     */
    public function values () {
        $values = [];
        foreach ($this->propValues as $name => $propValue) {
            $values[$name] = $propValue->option['value'];
        }
        return $values;
    }

    /**
     * @return array
     */
    public function texts () {
        $values = [];
        foreach ($this->propValues as $name => $propValue) {
            $values[$propValue->prop->label] = $propValue->option['text'];
        }
        return $values;
    }

    /**
     * @param PropValue $propValue
     * @param bool      $sort
     * @return PropValue
     */
    public function add (PropValue $propValue, $sort = true) {
        $this->propValues[$propValue->prop->name] = $propValue;
        if ($sort) $this->sort();
        return $propValue;
    }

    /**
     * Unset a PropValue.
     * @param string|array $name
     */
    public function remove ($name) {
        foreach ((array)$name as $_name) {
            $this->offsetUnset($_name);
        }
        $this->sort();
    }

    /**
     * Checks if a PropValue exists.
     * @param  string $name
     * @return bool
     */
    public function offsetExists ($name) {
        return isset($this->propValues[$name]);
    }

    /**
     * Gets a PropValue by id.
     * @param  string $name
     * @return bool
     */
    public function offsetGet ($name) {
        return $this->getPropValue($name);
    }

    /**
     * Sets a $propValue.
     * @param string $name
     * @param string $price
     */
    public function offsetSet ($name, $price) {
        if (!$price instanceof PropValue) {
            $price = new PropValue($price);
        }
        $this->add($price);
    }

    /**
     * Unset a PropValue.
     * @param string $name
     */
    public function offsetUnset ($name) {
        $this->remove($name);
    }

    /**
     * Count cartItems.
     * @return int
     */
    public function count () {
        return count($this->propValues);
    }

    /**
     * Implements the IteratorAggregate.
     * @return \ArrayIterator
     */
    public function getIterator () {
        return new \ArrayIterator($this->propValues);
    }

    /**
     * @return array
     */
    function jsonSerialize () {
        return $this->propValues;
    }
}
