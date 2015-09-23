<?php

namespace Bixie\Cart\Controller;

use Pagekit\Application as App;
use Bixie\Cart\Model\Product;

/**
 * @Route("product", name="product")
 */
class ProductApiController {

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
	public function indexAction ($filter = [], $page = 1) {

		$query  = Product::query();
		$filter = array_merge(array_fill_keys(['model', 'status', 'order', 'limit'], ''), $filter);

		extract($filter, EXTR_SKIP);

		if ($model) {
			$query->where(function ($query) use ($model) {
				$query->orWhere(['item_model = :model'], ['model' => $model]);
			});
		}

		if (is_numeric($status)) {
			$query->where(['status' => (int) $status]);
		}

		if (!preg_match('/^(item_id|price)\s(asc|desc)$/i', $order, $order)) {
			$order = [1 => 'item_model', 2 => 'desc'];
		}

		$limit = (int) $limit ?: $this->cart->config('products_per_page');
		$count = $query->count();
		$pages = ceil($count / $limit);
		$page  = max(0, min($pages - 1, $page));

		$products = array_values($query->offset($page * $limit)->limit($limit)->orderBy($order[1], $order[2])->get());

		return compact('products', 'pages', 'count');

	}

}
