<?php

namespace Bixie\Cart\Event;

use Bixie\Cart\Model\Product;
use Pagekit\Application as App;
use Pagekit\Event\EventSubscriberInterface;
use Bixie\Download\Model\File;
use Pagekit\View\View;

class FileListener implements EventSubscriberInterface {

	protected $request;

	public function onProductView ($event, View $view) {
		$product = new \stdClass();
		$id = $event['file']->id;
		if (!empty($id)) {

			if (!$product = Product::where(['item_id = ?', 'item_model = ?'], [$id, 'Bixie\Download\Model\File'])->first()) {

				$product = Product::createNew([
					'item_id' => $id,
					'item_model' => 'Bixie\Download\Model\File'
				]);
			}
		}

		$view->data('$cart', [
			'product' => $product,
			'config' => App::module('bixie/cart')->config()
		]);
	}

	public function onProductPrepare ($event, File $file, View $view) {
		static $products;
		if (!$products) {
			$products = [];
		}

		if (!empty($file->id) && $file->get('cart_active')) {
//			$viewProducts = $view['$cart.products'];
			if (!$product = Product::where(['item_id = ?', 'item_model = ?'], [$file->id, 'Bixie\Download\Model\File'])->first()) {

				$product = Product::createNew([
					'item_id' => $file->id,
					'item_model' => 'Bixie\Download\Model\File'
				]);
			}
			$file->product = $product;
			$products[$file->id] = $product;
			$view->script('bixie-addtocart', 'bixie/cart:app/bundle/bixie-addtocart.js', ['vue', 'bixie-cart']);
			$view->data('$cart', [
				'products' => $products,
				'config' => App::module('bixie/cart')->config()
			]);
		}
	}

	public function onProductChange ($event, File $file) {
		$data = App::request()->request->get('product', []);

		if (!empty($data)) {
			// is new ?
			if (!$product = Product::find($data['id'])) {

				if ($data['id']) {
					App::abort(404, __('Product not found.'));
				}

				$product = Product::createNew($data);
			}

			$product->save($data);

			$file->product = $product;
		}
	}

	public function onProductDeleted ($event, File $file) {
		foreach (Product::where(['item_id = :id', 'item_model = :item_model'], [':id' => $file->id, ':item_model' => 'Bixie\Download\Model\File'])->get() as $product) {
			$product->delete();
		}
	}


	/**
	 * {@inheritdoc}
	 */
	public function subscribe () {
		return [
			'view.bixie/download/admin/file' => 'onProductView',
			'bixie.prepare.file' => 'onProductPrepare',
			'model.file.saved' => 'onProductChange',
			'model.file.deleted' => 'onProductDeleted'
		];
	}
}
