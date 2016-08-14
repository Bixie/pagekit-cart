<?php

namespace Bixie\Cart\Helper;

use Bixie\Cart\CartModule;
use Bixie\Cart\Payment\PaymentException;
use Pagekit\Application as App;
use Bixie\Cart\Model\Order;
use Pagekit\Event\Event;

class MailHelper {

	/**
	 * @var CartModule
	 */
	protected $cart;

	/**
	 * @var Order
	 */
	protected $order;

	/**
	 * MailHelper constructor.
	 * @param Order $order
	 */
	public function __construct (Order $order) {
		$this->order = $order;
		$this->cart = App::module('bixie/cart');
	}

	/**
	 * @return string
	 * @throws PaymentException
	 */
	public function sendMail () {

		$mailSubject = $this->replaceString($this->cart->config('email.subject'));
		$mailBody = $this->replaceString($this->cart->config('email.body'));
		$mailBody = App::content()->applyPlugins($mailBody, ['order' => $this->order, 'markdown' => $this->cart->config('email.markdown_enabled')]);
		$mailBody .= App::view('bixie/cart/mails/order_confirmation.php', ['cart' => $this->cart, 'order' => $this->order]);
		$mailTemplate = App::view('bixie/cart/mails/template.php', compact('mailBody'));
		try {

			if ($adminMail = $this->cart->config('email.admin_email')) {
				$mail = App::mailer()->create();
				$mail->setTo($adminMail)->setSubject($mailSubject)->setBody($mailTemplate, 'text/html')->send();
			}

			if ($this->order->email) {

				$mail = App::mailer()->create();
				$mail->setTo($this->order->email)->setSubject($mailSubject)->setBody($mailTemplate, 'text/html')->send();
			}

		} catch (\Exception $e) {
			throw new PaymentException(__('Unable to send confirmation mail.'));
		}

	}

	public function replaceString ($string) {
		$billing_fields = $this->order->get('billing_address');
		$string = preg_replace_callback('/\$\$(.+?)\$\$/is', function($matches) use ($billing_fields) {
			$placeholder = $matches[1];
			if (property_exists($this->order, $placeholder)) {
				return $this->order->$placeholder;
			}
			if ($this->order->get($placeholder) !== null) {
				return $this->order->get($placeholder);
			}
			if (isset($billing_fields[$placeholder])) {
				return $billing_fields[$placeholder];
			}
			return '';
		}, $string);

		return $string;
	}

}