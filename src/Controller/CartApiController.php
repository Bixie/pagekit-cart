<?php

namespace Bixie\Cart\Controller;

use Bixie\Cart\Calculation\OrderCalculator;
use Bixie\Cart\CartDeliveryException;
use Pagekit\Application as App;
use Bixie\Cart\Cart\CartHandler;
use Bixie\Cart\Cart\DeliveryHelper;
use Bixie\Cart\CartException;
use Bixie\Cart\Helper\UserHelper;
use Bixie\Cart\Cart\CartItemCollection;
use Bixie\Cart\Helper\MailHelper;
use Bixie\Cart\Model\Order;
use Bixie\Cart\Payment\PaymentException;
use Bixie\Cart\Cart\CartItem;
use Pagekit\Event\Event;
use Pagekit\User\Model\User;
use Pagekit\Application\Exception;
use Pagekit\Util\Arr;

/**
 * @Route("cart", name="cart")
 */
class CartApiController {
    /**
     * @var \Bixie\Cart\CartModule
     */
    protected $cart;

    /**
     * Constructor.
     */
    public function __construct () {
        $this->cart = App::module('bixie/cart');
    }

    /**
     * @Route("/", methods="POST")
     * @Request({"cart": "array"}, csrf=true)
     * @return array
     */
    public function saveAction ($cart) {
        /** @var CartItemCollection $bixieCart */
        /** @var CartItem $cartItem */
        $bixieCart = App::bixieCart();
        $this->saveCartItems($cart['items']);

        try {
            $handler = new CartHandler($bixieCart);
            $deliveryHelper = new DeliveryHelper($handler);

            $handler->setDeliveryAddress($cart['delivery_address']);
            $deliveryOptions = $deliveryHelper->getProviderDeliveryOptions();

            $payment_options = App::bixiePayment()->activeGatewaysData();

            return array_merge($cart, [
                'delivery_address' => $handler->getDeliveryAddress(),
                'deliveryOptions' => $deliveryOptions,
                'payment_options' => $payment_options,
                'items' => array_values($bixieCart->all()),
            ]);
        } catch (CartException $e) {
            return App::abort(400, $e->getMessage());
        }
    }

    /**
     * @Route("/checkout", methods="POST")
     * @Request({"cart": "array", "order_data": "array", "user_data": "array"}, csrf=true)
     */
    public function checkoutAction ($cart, $order_data, $user_data = []) {
        $checkout = ['error' => false, 'registererror' => '', 'errormessage' => '', 'redirect_url' => ''];
        $bixieCart = App::bixieCart();
        $cartItems = $this->saveCartItems($cart['items']);

        //register user?
        if (empty($userData['id']) && !empty($userData['username'])) {

//            try {
//
//                $userData['name'] = $checkout['billing_address']['firstName'] . ' ' . $checkout['billing_address']['lastName'];
//                $userData['email'] = $checkout['billing_address']['email'];
//
//                $userHelper = new UserHelper();
//                $userHelper->createUser($userData, User::STATUS_ACTIVE);
//                $userHelper->login(['username' => $userData['username'], 'password' => $userData['password']]);//sessionkey todo
//
//            } catch (Exception $e) {
//                $checkout['error'] = true;
//            $checkout['registererror'] = $e->getMessage();
//                return $checkout;
//            }
//
        }
        try {

            $handler = (new CartHandler($bixieCart))
                ->setDeliveryAddress($cart['delivery_address'])
                ->setBillingAddress($cart['delivery_address']); //todo

            $deliveryHelper = new DeliveryHelper($handler);
            if (!$deliveryOption = $deliveryHelper->getDeliveryOption($cart['delivery_option_id'])) {
                throw new CartException(__('Invalid delivery option!'));
            }

            if (!$paymentOption =  App::bixiePayment()->getPaymentOption($cart['payment_option_name'])) {
                throw new CartException(__('Invalid payment option!'));
            }

            $handler->setDeliveryOption($deliveryOption)
                ->setPaymentOption($paymentOption);

            /** @var Order $order */
            if (!empty($order_data['id'])) {
                $order = Order::find($order_data['id']);
                $order->currency = $handler->getCurrency();
                $order->ext_key = $order_data['ext_key'];
                $order->reference = $order_data['reference'];
                $order->cartItemsData = $cartItems;
                $order->data = array_merge((array)$order->data, $order_data['data'], $handler->getOrderData());
            } else {
                $order = Order::createFromHandler($handler, $order_data);
            }

            foreach ($order->getCartItems() as $cartItem) {
                $event = new Event('bixie.cartitem.ordered');
                App::trigger($event, [$this, $cartItem]);
            }

            /** @var OrderCalculator $calculator */
            $calculator = App::cartCalculator($order);
            $order->total_netto = $calculator->total_netto;
            $order->total_bruto = $calculator->total_bruto;

            /** @var \Omnipay\Common\Message\ResponseInterface $paymentResponse */
            $paymentResponse = App::bixiePayment()->getPayment($handler, $order, $cart['card']);

            $checkout['redirect_url'] = App::url('@cart/checkout/paymentreturn', ['transaction_id' => $order->transaction_id], true);

            if ($paymentResponse->isRedirect()) {
                $checkout['redirect_url'] = $paymentResponse->getRedirectUrl();
            }

            if ($paymentResponse->isSuccessful()) {

                $order->status = Order::STATUS_CONFIRMED;

                //reset cart
                App::bixieCart()->reset();
                $cartItems = [];


                try {

                    App::trigger('bixie.cart.payment.confirmed', [$order, $calculator]);

                } catch (CartException $e) {
                    //todo create events on order
                    $order->set('error', $e->getMessage());
                }

            }

            $order->payment = $paymentResponse->getData();
            $order->save();


        } catch (Exception $e) {
            $checkout['error'] = true;
            $checkout['errormessage'] = $e->getMessage();
        }
        return [
            'cartItems' => array_values($cartItems),
            'checkout' => $checkout,
            'order' => $order ?: []
        ];


    }

    /**
     * @Route("/terms", methods="GET")
     */
    public function termsAction () {
        $title = $this->cart->config('terms.title');
        $content = App::content()->applyPlugins($this->cart->config('terms.content'), ['markdown' => $this->cart->config('terms.markdown_enabled')]);;
        return ['html' => App::view('bixie/cart/templates/terms.php', compact('content', 'title'), 'raw')];
    }

    /**
     * @param $cartItems
     * @return CartItem[]
     */
    protected function saveCartItems ($cartItems) {
        /** @var CartItemCollection $bixieCart */
        /** @var CartItem $cartItem */
        $bixieCart = App::bixieCart();
        $ids = [];
        foreach ($cartItems as $cartData) {
            if ($cartItem = $bixieCart->addItemFromData($cartData)) {
                $ids[] = $cartItem->getId();
            }
        }
        $bixieCart->remove(array_diff($bixieCart->ids(), $ids));
        return $bixieCart->all();
    }

}
