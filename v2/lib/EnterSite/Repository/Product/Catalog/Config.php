<?php

namespace EnterSite\Repository\Product\Catalog;

use Enter\Curl\Query;
use Enter\Http;
use EnterSite\ConfigTrait;
use EnterSite\Model;

class Config {
    use ConfigTrait;

    /**
     * @param Query $query
     * @return Model\Product\Catalog\Config|null
     */
    public function getObjectByQuery(Query $query) {
        $object = null;

        $item = $query->getResult();
        if ($item) {
            $object = new Model\Product\Catalog\Config($item);
        }

        return $object;
    }

    public function getLimitByHttpRequest(Http\Request $request) {
        $limit = (int)$request->query['limit'];
        if (($limit >= 400) || ($limit <= 0)) {
            $limit = $this->getConfig()->product->itemPerPage;
        }

        return $limit;
    }
}