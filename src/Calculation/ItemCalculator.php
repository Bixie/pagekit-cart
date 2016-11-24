<?php


namespace Bixie\Cart\Calculation;


use Pagekit\Application as App;
use Bixie\Cart\Calculation\Price\CalculationObject;
use Bixie\Cart\Calculation\Price\QuantityCollection;
use Bixie\Cart\Calculation\Prop\PropValueCollection;
use Bixie\Cart\Cart\CartItem;

class ItemCalculator {
    /**
     * @var OrderCalculator
     */
    protected $orderCalculator;
    /**
     * @var CartItem
     */
    protected $item;
    /**
     * @var QuantityCollection
     */
    protected $prices;
    /**
     * @var PropValueCollection
     */
    protected $propValues;

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
     * ItemCalculator constructor.
     * @param OrderCalculator                    $orderCalculator
     * @param CartItem $item
     */
    public function __construct (OrderCalculator $orderCalculator, CartItem $item) {
        $this->orderCalculator = $orderCalculator;
        $this->item = $item;
        $this->prices = $item->getQuantityPrices();
        $this->propValues = $item->getPropValues();
    }

    /**
     * @return CartItem
     */
    public function getItem () {
        return $this->item;
    }

    /**
     * @return CalculationObject
     */
    public function getCalculationObject () {

        if (!$quantity_prices = $this->prices[$this->propValues->hash()]) {
            throw new CalculationException(sprintf('No quantityprices found for %s, %s',
                $this->item->sku,
                $this->propValues->hash()
            ));
        }
        $quantity = false;
        foreach ($quantity_prices->quantities as $qanty) {
            if ($qanty['min_quantity'] <= $this->item->quantity && $qanty['max_quantity'] >= $this->item->quantity) {
                $quantity = $qanty;
                break;
            }
        }
        if (!$quantity) {
            throw new CalculationException(sprintf('No quantityprice found for %sku%, %hash%, Qty. %quantity%',
                $this->item->sku,
                $this->propValues->hash(),
                $this->item->quantity
            ));
        }

        $price = $quantity['price'] * ($quantity_prices->type === 'piece' ? $this->item->quantity : 1);

        $this->total_netto = $this->orderCalculator->convertPrice($price, $quantity['currency']);
        $this->total_vat = App::cartCalcVat($this->total_netto, $this->item->vat);
        $this->total_bruto = $this->total_netto + $this->total_vat;

        $props = [];
        foreach ($this->propValues->texts() as $label => $value) {
            $props[] = "$label: $value";
        }

        return new CalculationObject([
            'description' => $this->item->title,
            'details' => join(', ', $props),
            'quantity' => $this->item->quantity,
            'cost_price' => 0,
            'netto' => $this->total_netto,
            'vat' => $this->total_vat,
            'bruto' => $this->total_bruto,
            'vat_rate' => $this->orderCalculator->getVatClass($this->item->vat)['rate'],
            'vat_type' => $this->item->vat,
            'currency' => $this->orderCalculator->currency,
        ]);
    }


}