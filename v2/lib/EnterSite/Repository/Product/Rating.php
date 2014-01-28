<?php

namespace EnterSite\Repository\Product;

use Enter\Http;
use Enter\Curl\Query;
use EnterSite\Model;

class Rating {
    public function getObjectListByQuery(Query $query) {
        $ratings = null;

        $data = $query->getResult();
        foreach ($data as $item) {
            $ratings[] = new Model\Product\Rating($item);
        }

        return $ratings;
    }
}