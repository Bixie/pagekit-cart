<?php

namespace Bixie\Cart\Controller;

use Pagekit\Application as App;
use Bixie\Cart\Model\Order;

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
	 * @Access("cart: manage orders")
	 */
	public function indexAction ($filter = [], $page = 0) {

		$query  = Order::query();
		$filter = array_merge(array_fill_keys(['status', 'order', 'limit'], ''), $filter);

		extract($filter, EXTR_SKIP);

		if (is_numeric($status)) {
			$query->where(['status' => (int) $status]);
		}

		if (!preg_match('/^(transaction_id|created|total_bruto)\s(asc|desc)$/i', $order, $order)) {
			$order = [1 => 'created', 2 => 'desc'];
		}

		$limit = (int) $limit ?: $this->cart->config('orders_per_page');
		$count = $query->count();
		$pages = ceil($count / $limit);
		$page  = max(0, min($pages - 1, $page));

		$orders = array_values($query->offset($page * $limit)->limit($limit)->orderBy($order[1], $order[2])->get());

		return compact('orders', 'pages', 'count');

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
				App::abort(404, __('Post not found.'));
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

}
