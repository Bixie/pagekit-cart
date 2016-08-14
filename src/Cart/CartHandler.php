<?php

namespace Bixie\Cart\Cart;


class CartHandler {

    protected $currency = 'EUR';
    /**
     * @var CartItemCollection
     */
    protected $items;
    /**
     * @var CartAddress
     */
    protected $deliveryAddress;
    /**
     * @var CartAddress
     */
    protected $billingAddress;
    /**
     * @var DeliveryOption
     */
    protected $deliveryOption;
    /**
     * @var PaymentOption
     */
    protected $paymentOption;
    /**
     * @var array
     */
    protected $order_data = [];

    /**
     * CartHandler constructor.
     * @param CartItemCollection $items
     */
    public function __construct (CartItemCollection $items) {
        $this->items = $items;
    }

    /**
     * @return string
     */
    public function getCurrency () {
        return $this->currency;
    }

    /**
     * @param string $currency
     * @return CartHandler
     */
    public function setCurrency ($currency) {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @return CartAddress
     */
    public function getDeliveryAddress () {
        return $this->deliveryAddress;
    }

    /**
     * @param CartAddress|array $deliveryAddress
     * @return CartHandler
     */
    public function setDeliveryAddress ($deliveryAddress) {
        if (is_array($deliveryAddress)) {
            $deliveryAddress = new CartAddress($deliveryAddress);
        }
        $this->deliveryAddress = $deliveryAddress;
        return $this;
    }

    /**
     * @return CartAddress
     */
    public function getBillingAddress () {
        return $this->billingAddress;
    }

    /**
     * @param CartAddress $billingAddress
     * @return CartHandler
     */
    public function setBillingAddress ($billingAddress) {
        if (is_array($billingAddress)) {
            $billingAddress = new CartAddress($billingAddress);
        }
        $this->billingAddress = $billingAddress;
        return $this;
    }

    /**
     * @return DeliveryOption
     */
    public function getDeliveryOption () {
        return $this->deliveryOption;
    }

    /**
     * @param DeliveryOption $deliveryOption
     * @return CartHandler
     */
    public function setDeliveryOption ($deliveryOption) {
        $this->deliveryOption = $deliveryOption;
        return $this;
    }

    /**
     * @return PaymentOption
     */
    public function getPaymentOption () {
        return $this->paymentOption;
    }

    /**
     * @param PaymentOption $paymentOption
     * @return CartHandler
     */
    public function setPaymentOption ($paymentOption) {
        $this->paymentOption = $paymentOption;
        return $this;
    }

    /**
     * @return CartItem[]
     */
    public function getItems () {
        return $this->items->all();
    }

    /**
     * @return int
     */
    public function itemCount () {
        return count($this->items);
    }

    /**
     * @return array
     */
    public function getOrderData () {
        return array_merge([
            'payment' => ['success' => false, 'method_name' => '', 'message' => ''],
            'payment_option' => (array)$this->getPaymentOption(),
            'delivery_option' => (array)$this->getDeliveryOption(),
            'delivery_address' => (array)$this->getDeliveryAddress(),
            'billing_address' => (array)$this->getBillingAddress()
        ], $this->order_data);
    }

}