<?php

namespace EnterSite\Repository;

use Enter\Http;
use Enter\Curl\Query;
use EnterSite\Model;

class Shop {
    /**
     * @param Query $query
     * @return Model\Shop
     */
    public function getObjectByQuery(Query $query) {
        $shop = null;

        $item = $query->getResult();
        $shop = new Model\Shop($item);

        return $shop;
    }
}