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
        $token = explode('/', $request->query['categoryPath']);
        $token = end($token);

        return $token;
    }

    /**
     * @param \Enter\Curl\Query $query
     * @return Model\Product\Category
     */
    public function getAncestryObjectByQuery(Query $query) {
        $category = null;

        if ($item = $query->getResult()) {
            $category = new Model\Product\Category($item);
        }

        return $category;
    }

    /**
     * @param Query $coreQuery
     * @param Query $adminQuery
     * @return Model\Product\Category
     */
    public function getObjectByQuery(Query $coreQuery, Query $adminQuery = null) {
        $category = null;

        if ($item = $coreQuery->getResult()) {
            if ($adminQuery) {
                try {
                    $item = array_merge($item, $adminQuery->getResult());
                } catch (\Exception $e) {
                    trigger_error($e, E_USER_ERROR);
                }
            }

            $category = new Model\Product\Category($item);
        }

        return $category;
    }

    /**
     * @param Model\Product[] $products
     * @param string[] $categoryTokens
     * @return Model\Product\Category[]
     */
    public function getIndexedObjectListByProductListAndTokenList(array $products, array $categoryTokens) {
        $categoriesById = [];

        foreach ($products as $product) {
            if (!$product->category) continue;

            $isValid = false;
            foreach (array_merge([$product->category], $product->category->ascendants) as $category) {
                /** @var Model\Product\Category $category */
                if (in_array($category->token, $categoryTokens)) {
                    $isValid = true;
                    break;
                }
            }

            if ($isValid && !isset($categoriesById[$product->category->id])) {
                $categoriesById[$product->category->id] = $product->category;
            }
        }

        return $categoriesById;
    }
}