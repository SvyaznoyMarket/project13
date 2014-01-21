<?php

namespace EnterSite\Action\Product;

use Enter\Curl\Query;
use EnterSite\ConfigTrait;
use EnterSite\Model;

class GetObjectListByQuery {
    use \EnterSite\ConfigTrait;

    /**
     * @param Query $query
     * @return Model\Product[]
     */
    public function execute(Query $query) {
        $products = [];

        foreach ($query->getResult() as $item) {
            $products[] = new Model\Product($item);
        }

        return $products;
    }
}