<?php

namespace EnterSite\Action\Product\Category;

use Enter\Curl\Query;
use Enter\Exception\NotFound;
use EnterSite\ConfigTrait;
use EnterSite\Model;

class GetAncestryObjectByQuery {
    use ConfigTrait;

    /**
     * @param Query $query
     * @throws NotFound
     * @return Model\Product\TreeCategory
     */
    public function execute(Query $query) {
        $category = null;

        $item = $query->getResult();
        if (!$item) {
            throw new NotFound('Категория товара не найдена');
        }

        $category = new Model\Product\TreeCategory($item);

        return $category;
    }
}