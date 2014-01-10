<?php

namespace Enter\Site\Action\Product\Category;

use Enter\Site\ConfigTrait;
use Enter\Curl\Query;
use Enter\Exception\NotFound;
use Enter\Site\Model\Product\TreeCategory;

class GetAncestryObjectByQuery {
    use ConfigTrait;

    /**
     * @param Query $query
     * @throws NotFound
     * @return TreeCategory
     */
    public function execute(Query $query) {
        $category = null;

        $item = $query->getResult();
        if (!$item) {
            throw new NotFound('Категория товара не найдена');
        }

        $category = new TreeCategory($item);

        return $category;
    }
}