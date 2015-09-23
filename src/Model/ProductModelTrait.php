<?php

namespace Bixie\Cart\Model;

use Pagekit\Application as App;
use Pagekit\Database\ORM\ModelTrait;

trait ProductModelTrait
{
    use ModelTrait;


	public function loadItemModel () {
		if (empty($this->item) && $this->item_id && class_exists($this->item_model)) {
			$item = call_user_func([$this->item_model, 'find'], $this->item_id);
			return $item;
		}
		return null;
	}

    /**
     * @Saving
     */
    public static function saving($event, Product $project)
    {


    }

}
