<?php


namespace Bixie\Cart\Payment;


use Pagekit\Application\Exception;

class PaymentException extends Exception {

	public function __construct($message = "", $code = 0, \Exception $previous = null) {
		parent::__construct("Payment error: " . $message, $code, $previous);
	}

}