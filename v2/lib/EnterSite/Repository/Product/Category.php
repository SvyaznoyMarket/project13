<?php

namespace EnterSite\Repository\Product;

use Enter\Http;
use Enter\Curl\Query;
use Enter\Exception;
use EnterSite\ConfigTrait;
use EnterSite\Model;

class Category {
    use ConfigTrait;

    /**
     * @param Http\Request $request
     * @return string
     */
    public function getTokenByHttpRequest(Http\Request $request) {
        $token = explode('/', $request->query['productCategoryPath']);
        $token = end($token);

        return $token;
    }

    /**
     * @param Query $query
     * @throws Exception\NotFound
     * @return Model\Product\TreeCategory
     */
    public function getAncestryObjectByQuery(Query $query) {
        $category = null;

        $item = $query->getResult();
        if (!$item) {
            throw new Exception\NotFound('Категория товара не найдена');
        }

        $category = new Model\Product\TreeCategory($item);

        return $category;
    }

    /**
     * @param Query $coreQuery
     * @param Query $adminQuery
     * @return Model\Product\Category
     * @throws Exception\NotFound
     */
    public function getObjectByQuery(Query $coreQuery, Query $adminQuery = null) {
        $category = null;

        $item = $coreQuery->getResult();
        if (!$item) {
            throw new Exception\NotFound('Категория товара не найдена');
        }

        if ($adminQuery) {
            try {
                $item = array_merge($item, $adminQuery->getResult());
            } catch (\Exception $e) {
                trigger_error(sprintf('Некорректный ответ от admin-сервиса: %s', $e->getMessage()), E_USER_ERROR);
            }
        }

        $category = new Model\Product\Category($item);

        return $category;
    }
}