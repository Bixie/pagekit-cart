<?php

namespace Bixie\Cart\Event;

use Pagekit\Application as App;
use Pagekit\Event\Event;
use Pagekit\Event\EventSubscriberInterface;
use Bixie\Cart\Calculation\OrderCalculator;
use Bixie\Cart\Model\Order;
use Bixie\Cart\Helper\InvoiceHelper;

class CartListener implements EventSubscriberInterface {


    public function onPaymentConfirmed (Event $event, Order $order, OrderCalculator $calculator) {

        InvoiceHelper::createInvoice($order, $calculator);

    }

    /**
     * {@inheritdoc}
     */
    public function subscribe () {
        return [
            'bixie.cart.payment.confirmed' => ['onPaymentConfirmed', 50],
        ];
    }
}
