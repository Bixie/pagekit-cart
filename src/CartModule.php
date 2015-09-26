<?php

namespace Bixie\Cart;

use Bixie\Cart\Payment\PaymentHelper;
use Pagekit\Application as App;
use Pagekit\Module\Module;
use Bixie\Cart\Cart\CartFactory;

class CartModule extends Module {
	const CURRENCY_ICONS = [
		'EUR' => 'uk-icon-euro',
		'USD' => 'uk-icon-dollar'
	];

	const DATETIME_FORMATS = [
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
	 * @param integer|\DateTime|string $date
	 * @param string $format (fullDate, longDate, medium, mediumDate, mediumTime, short, shortDate, shortTime)
	 * @return string
	 */
	public function formatDate ($date, $format = 'medium') {
		try {

			if (is_numeric($date)) {
				$date = '@' . $date;
				$date = new \DateTime($date, new \DateTimeZone('UTC'));
			}
			if (is_string($date)) {
				$date = new \DateTime($date);
			}

			$formats = self::DATETIME_FORMATS;
//			$formats = App::module('system/intl')->getFormats(); //todo get form core
			return $date->format(isset($formats[$format]) ? $formats[$format] : $formats['medium']);

		} catch (\Exception $e) {
		    return 'Invalid date';
		}
	}

	public function formatMoney ($amount, $currency = 'EUR') {
		$icons = self::CURRENCY_ICONS;
		$icon = '<i class="' . $icons[$currency] . ' uk-margin-small-right"></i>';

		$formats = App::module('system/intl')->getFormats();
		$numberString = number_format($amount, 2, $formats['NUMBER_FORMATS']['DECIMAL_SEP'], $formats['NUMBER_FORMATS']['GROUP_SEP']);

		return $icon . $numberString;
	}

}
