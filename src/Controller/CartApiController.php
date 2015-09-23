<?php

namespace Bixie\Cart\Controller;

use Bixie\Cart\Cart\CartFactory;
use Pagekit\Application as App;
use Bixie\Cart\Cart\CartItem;

/**
 * @Route("cart", name="cart")
 */
class CartApiController
{

    /**
     * @Route("/", methods="POST")
     * @Request({"cartItems": "array"}, csrf=true)
     */
    public function saveAction($data)
    {
		/** @var CartFactory $bixieCart */
		/** @var CartItem $cartItem */
		$bixieCart = App::bixieCart()->loadFromSession();
		$ids = [];
		foreach ($data as $cartData) {
			if ($cartItem = $bixieCart->load($cartData)) {
				$ids[] = $cartItem->getId();
				$bixieCart[$cartItem->getId()] = $cartItem;
			}
		}
		foreach(array_diff($bixieCart->ids(), $ids) as $id) {
			unset($bixieCart[$id]);
		}

		$bixieCart->saveCartItems();

		return array_values($bixieCart->all());
    }

    /**
     * @Route("/{id}", methods="DELETE", requirements={"id"="\d+"})
     * @Request({"id": "int"}, csrf=true)
     */
    public function deleteAction($id)
    {
        if ($project = File::find($id)) {

            if(!App::user()->hasAccess('download: manage downloads')) {
                return ['error' => __('Access denied.')];
            }

			$project->delete();
        }

        return ['message' => 'success'];
    }

    /**
     * @Route("/bulk", methods="POST")
     * @Request({"files": "array"}, csrf=true)
     */
    public function bulkSaveAction($files = [])
    {
        foreach ($files as $data) {
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
