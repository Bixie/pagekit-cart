<?php

namespace Bixie\Cart;

use Pagekit\Application as App;
use Bixie\Cart\Model\Order;
use Bixie\Cart\Payment\PaymentHelper;
use Bixie\Cart\Calculation\OrderCalculator;
use Bixie\Cart\Event\CartListener;
use Bixie\Cart\Event\UserListener;
use Pagekit\Module\Module;
use Bixie\Cart\Cart\CartItemCollection;

class CartModule extends Module {

	protected $currency_icons = [
		'EUR' => 'uk-icon-euro',
		'USD' => 'uk-icon-dollar'
	];

	protected $datetime_formats = [
		'full' => 'l, F j, Y h:i:s a',
		'fullDate' => 'l, F j, Y',
		'longDate' => 'F j, Y',
		'medium' => 'F j, Y h:i:s a',
		'mediumDate' => 'M j, Y',
		'mediumTime' => 'h:i:s a',
		'short' => 'n/j/Y h:i a',
		'shortDate' => 'n/j/Y',
		'shortTime' => 'h:i a'
	];

	/**
	 * @var array
	 */
	protected $types;

	/**
	 * {@inheritdoc}
	 */
	public function main (App $app) {
		$app['bixieCart'] = function ($app) {
			return (new CartItemCollection($app['session']))->loadFromSession();
		};
		$app['bixiePayment'] = function ($app) {
			return new PaymentHelper($app, $this);
		};

		$app['cartCalcVat'] = function ($app) {
			return function ($amount, $vat_class) use ($app) {
                $vatclass = $this->config('vatclasses.' . $vat_class);
                return $amount * ($vatclass['rate'] / 100);
            };
		};

        $app->factory('cartCalculator', function ($app) {
            return function (Order $order) {
                return (new OrderCalculator($order, $this->config))->calculate();
            };
        });

        $app->on('boot', function () use ($app) {
            $app->subscribe(
                new UserListener(),
                new CartListener()
            );
            //add mailtypes
            if (isset($app['emailtypes'])) {
                $app['emailtypes']->register([
                    'bixie.cart.admin.order.status' => [
                        'label' => 'Cart: Change orderstatus',
                        'classes' => [
                            'order' => 'Bixie\Cart\Model\Order'
                        ]
                    ],
                ]);
            }
        });
   }

    /**
     * @param string $transaction_id
     * @param string $product_identifier
     * @return string
     * @throws CartException
     */
	public function validateTransaction ($transaction_id, $product_identifier) {
		$purchaseKey = false;

		if (!$order = Order::findByTransaction_id($transaction_id)) {
			throw new CartException('Transaction not found');
		}

		foreach ($order->getCartItems() as $cartItem) {

			if ($cartItem->get('product_identifier') == $product_identifier) {
				$purchaseKey = $cartItem->purchaseKey($order);
			}

		}

		if (false === $purchaseKey) {
			throw new CartException('Product not found in order');
		}

		if ('' === $purchaseKey) {
			throw new CartException('Product not valid');
		}

		return $purchaseKey;
	}

	/**
	 * @param integer|\DateTime|string $date
	 * @param string                   $format (fullDate, longDate, medium, mediumDate, mediumTime, short, shortDate, shortTime)
	 * @param string                   $timezone
	 * @return string
	 */
	public function formatDate ($date, $format = 'medium', $timezone = null) {
		try {

			if (!$date) {
				return 'Invalid date';
			}

			if (is_numeric($date)) {
				$date = '@' . $date;
				$date = new \DateTime($date, new \DateTimeZone('UTC'));
			}
			if (is_string($date)) {
				$date = new \DateTime($date);
			}

			if (!$timezone) {
				$timezone = $this->config('server_tz', '');
			}
			$date->setTimezone(new \DateTimeZone($timezone));
			//todo locale!
//			$formats = App::module('system/intl')->getFormats(); //todo get from core
			return $date->format(isset($this->datetime_formats[$format]) ? $this->datetime_formats[$format] : $this->datetime_formats['medium']);

		} catch (\Exception $e) {
		    return 'Invalid date';
		}
	}

    /**
     * @param        $amount
     * @param string $currency
     * @param bool   $raw
     * @return string
     */
	public function formatMoney ($amount, $currency = 'EUR', $raw = false) {
        $symbols = ['EUR' => '€ ','USD' => '$'];
		$icon = '<i class="' . $this->currency_icons[$currency] . ' uk-margin-small-right"></i>';

		$formats = App::module('system/intl')->getFormats();
		$numberString = number_format($amount, 2, $formats['NUMBER_FORMATS']['DECIMAL_SEP'], $formats['NUMBER_FORMATS']['GROUP_SEP']);

		return ($raw ? $symbols[$currency] : $icon) . $numberString;
	}

	public function getTimeZomes () {
		$zones = \DateTimeZone::listIdentifiers();
		$locations = [];
		foreach ($zones as $zone) {
			$zone = explode('/', $zone); // 0 => Continent, 1 => City

			// Only use "friendly" continent names
			if ($zone[0] == 'Africa' || $zone[0] == 'America' || $zone[0] == 'Antarctica' || $zone[0] == 'Arctic' || $zone[0] == 'Asia' || $zone[0] == 'Atlantic' || $zone[0] == 'Australia' || $zone[0] == 'Europe' || $zone[0] == 'Indian' || $zone[0] == 'Pacific') {
				if (isset($zone[1]) != '') {
					$locations[$zone[0]][$zone[0] . '/' . $zone[1]] = str_replace('_', ' ', $zone[1]); // Creates array(DateTimeZone => 'Friendly name')
				}
			}
		}
		return $locations;
	}
    /**
     * public accessable config
     * @return array
     */
    public function publicConfig () {
        return array_intersect_key(static::config(), array_flip([
            'currency', 'vat', 'vat_view', 'required_checkout', 'vatclasses', 'USDtoEUR', 'EURtoUSD'
        ]));
    }

}
