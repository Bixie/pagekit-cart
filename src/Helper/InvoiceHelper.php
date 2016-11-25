<?php


namespace Bixie\Cart\Helper;

use Pagekit\Application as App;
use Bixie\Cart\Calculation\OrderCalculator;
use Bixie\Cart\Cart\CartAddress;
use Bixie\Cart\Model\Order;
use Bixie\Invoicemaker\InvoicemakerException;
use Pagekit\User\Model\User;


class InvoiceHelper {

    protected static $_symbols = [
        'EUR' => '&euro; ',
        'USD' => '$'
    ];

    /**
     * @param Order           $order
     * @param OrderCalculator $calculator
     */
    public static function createInvoice (Order $order, OrderCalculator $calculator) {
        $app = App::getInstance();
        $user = $order->user_id? $app['auth']->getUserProvider()->find($order->user_id) : User::create(['name' => __('Guest')]);
        $billing_address = new CartAddress($order->get('billing_address'));
        $config = $app->module('bixie/cart')->config();

        //get payment status/info
        if ($order->get('payment.success')) {
            $payment_info = __('Invoice has been paid via %method% with transaction ID %transaction_id%', [
                '%method%' => $order->get('payment.method_name'),
                '%transaction_id%' => $order->getPayment('id')
            ]);
        } else {
            $payment_info = __('Invoice has not been paid.');
        }

        try {

            $debtor = $app['invoicemaker.factory']->debtor([
                'company' => $billing_address->company_name,
                'name' => $billing_address->fullName(),
                'address_1' => $billing_address->address1,
                'address_2' => $billing_address->address2,
                'zip_code' => $billing_address->zipcode,
                'city' => $billing_address->city,
                'county' => $billing_address->county,
                'state' => $billing_address->state,
                'country' => $billing_address->country_code,
                'email' => $billing_address->email,
                'debtor_id' => $user->id,
                'debtor_vat' => '',
                'debtor_coc' => ''
            ]);

            $app['invoicemaker.factory']->create(
                $debtor,
                $app['invoicemaker.factory']->invoiceLines(InvoiceHelper::getInvoiceLines($calculator)),
                $config['invoicemaker_template'],
                $config['invoicemaker_group'],
                [
                    'amount' => $order->total_bruto,
                    'debtor_id' => $user->id,
                    'ext_key' => 'game2art.order.' . $order->id,
                    'internal_reference' => $order->transaction_id,
                    'payment_info' => $payment_info
                ]
            );

        } catch (InvoicemakerException $e) {
            //todo?
            $a = $e;
        }

    }

    /**
     * @param OrderCalculator $calculator
     * @return array
     */
    protected static function getInvoiceLines (OrderCalculator $calculator) {
        $invoice_lines = [];
        foreach ($calculator->getCalcObjects() as $calcObject) {
            $invoice_lines[] = [
                'type' => 'spec',
                'description' => $calcObject->description . ($calcObject->details ? ' (' . $calcObject->details . ')' : ''),
                'vat_perc' => $calcObject->vat_rate,
                'units' => $calcObject->quantity,
                'per_unit' => $calcObject->quantity ? self::format($calcObject->netto / $calcObject->quantity, $calculator->currency) : '',
                'amount' => self::format($calcObject->netto, $calculator->currency)
            ];
        }
        $invoice_lines[] = [
            'type' => 'sub',
            'description' => __('Subtotal'),
            'amount' => self::format($calculator->total_netto, $calculator->currency)
        ];
        foreach ($calculator->getVatObjects() as $vatObject) {
            $invoice_lines[] = [
                'type' => 'spec',
                'description' => $vatObject->description,
                'vat_perc' => $vatObject->rate,
                'base' => self::format($vatObject->netto),
                'amount' => self::format($vatObject->vat, $calculator->currency)
            ];
        }
        $invoice_lines[] = [
            'type' => 'total',
            'description' => __('Total invoice'),
            'amount' => self::format($calculator->total_bruto, $calculator->currency)
        ];

        return $invoice_lines;
    }
	/**
	 * @param        $number
	 * @param string $currency
	 * @return string
	 */
	public static function format ($number, $currency = 'EUR') {
		return self::$_symbols[$currency] . number_format($number, 2, ',', '.');
	}

}