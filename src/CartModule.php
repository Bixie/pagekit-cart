<?php

namespace Bixie\Cart;

use Bixie\Cart\Model\Order;
use Bixie\Cart\Payment\PaymentHelper;
use Pagekit\Application as App;
use Pagekit\Event\Event;
use Pagekit\Module\Module;
use Bixie\Cart\Cart\CartFactory;
use Pagekit\Application\Exception;

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
			return new CartFactory($app['session']);
		};
		$app['bixiePayment'] = function ($app) {
			return new PaymentHelper($app, $this);
		};

	}

	public function publicConfig () {
		$config = $this->config();
		unset($config['gateways']);
		return $config;
	}

	/**
	 * @param string $transaction_id
	 * @param string $product_identifier
	 * @return string
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

	public function formatMoney ($amount, $currency = 'EUR') {
		$icon = '<i class="' . $this->currency_icons[$currency] . ' uk-margin-small-right"></i>';

		$formats = App::module('system/intl')->getFormats();
		$numberString = number_format($amount, 2, $formats['NUMBER_FORMATS']['DECIMAL_SEP'], $formats['NUMBER_FORMATS']['GROUP_SEP']);

		return $icon . $numberString;
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

}

class CartException extends Exception {

	public function __construct($message = "", $code = 0, Exception $previous = null) {
		parent::__construct($message, $code, $previous);
	}

}
