<?php

namespace EnterSite\Repository;

use Enter\Curl\Query;
use EnterSite\ConfigTrait;
use EnterSite\Model;

class Product {
    use ConfigTrait;

    /**
     * @param Query $query
     * @return Model\Product[]
     */
    public function getObjectListByQuery(Query $query) {
        $products = [];

        foreach ($query->getResult() as $item) {
            $products[] = new Model\Product($item);
        }

        return $products;
    }
}