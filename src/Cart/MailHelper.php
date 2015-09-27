<?php

namespace Bixie\Cart\Cart;

use Bixie\Cart\CartModule;
use Pagekit\Application as App;
use Pagekit\Application\Exception;
use Bixie\Cart\Model\Order;

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
	 */
	public function sendMail () {

		$mailSubject = $this->replaceString($this->cart->config('email.subject'));
		$mailBody = $this->replaceString($this->cart->config('email.body'));
		$mailBody = App::content()->applyPlugins($mailBody, ['order' => $this->order, 'markdown' => $this->cart->config('markdown_enabled')]);
		$mailBody .= App::view('bixie/cart/mails/order_confirmation.php', ['cart' => $this->cart, 'order' => $this->order]);
		$mailTemplate = App::view('bixie/cart/mails/template.php', compact('mailBody'), 'text/html');
		try {

			if ($adminMail = $this->cart->config('email.admin_email')) {
				$mail = App::mailer()->create();
				$mail->setTo($adminMail)->setSubject($mailSubject)->setBody($mailTemplate)->send();
			}

			if ($this->order->email) {

				$mail = App::mailer()->create();
				$mail->setTo($this->order->email)->setSubject($mailSubject)->setBody($mailTemplate)->send();
			}

		} catch (\Exception $e) {
			throw new Exception(__('Unable to send confirmation mail.'));
		}

	}

	public function replaceString ($string) {
		$billing_flieds = $this->order->get('billing_address');
		$string = preg_replace_callback('/\$\$(.+?)\$\$/is', function($matches) use ($billing_flieds) {
			$placeholder = $matches[1];
			if (property_exists($this->order, $placeholder)) {
				return $this->order->$placeholder;
			}
			if (isset($billing_flieds[$placeholder])) {
				return $billing_flieds[$placeholder];
			}
			return '';
		}, $string);

		return $string;
	}

}