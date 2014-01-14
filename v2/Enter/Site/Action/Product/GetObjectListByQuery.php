<?php

namespace Enter\Site\Action\Product;

use Enter\Site\ConfigTrait;
use Enter\Curl\Query;
use Enter\Site\Model\Product;

class GetObjectListByQuery {
    use ConfigTrait;

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