<?php

namespace Bixie\Cart\Cart;

use Pagekit\Application as App;
use Pagekit\Session\Session;

class CartItemCollection implements \ArrayAccess, \IteratorAggregate, \Countable {

    /**
     * @var Session
     */
    protected $session;
    /**
     * @var array
     */
    protected $items = [];

    /**
     * CartFactory constructor.
     * @param Session $session
     */
    public function __construct ($session) {
        $this->session = $session;
    }

    /**
     * Get shortcut.
     * @see getItem()
     * @param $id
     * @return mixed|null
     */
    public function __invoke ($id) {
        return $this->getItem($id);
    }

    /**
     * save cart
     */
    public function __destruct () {
        $this->saveCartItems();
    }


    /**
     * Gets a cartItem.
     * @param  string $name
     * @param bool    $force
     * @return mixed|null
     */
    public function getItem ($name, $force = false) {
        if ($force || empty($this->items)) {
            $this->loadCartItems();
        }

        return isset($this->items[$name]) ? $this->items[$name] : null;
    }

    /**
     * Gets all cartItems.
     * @param bool $force
     * @return array
     */
    public function all ($force = false) {
        if ($force || empty($this->items)) {
            $this->loadCartItems();
        }

        return $this->items;
    }

    /**
     * Gets all stored ids.
     * @param bool $force
     * @return array
     */
    public function ids ($force = false) {
        if ($force || empty($this->items)) {
            $this->loadCartItems();
        }

        return array_keys($this->items);
    }

    /**
     * Stores cart to session
     */
    public function reset () {
        $this->items = [];
        $this->saveCartItems();
    }

    /**
     * Stores cart to session
     */
    public function saveCartItems () {
        $this->session->set($this->getKey(), $this->items);
    }

    /**
     * @return $this
     */
    public function loadFromSession () {
        $this->loadCartItems();
        return $this;
    }

    /**
     * Loads a cartItem from data.
     * @param  string|array $data
     * @return CartItem
     */
    public function addItemFromData ($data) {
        return $this->add($this->loadItemFromData($data));
    }

    /**
     * Loads a cartItem from data.
     * @param  string|array $data
     * @return CartItem
     */
    public function loadItemFromData ($data) {
        if (is_string($data)) {
            $data = @json_decode($data, true);
        }

        if (!isset($data['id'])) {
            $data['id'] = md5(time() . serialize($data));
        }

        return new CartItem((array)$data);
    }

    /**
     * @param CartItem $cartItem
     * @return CartItem
     */
    public function add (CartItem $cartItem) {
        $this->items[$cartItem->getId()] = $cartItem;
        return $cartItem;
    }

    /**
     * Unset a cartItem.
     * @param string|array $id
     */
    public function remove ($id) {
        foreach ((array)$id as $_id) {
            $this->offsetUnset($_id);
        }
    }

    /**
     * Checks if a cartItem exists.
     * @param  string $id
     * @return bool
     */
    public function offsetExists ($id) {
        return isset($this->items[$id]);
    }

    /**
     * Gets a cartItem by id.
     * @param  string $name
     * @return bool
     */
    public function offsetGet ($name) {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        return $this->getItem($name);
    }

    /**
     * Sets a cartItem.
     * @param string $id
     * @param string $cartItem
     */
    public function offsetSet ($id, $cartItem) {
        if (property_exists($this, $id)) {
            $this->$id;
        }
        $this->items[$id] = $cartItem;
    }

    /**
     * Unset a cartItem.
     * @param string $id
     */
    public function offsetUnset ($id) {
        unset($this->items[$id]);
    }

    /**
     * Count cartItems.
     * @return int
     */
    public function count () {
        return count($this->items);
    }

    /**
     * Implements the IteratorAggregate.
     * @return \ArrayIterator
     */
    public function getIterator () {
        return new \ArrayIterator($this->items);
    }

    /**
     * Load cartItems from session.
     */
    protected function loadCartItems () {

        if (null === $items = $this->session->get($this->getKey())) {
            $items = [];
        }

        foreach ($items as $cartItem) {

            if (!$cartItem instanceof CartItem) {
                $cartItem = $this->load($cartItem);
            }

            $this->add($cartItem);
        }
    }


    /**
     * Get a unique identifier for the cart session value.
     * @param  string $var
     * @return string
     */
    public function getKey ($var = 'cartItems') {
        return "_bixieCart.{$var}_" . sha1(get_class($this));
    }
}
