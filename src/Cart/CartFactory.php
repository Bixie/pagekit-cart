<?php

namespace Bixie\Cart\Cart;

use Pagekit\Application as App;
use Pagekit\Session\Session;

class CartFactory implements \ArrayAccess, \IteratorAggregate
{

	/**
	 * @var Session
	 */
	protected $session;
    /**
     * @var array
     */
    protected $cartItems = [];

	/**
	 * CartFactory constructor.
	 * @param Session $session
	 */
	public function __construct ($session) {
		$this->session = $session;
	}

	/**
	 * Get shortcut.
	 * @see get()
	 * @param $id
	 * @return mixed|null
	 */
	public function __invoke($id)
	{
		return $this->get($id);
	}

	/**
     * Gets a cartItem.
     *
     * @param  string $name
     * @param bool $force
     * @return mixed|null
     */
    public function get($name, $force = false)
    {
        if ($force || empty($this->cartItems)) {
            $this->loadCartItems();
        }

        return isset($this->cartItems[$name]) ? $this->cartItems[$name] : null;
    }

    /**
     * Gets all cartItems.
     *
     * @param bool $force
     * @return array
     */
    public function all($force = false)
    {
        if ($force || empty($this->cartItems)) {
            $this->loadCartItems();
        }

        return $this->cartItems;
    }

    /**
     * Gets all stored ids.
     *
     * @param bool $force
     * @return array
     */
    public function ids($force = false)
    {
        if ($force || empty($this->cartItems)) {
            $this->loadCartItems();
        }

        return array_keys($this->cartItems);
    }

	/**
	 * Stores cart to session
	 */
	public function reset () {
		$this->cartItems = [];
		$this->saveCartItems();
    }

	/**
	 * Stores cart to session
	 */
	public function saveCartItems () {
		$this->session->set($this->getKey(), $this->cartItems);
	}

    /**
     * Loads a cartItem from data.
     *
     * @param  string|array $data
     * @return CartItem
     */
    public function load($data)
    {
        if (is_string($data)) {
            $data = @json_decode($data, true);
        }

		if (!isset($data['id'])) {
			$data['id'] = md5(time() . serialize($data));
		}

        if (is_array($data)) {
            return new CartItem($data);
        }
    }


    /**
     * Checks if a cartItem exists.
     *
     * @param  string $id
     * @return bool
     */
    public function offsetExists($id)
    {
        return isset($this->cartItems[$id]);
    }

    /**
     * Gets a cartItem by id.
     *
     * @param  string $id
     * @return bool
     */
    public function offsetGet($id)
    {
        return $this->get($id);
    }

    /**
     * Sets a cartItem.
     *
     * @param string $id
     * @param string $cartItem
     */
    public function offsetSet($id, $cartItem)
    {
        $this->cartItems[$id] = $cartItem;
    }

    /**
     * Unset a cartItem.
     *
     * @param string $id
     */
    public function offsetUnset($id)
    {
        unset($this->cartItems[$id]);
    }

    /**
     * Implements the IteratorAggregate.
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->cartItems);
    }

    /**
     * Load cartItems from session.
     */
    protected function loadCartItems()
    {

		if (null === $items = $this->session->get($this->getKey())) {
			$items = [];
		}

		foreach ($items as $cartItem) {

			if (!$cartItem instanceof CartItem) {
				$cartItem = $this->load($cartItem);
			}

			$this->cartItems[$cartItem->getId()] = $cartItem;
		}
    }

	/**
	 * Get a unique identifier for the cart session value.
	 *
	 * @param  string $var
	 * @return string
	 */
	public function getKey($var = 'cartItems')
	{
		return "_bixieCart.{$var}_" . sha1(get_class($this));
	}

	public function loadFromSession () {
		$this->loadCartItems();
		return $this;
	}


}
