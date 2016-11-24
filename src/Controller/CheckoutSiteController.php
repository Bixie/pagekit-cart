<?php

namespace Bixie\Cart\Controller;

use Bixie\Cart\Calculation\OrderCalculator;
use Bixie\Cart\Helper\MailHelper;
use Bixie\Cart\Helper\UserHelper;
use Bixie\Cart\Model\Order;
use Pagekit\Application as App;
use Pagekit\Event\Event;
use Bixie\Cart\Payment\PaymentException;

class CheckoutSiteController
{
    /**
     * @var \Bixie\Cart\CartModule
     */
    protected $cart;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->cart = App::module('bixie/cart');
    }

	/**
	 * @Route("/")
	 */
	public function indexAction()
	{

		return [
			'$view' => [
				'title' => __('Checkout'),
				'name' => 'bixie/cart/checkout.php'
			],
			'$cart' => [
				'countries' => App::module('system/intl')->getCountries(),
				'gateways' => App::bixiePayment()->activeGatewaysData(),
				'config' => App::module('bixie/cart')->publicConfig()
			],
			'cart' => $this->cart,
			'node' => App::node()
		];
	}

    /**
     * @Route("/terms", methods="GET")
     */
    public function termsAction () {

        $title = $this->cart->config('terms.title');
        $content = App::content()->applyPlugins($this->cart->config('terms.content'), ['markdown' => $this->cart->config('terms.markdown_enabled')]);;

        return [
            '$view' => [
                'title' => $title,
                'name' => 'bixie/cart/templates/terms.php'
            ],
            'title' => $title,
            'content' => $content
        ];
    }

    /**
	 * @Route("/paymentreturn")
	 * @Request({"transaction_id": "string"})
	 */
	public function paymentreturnAction($transaction_id = '')
	{

		if ($transaction_id != App::session()->get('_bixiePayment.transaction_id')) {
			App::abort(401, __('Invalid request.'));
		}

		/** @var Order $order */
		if (!$order = Order::findByTransaction_id($transaction_id)) {
			App::abort(404, __('Order not found.'));
		}

        /** @var OrderCalculator $calculator */
        $calculator = App::cartCalculator($order);

        //check payment
		if ($order->status != Order::STATUS_CONFIRMED && $payment_method = App::session()->get('_bixiePayment.redirected')) {
			try {

                /** @var \Omnipay\Common\Message\ResponseInterface $paymentResponse */
                $paymentResponse = App::bixiePayment()->getReturn($payment_method, $order);

				if ($paymentResponse->isSuccessful()) {

					$order->status = Order::STATUS_CONFIRMED;

					//reset cart
					App::bixieCart()->reset();

					//send mail
//					(new MailHelper($order))->sendMail();
                    App::trigger('bixie.cart.payment.confirmed', [$order, $calculator]);

					App::message()->success(__('Payment successful'));

				}

				$order->payment = $paymentResponse->getData();
				$order->save();

				if ($paymentResponse->isRedirect()) {
					return [
						'$view' => [
							'title' => __('Please complete your payment'),
							'name' => 'bixie/cart/paymentredirect.php'
						],
						'title' => __('Please complete your payment'),
						'order' => $order,
						'response' => $paymentResponse,
						'cart' => $this->cart
					];
				}

			} catch (PaymentException $e) {
				//todo message does not persist??
				App::message()->error(__($e->getMessage()));
				return App::redirect('@cart/checkout');

			}


		}

		$content = '';
		if ($this->cart->config('thankyou.content')) {
			$content = App::content()->applyPlugins($this->cart->config('thankyou.content'), ['markdown' => $this->cart->config('thankyou.markdown_enabled')]);
			$content = (new MailHelper($order))->replaceString($content);
		}

        /** @var \Bixie\Invoicemaker\InvoicemakerModule $invoicemaker */
        $invoices = [];
        if ($invoicemaker = App::module('bixie/invoicemaker')) {
            $invoices = $invoicemaker->getByExternKey('game2art.order.' . $order->id);
        }

        $orderItems = $calculator->getItems();
        foreach ($orderItems as $orderItem) {
            $event = new Event('bixie.cart.admin.orderitem');
            App::trigger($event, [$order, $orderItem->getItem()]);
            $orderItem->getItem()->setTemplate('bixie.cart.order_item', $event['bixie.cart.order_item'] ? : '');
        }

        return [
			'$view' => [
				'title' => $this->cart->config('thankyou.title'),
				'name' => 'bixie/cart/paymentreturn.php'
			],
            '$data' => [
                'order' => $order
            ],
			'title' => $this->cart->config('thankyou.title'),
			'content' => $content,
            'invoices' => $invoices,
			'order' => $order,
            'calculator' => $calculator,
            'orderItems' => $orderItems,
			'cart' => $this->cart,
			'node' => App::node()
		];
	}

}