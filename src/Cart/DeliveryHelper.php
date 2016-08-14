<?php

namespace Bixie\Cart\Cart;


use Pagekit\Application as App;
use Bixie\Cart\CartException;
use Bixie\Cart\Event\DeliveryOptionsEvent;

class DeliveryHelper {

    /**
     * @var CartHandler
     */
    protected $cartHandler;

    /**
     * DeliveryHelper constructor.
     * @param CartHandler $cartHandler
     */
    public function __construct (CartHandler $cartHandler) {
        $this->cartHandler = $cartHandler;
    }

    /**
     * @return DeliveryOption[]
     */
    public function getDeliveryOptions () {
        $delivery_options = [];
        if ($this->cartHandler->itemCount() && $this->cartHandler->getDeliveryAddress()) {
            $delivery_options = App::trigger(new DeliveryOptionsEvent($this->cartHandler))->getDeliveryOptions();
        }
        return $delivery_options;
    }

    /**
     * @param $delivery_option_id
     * @return bool|DeliveryOption
     */
    public function getDeliveryOption ($delivery_option_id) {
        foreach ($this->getDeliveryOptions() as $deliveryOption) {
            if ($deliveryOption->id == $delivery_option_id) {
                return $deliveryOption;
            }
        }
        return false;
    }
}