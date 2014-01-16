<?php

namespace EnterSite\Action\Product\Category;

use EnterSite\ConfigTrait;
use Enter\Curl\Query;
use Enter\Exception\NotFound;
use EnterSite\Model\Product\TreeCategory;

class GetAncestryObjectByQuery {
    use \EnterSite\ConfigTrait;

    /**
     * @param Query $query
     * @throws NotFound
     * @return \EnterSite\Model\Product\TreeCategory
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