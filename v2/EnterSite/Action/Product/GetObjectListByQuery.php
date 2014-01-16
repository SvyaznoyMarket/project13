<?php

namespace EnterSite\Action\Product;

use EnterSite\ConfigTrait;
use Enter\Curl\Query;
use EnterSite\Model\Product;

class GetObjectListByQuery {
    use \EnterSite\ConfigTrait;

    /**
     * @param Query $query
     * @return Product[]
     */
    public function execute(Query $query) {
        $products = [];

        foreach ($query->getResult() as $item) {
            $products[] = new Product($item);
        }

        return $products;
    }
}