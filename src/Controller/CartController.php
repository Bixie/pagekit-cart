<?php

namespace Bixie\Cart\Controller;

use Bixie\Cart\Calculation\OrderCalculator;
use Bixie\Cart\Model\Order;
use Pagekit\Application as App;
use Pagekit\Event\Event;

/**
 * @Access(admin=true)
 */
class CartController {
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
     * @Route("/", name="order")
     * @Request({"filter": "array", "page":"int"})
     */
    public function indexAction ($filter = [], $page = null) {
        return [
            '$view' => [
                'title' => __('Orders'),
                'name' => 'bixie/cart/admin/orders.php'
            ],
            '$data' => [
                'statuses' => Order::getStatuses(),
                'config' => [
                    'filter' => (object)$filter,
                    'page' => $page
                ]
            ]
        ];
    }

    /**
     * @Route("/order/edit", name="order/edit")
     * @Access("bixie/cart: manage orders")
     * @Request({"id": "int"})
     */
    public function editAction ($id = 0) {
        /** @var Order $order */
        if (!$order = Order::where(compact('id'))->related('user')->first()) {

            if ($id) {
                App::abort(404, __('Invalid file id.'));
            }

            App::abort(401, __('Orders cannot be created.'));

        }

        //todo replace by related-item picker
        $users = App::db()->createQueryBuilder()
            ->from('@system_user')
            ->execute('id, username')
            ->fetchAll();

        /** @var \Bixie\Invoicemaker\InvoicemakerModule $invoicemaker */
        $invoices = [];
        if ($invoicemaker = App::module('bixie/invoicemaker')) {
            $invoices = $invoicemaker->getByExternKey('game2art.order.' . $order->id);
        }

        $templates = [];
        /** @var \Bixie\Emailsender\EmailsenderModule $emailsender */
        if ($emailsender = App::module('bixie/emailsender')) {
            $templates = array_values($emailsender->loadTexts('bixie.cart.admin.order.'));
        }

        /** @var OrderCalculator $calculator */
        $calculator = App::cartCalculator($order);

        $orderItems = $calculator->getItems();
        foreach ($orderItems as $orderItem) {
            $event = new Event('bixie.cart.admin.orderitem');
            App::trigger($event, [$order, $orderItem->getItem()]);
            $orderItem->getItem()->setTemplate('bixie.cart.admin.order', $event['bixie.cart.admin.order'] ?: '');
            $orderItem->getItem()->setTemplate('bixie.cart.order_item', $event['bixie.cart.order_item'] ?: '');
        }

        return [
            '$view' => [
                'title' => __('Edit order'),
                'name' => 'bixie/cart/admin/order.php'
            ],
            '$data' => [
                'statuses' => Order::getStatuses(),
                'users' => $users,
                'templates' => $templates,
                'config' => $this->cart->config(),
                'order' => $order
            ],
            'invoices' => $invoices,
            'cart' => $this->cart,
            'calculator' => $calculator,
            'orderItems' => $orderItems,
            'order' => $order,
        ];

    }

    /**
     * @Access("system: access settings")
     */
    public function settingsAction () {
        $config = $this->cart->config();
        $config['gateways'] = App::bixiePayment()->getSettings();

        /** @var \Bixie\Invoicemaker\InvoicemakerModule $invoicemaker */
        $invoicemaker = App::module('bixie/invoicemaker');

        return [
            '$view' => [
                'title' => __('Cart Settings'),
                'name' => 'bixie/cart/admin/settings.php'
            ],
            '$data' => [
                'config' => $config,
                'invoicemaker_templates' => $invoicemaker ? $invoicemaker->getTemplates() : [],
                'invoicemaker_groups' => $invoicemaker ? $invoicemaker->getInvoiceGroups() : [],
                'timezones' => $this->cart->getTimeZomes()
            ],
            'gateways' => App::bixiePayment()->getGateways()
        ];
    }
}
