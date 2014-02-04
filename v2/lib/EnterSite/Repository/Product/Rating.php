<?php

namespace EnterSite\Repository\Product;

use Enter\Http;
use Enter\Curl\Query;
use EnterSite\Model;

class Rating {
    public function getObjectListByQuery(Query $query) {
        $ratings = [];

        try {
            foreach ($query->getResult() as $item) {
                $ratings[] = new Model\Product\Rating($item);
            }
        } catch (\Exception $e) {
            //trigger_error($e, E_USER_ERROR);
        }

        return $ratings;
    }
}