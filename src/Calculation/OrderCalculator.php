<?php


namespace Bixie\Cart\Calculation;


use Pagekit\Application as App;
use Bixie\Cart\Calculation\Price\CalculationObject;
use Bixie\Cart\Calculation\Vat\VatObject;
use Bixie\Cart\Model\Order;

class OrderCalculator {

    /**
     * @var Order
     */
    protected $order;
    /**
     * @var ItemCalculator[]
     */
    protected $items = [];
    /**
     * @var CalculationObject[]
     */
    protected $items_objects = [];
    /**
     * @var CalculationObject[]
     */
    protected $delivery_objects = [];
    /**
     * @var CalculationObject[]
     */
    protected $payment_objects = [];
    /**
     * @var CalculationObject[]
     */
    protected $coupon_objects = [];
    /**
     * @var VatObject[]
     */
    protected $vat_objects = [];
    /**
     * @var float
     */
    protected $conversion_factor = 1;
    /**
     * @var array
     */
    protected $vat_classes;

    /**
     * @var float
     */
    public $items_netto = 0;

    /**
     * @var float
     */
    public $delivery_netto = 0;

    /**
     * @var float
     */
    public $payment_netto = 0;

    /**
     * @var float
     */
    public $total_netto = 0;

    /**
     * @var float
     */
    public $total_bruto = 0;

    /**
     * @var float
     */
    public $total_vat = 0;

    /**
     * @var string
     */
    public $currency = 'EUR';

    /**
     * OrderCalculator constructor.
     * @param Order $order
     * @param array $config
     */
    public function __construct (Order $order, $config) {
        $this->order = $order;

        foreach ($order->getCartItems() as $item) {
            $this->items[$item->getId()] = new ItemCalculator($this, $item);
        }
        $this->currency = $this->order->currency;
        $conversion_key = ($this->currency == 'EUR' ? 'USD' : 'EUR') . 'to' . $this->currency;
        $this->conversion_factor = $config[$conversion_key];
        $this->vat_classes = $config['vatclasses'];
    }

    /**
     * @return Order
     */
    public function getOrder () {
        return $this->order;
    }

    /**
     * @return ItemCalculator[]
     */
    public function getItems () {
        return $this->items;
    }

    /**
     * @return CalculationObject[]
     */
    public function getCalcObjects () {
        return array_merge($this->items_objects, $this->delivery_objects, $this->payment_objects);
    }

    /**
     * @return CalculationObject[]
     */
    public function getItemsObjects () {
        return $this->items_objects;
    }

    /**
     * @return CalculationObject[]
     */
    public function getDeliveryObjects () {
        return $this->delivery_objects;
    }

    /**
     * @return CalculationObject[]
     */
    public function getPaymentObjects () {
        return $this->payment_objects;
    }

    /**
     * @return CalculationObject[]
     */
    public function getCouponObjects () {
        return $this->coupon_objects;
    }

    /**
     * @return Vat\VatObject[]
     */
    public function getVatObjects () {
        return $this->vat_objects;
    }

    /**
     * @param string $vat_type
     * @return array
     */
    public function getVatClass ($vat_type) {
        return $this->vat_classes[$vat_type];
    }

    /**
     * @return $this
     */
    public function calculate () {
        $vat_calc = ['none' => 0, 'low' => 0, 'high' => 0];
        $this->items_netto = 0;
        foreach ($this->items as $item) {
            $calcObject = $item->getCalculationObject();
            $this->items_objects[] = $calcObject;

            $this->items_netto += $calcObject->netto;
            $vat_calc[$calcObject->vat_type] += $calcObject->netto * 100;
        }

        $this->delivery_netto = 0;
        if ($delivery_price = $this->order->get('delivery_option.price', 0)) {
            $calcObject = $this->getDeliveryCalculation();
            $this->delivery_objects[] = $calcObject;

            $this->delivery_netto += $calcObject->netto;
            $vat_calc['high'] += $calcObject->netto * 100;
        }

        $this->payment_netto = 0;
        if ($payment_price = $this->order->get('payment_option.price', 0)) {
            $calcObject = $this->getPaymentCalculation();
            $this->payment_objects[] = $calcObject;

            $this->payment_netto += $calcObject->netto;
            $vat_calc['high'] += $calcObject->netto * 100;
        }

        //todo coupon

        $this->total_netto = round($this->items_netto + $this->delivery_netto + $this->payment_netto, 2);

        $this->total_vat = 0;
        foreach (['none', 'low', 'high'] as $vat_type) {
            if ($vat_calc[$vat_type]) {
                $vatObject = $this->getVatCalculation($vat_type, $vat_calc[$vat_type]);
                $this->total_vat += $vatObject->amount;
                $this->vat_objects[] = $vatObject;
            }
        }

        $this->total_vat = round($this->total_vat) / 100;
        $this->total_bruto = $this->total_netto + $this->total_vat;

        return $this;
    }

    /**
     * @return CalculationObject
     */
    public function getDeliveryCalculation () {
        $option = $this->order->getDeliveryOption();
        $netto = $this->convertPrice($option->price, $option->currency);
        $vat = App::cartCalcVat($netto, 'high');

        return new CalculationObject([
            'description' => $option->title ?: __('Delivery costs'),
            'cost_price' => 0,
            'netto' => $netto,
            'vat' => $vat,
            'bruto' => $netto + $vat,
            'vat_rate' => $this->vat_classes['high']['rate'],
            'vat_type' => 'high',
            'currency' => $this->currency,
        ]);
    }

    /**
     * @return CalculationObject
     */
    public function getPaymentCalculation () {
        $option = $this->order->getPaymentOption();
        $netto = $this->convertPrice($option->price, $option->currency);
        $vat = App::cartCalcVat($netto, 'high');

        return new CalculationObject([
            'description' => $option->title ?: __('Payment costs'),
            'cost_price' => 0,
            'netto' => $netto,
            'vat' => $vat,
            'bruto' => $netto + $vat,
            'vat_rate' => $this->vat_classes['high']['rate'],
            'vat_type' => 'high',
            'currency' => $this->currency,
        ]);
    }

    /**
     * @param $vat_type
     * @param $base
     * @return VatObject
     */
    public function getVatCalculation ($vat_type, $base) {

        $amount = App::cartCalcVat($base, $vat_type);
        $vat_class = $this->vat_classes[$vat_type];

        return new VatObject([
            'description' => __('Vat %vat_label%', ['%vat_label%' => $vat_class['name']]),
            'details' => __('%vat_label%% of %base%', [
                '%base%' => App::module('bixie/cart')->formatMoney($base, $this->order->currency, true),
                '%vat_label%' => $vat_class['rate']
            ]),
            'base' => $base,
            'amount' => $amount,
            'netto' => $base / 100,
            'vat' => round($amount) / 100,
            'rate' => $vat_class['rate'],
            'type' => $vat_type,
            'currency' => $this->currency,
        ]);
    }

    /**
     * @param float $price
     * @param string $currency
     * @return float
     */
    public function convertPrice ($price, $currency) {

        if ($currency !== $this->currency) {
            $price = (round(($price * 100) * ($this->conversion_factor ? : 1))) / 100;
        }
        return $price;
    }


}