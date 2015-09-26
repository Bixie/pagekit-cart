<?php

namespace Bixie\Cart\Model;

use Bixie\Cart\Cart\CartItem;
use Pagekit\Application as App;
use Pagekit\Database\ORM\ModelTrait;

trait OrderModelTrait
{
    use ModelTrait;

	/**
	 * @param CartItem $cartItem
	 * @return array
	 */
	protected function calcVat (CartItem $cartItem) {

		$netto = $this->convertPrice($cartItem->price, $cartItem);
		$vatclass = App::module('bixie/cart')->config('vatclasses.' . $cartItem->vat);
		$bruto = (round(($netto * 100) * (($vatclass['rate'] / 100) + 1))) / 100;

		return [
			'netto' => $netto,
			'bruto' => $bruto,
			'vat' => $bruto - $netto,
			'vatclass' => $vatclass
		];
	}

	/**
	 * @param float $price
	 * @param CartItem|null $cartItem
	 * @return float
	 */
	protected function convertPrice ($price, CartItem $cartItem = null) {

		if ($cartItem && $cartItem->currency !== $this->currency) {
			$factor = App::module('bixie/cart')->config($cartItem->currency . 'to' . $this->currency);
			$price = (round(($price * 100) * ($factor ? : 1))) / 100;
        }
		return $price;
	}

    /**
     * @Saving
     */
    public static function saving($event, Order $order)
    {


    }

}
