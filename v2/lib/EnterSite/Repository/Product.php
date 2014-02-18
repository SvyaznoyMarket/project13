<?php

namespace EnterSite\Repository;

use Enter\Exception;
use Enter\Http;
use Enter\Curl\Query;
use EnterSite\ConfigTrait;
use EnterSite\Model;

class Product {
    use ConfigTrait;

    /**
     * @param Http\Request $request
     * @return string
     */
    public function getTokenByHttpRequest(Http\Request $request) {
        $token = explode('/', $request->query['productPath']);
        $token = end($token);

        return $token;
    }

    /**
     * @param Query $query
     * @return Model\Product
     * @throws Exception\NotFound
     */
    public function getObjectByQuery(Query $query) {
        $product = null;

        $item = $query->getResult();
        if (!$item) {
            throw new Exception\NotFound('Товар не найден');
        }

        $product = new Model\Product($item);

        return $product;
    }

    /**
     * @param Query $query
     * @return Model\Product[]
     */
    public function getObjectListByQuery(Query $query) {
        $products = [];

        foreach ($query->getResult() as $item) {
            if (isset($item['property'])) unset($item['property']); // оптимизация
            if (isset($item['property_group'])) unset($item['property_group']); // оптимизация
            $products[] = new Model\Product($item);
        }

        return $products;
    }
}