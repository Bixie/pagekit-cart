<?php

namespace Bixie\Cart\Event;


use Bixie\Cart\Cart\CartHandler;
use Pagekit\Event\Event;

class DeliveryOptionsEvent extends Event{

    /**
     * @var CartHandler
     */
    protected $cartHandler;
    /**
     * @var array
     */
    protected $delivery_options = [];

    /**
     * DeliveryOptionsEvent constructor.
     * @param CartHandler $cartHandler
     * @param array       $parameters
     */
    public function __construct (CartHandler $cartHandler, $parameters = []) {
        parent::__construct('bixie.cart.delivery_options', $parameters);

        $this->cartHandler = $cartHandler;
    }

    /**
     * @return CartHandler
     */
    public function getCartHandler () {
        return $this->cartHandler;
    }

    /**
     * @param CartHandler $cartHandler
     * @return DeliveryOptionsEvent
     */
    public function setCartHandler ($cartHandler) {
        $this->cartHandler = $cartHandler;
        return $this;
    }

    /**
     * @return array
     */
    public function getDeliveryOptions () {
        return $this->delivery_options;
    }

    /**
     * @param array $delivery_options
     * @return DeliveryOptionsEvent
     */
    public function setDeliveryOptions ($delivery_options) {
        $this->delivery_options = $delivery_options;
        return $this;
    }



}