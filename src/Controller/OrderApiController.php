<?php

namespace Bixie\Cart\Controller;

use Bixie\Cart\Cart\UserHelper;
use Bixie\Cart\CartException;
use Pagekit\Application as App;
use Bixie\Cart\Model\Order;
use Pagekit\Application\Exception;
use Pagekit\User\Model\User;

/**
 * @Route("order", name="order")
 */
class OrderApiController {

	/**
	 * @var \Bixie\Cart\CartModule
	 */
	protected $cart;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$this->cart = App::module('bixie/cart');
	}

	/**
	 * @Route("/", methods="GET")
	 * @Request({"filter": "array", "page": "int"})
	 */
	public function indexAction ($filter = [], $page = 0) {

		$query  = Order::query();
		$filter = array_merge(array_fill_keys(['search', 'status', 'user_id', 'order', 'limit'], ''), $filter);

		extract($filter, EXTR_SKIP);

		$user = App::user();
		if (!$user->id) {
			App::abort(403, __('Access denied.'));
		}
		if (!$user->hasAccess('cart: manage orders')) {
			$user_id = $user->id;
		}

		if (is_numeric($user_id)) {
			$query->where(['user_id' => (int) $user_id]);
		}

		if (is_numeric($status)) {
			$query->where(['status' => (int) $status]);
		}

		if ($search) {
			$query->where(function ($query) use ($search) {
				$query->orWhere(['transaction_id LIKE :search', 'email LIKE :search'], ['search' => "%{$search}%"]);
			});
		}

		if (!preg_match('/^(transaction_id|created|email|total_bruto)\s(asc|desc)$/i', $order, $order)) {
			$order = [1 => 'created', 2 => 'desc'];
		}

		$limit = (int) $limit ?: $this->cart->config('orders_per_page');
		$count = $query->count();
		$pages = ceil($count / $limit);
		$page  = max(0, min($pages - 1, $page));

		$orders = array_values($query->offset($page * $limit)->related('user')->limit($limit)->orderBy($order[1], $order[2])->get());

		return compact('orders', 'pages', 'count');

	}

	/**
	 * @Route("/findorder", methods="POST")
	 * @Request({"transaction_id": "string", "email": "string"}, csrf=true)
	 * @param $transaction_id
	 * @param $email
	 * @return array
	 */
	public function findorderAction($transaction_id, $email)
	{

		if ($order = Order::where('transaction_id = ? AND email = ?', [$transaction_id, $email])->first()) {

			if ($order->user_id) {
				return ['error' => 'The order is already associated with a user. Try a password reset.'];
			}

			if ($user = User::findByEmail($email)) {
				return ['error' => 'A user with that email address already exists'];
			}

			App::session()->set('_bixieCart.findorder.active', md5($transaction_id . $email . $order->id));

			return ['success' => true];

		}

		return ['error' => 'No order found on this transaction ID and email address'];
	}

	/**
	 * @Route("/register", methods="POST")
	 * @Request({"transaction_id": "string", "user": "array"}, csrf=true)
	 * @param string $transaction_id
	 * @param array $data
	 * @return array
	 */
	public function registerAction($transaction_id, $data)
	{
		if ($order = Order::where('transaction_id = ? AND email = ?', [$transaction_id, $data['email']])->first()) {

			if ($order->email != $data['email'] ||
				md5($transaction_id . $data['email'] . $order->id) != App::session()->get('_bixieCart.findorder.active')) {
				App::abort(401, __('Invalid request.'));
			}

			try {

				$data['name'] = $order->get('billing_address.firstName') . ' ' . $order->get('billing_address.lastName');

				$user = (new UserHelper())->createUser($data, User::STATUS_BLOCKED);

					//send mail
				$this->sendActivationMail($user, $order);

				//attach user to orders
				foreach (Order::where('user_id = 0 AND email = ?', [$user->email])->get() as $userOrder) {
					$userOrder->user_id = $user->id;
					$userOrder->save();
				}

				App::session()->remove('_bixieCart.findorder.active');

				return ['success' => true];

			} catch (Exception $e) {

				return ['error' => $e->getMessage()];

			}

		}

		return ['error' => 'No order found on this transaction ID and email address'];
	}

	/**
	 * @Route("/{id}", methods="GET", requirements={"id"="\d+"})
	 * @Access("cart: manage orders")
	 */
	public function getAction($id)
	{
		return Order::where(compact('id'))->first();
	}

	/**
	 * @Route("/", methods="POST")
	 * @Route("/{id}", methods="POST", requirements={"id"="\d+"})
	 * @Request({"order": "array", "id": "int"}, csrf=true)
	 * @Access("cart: manage orders")
	 */
	public function saveAction($data, $id = 0)
	{
		if (!$id || !$order = Order::find($id)) {

			if ($id) {
				App::abort(404, __('Order not found.'));
			}

			App::abort(401, __('Orders cannot be created.'));
		}



		$order->save($data);

		return ['message' => 'success', 'order' => $order];
	}

	/**
	 * @Route("/{id}", methods="DELETE", requirements={"id"="\d+"})
	 * @Request({"id": "int"}, csrf=true)
	 * @Access("cart: manage orders")
	 */
	public function deleteAction($id)
	{
		if ($post = Order::find($id)) {


			$post->delete();
		}

		return ['message' => 'success'];
	}

	/**
	 * @Route("/bulk", methods="POST")
	 * @Request({"orders": "array"}, csrf=true)
	 */
	public function bulkSaveAction($orders = [])
	{
		foreach ($orders as $data) {
			$this->saveAction($data, isset($data['id']) ? $data['id'] : 0);
		}

		return ['message' => 'success'];
	}

	/**
	 * @Route("/bulk", methods="DELETE")
	 * @Request({"ids": "array"}, csrf=true)
	 */
	public function bulkDeleteAction($ids = [])
	{
		foreach (array_filter($ids) as $id) {
			$this->deleteAction($id);
		}

		return ['message' => 'success'];
	}

	/**
	 * @Route("/validateTransaction", methods="POST")
	 * @Request({"transaction_id": "string", "product_identifier": "string", "seal": "string"})
	 * @param string $transaction_id
	 * @param string $product_identifier
	 * @param string $seal
	 * @return array
	 */
 	public function validateTransactionAction ($transaction_id, $product_identifier, $seal) {
		//check seal
		if ($seal !== sha1($transaction_id . $product_identifier . $this->cart->config('validation_key'))) {
			//todo return proper 400
			App::abort(400, 'Seal not valid');
		}

		try {

			$purchaseKey = $this->cart->validateTransaction($transaction_id, $product_identifier);

			$valid = true;
			$message = 'Validation successful';

		} catch (CartException $e) {

			$purchaseKey = '';
			$valid = false;
			$message = $e->getMessage();
		}

		return ['valid' => $valid, 'purchaseKey' => $purchaseKey, 'message' => $message];
	}
	

	protected function sendActivationMail (User $user, Order $order) {

		$mailSubject = __('Your account at %site%', ['%site%' => App::module('system/site')->config('title')]);
		$mailBody = App::view('bixie/cart/mails/account_registration.php', ['cart' => $this->cart, 'user' => $user, 'order' => $order]);
		$mailTemplate = App::view('bixie/cart/mails/template.php', compact('mailBody'));

		if ($adminMail = $this->cart->config('email.admin_email')) {
			$mail = App::mailer()->create();
			$mail->setTo($adminMail)->setSubject($mailSubject)->setBody($mailTemplate, 'text/html')->send();
		}

		$mail = App::mailer()->create();
		$mail->setTo($user->email)->setSubject($mailSubject)->setBody($mailTemplate, 'text/html')->send();

	}

}
