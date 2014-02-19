<?php

namespace EnterSite\Repository\Product;

use Enter\Curl\Query;
use EnterSite\Model;

class Rating {
    /**
     * @param Query $query
     * @return Model\Product\Rating[]
     */
    public function getObjectListByQueryIndexedByProductId(Query $query) {
        $ratings = [];

        try {
            foreach ($query->getResult() as $item) {
                $ratings[$item['product_id']] = new Model\Product\Rating($item);
            }
        } catch (\Exception $e) {
            //trigger_error($e, E_USER_ERROR);
        }

        return $ratings;
    }
}