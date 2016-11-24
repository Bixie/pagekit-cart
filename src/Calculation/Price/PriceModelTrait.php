<?php

namespace Bixie\Cart\Calculation\Price;


use Bixie\Cart\Calculation\Prop\PropValueCollection;

trait PriceModelTrait {

    /**
     * @Column(type="json_array")
     * @var PriceCollection[]
     */
    public $prices;

    /**
     * @return bool
     */
    public function hasPrices () {
        return count($this->getPrices()) > 0;
    }

    /**
     * @return PriceCollection[]
     */
    public function getPrices () {
        foreach ($this->prices as $hash => $price_collection) {
            if (!$price_collection instanceof PriceCollection) {
                $this->prices[$hash] = new PriceCollection(
                    new PropValueCollection($price_collection['propValues']),
                    $price_collection['prices']
                );
            }
        }
        return $this->prices;
    }

    /**
     * @param $hash
     * @return PriceCollection|null
     */
    public function getPrice ($hash) {
        $this->getPrices();
        return isset($this->prices[$hash]) ? $this->prices[$hash] : null;
    }

    /**
     * @return QuantityCollection
     */
    public function getQuantityData () {
        $quantity_data = new QuantityCollection();
        foreach ($this->getPrices() as $priceCollection) {
            $quantity = new Quantity([
                'hash' => $priceCollection->propValues->hash(),
                'type' => 'piece',
                'quantities' => []
            ]);
            foreach ($priceCollection as $price) {
                if ($price->min_quantity > 1) {
                    $quantity->type = $price->min_quantity == $price->max_quantity ? 'total' : 'piece';
                }
                $quantity->quantities[] = array_merge($price->toArray(), [
                    'vat' => $this->get('vat'),
                ]);
            }
            $quantity_data[$quantity->hash] = $quantity;
        }

        return $quantity_data;
    }


}