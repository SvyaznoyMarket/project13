<?php

namespace EnterSite\Repository\Product;

use Enter\Http;
use Enter\Curl\Query;
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
     * Возвращает список категорий без дочерних узлов
     *
     * @param Query $query
     * @return Model\Product\Category[]
     */
    public function getObjectListByQuery(Query $query) {
        $categories = [];

        foreach ($query->getResult() as $item) {
            if (isset($item['children'])) unset($item['children']);

            $categories[] = new Model\Product\Category($item);
        }

        return $categories;
    }

    /**
     * Преобразовывает древовидную структуру данных в линейную
     * и возвращает список категорий от верхнего уровня до нижнего (branch)
     *
     * @param \Enter\Curl\Query $query
     * @return Model\Product\Category[]
     */
    public function getAscendantListByQuery(Query $query) {
        $categories = [];

        $walk = function(array $item) use (&$walk, &$categories) {
            $childItem = isset($item['children'][0]['id']) ? $item['children'][0] : null;
            // удаляем children, т.к. он не загружен полностью - в нем только один элемент
            if (isset($item['children'])) unset($item['children']);
            $categories[] = new Model\Product\Category($item);

            if ($childItem) {
                $walk($childItem);
            }
        };

        if ($item = $query->getResult()) {
            $walk($item);
        }

        return $categories;
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
                    $adminItem = $adminQuery->getResult();

                    $item = array_merge($item, $adminItem ?: []);
                } catch (\Exception $e) {
                    trigger_error($e, E_USER_ERROR);
                }
            }

            $category = new Model\Product\Category($item);
        }

        return $category;
    }

    /**
     * К переданной категории добавляет предков и детей
     *
     * @param Model\Product\Category $category
     * @param Query $query
     */
    public function setBranchForObjectByQuery(Model\Product\Category $category, Query $query) {
        $walk = function($item) use (&$walk, &$category) {
            if (!$item) {
                return;
            }

            $id = isset($item['id']) ? (string)$item['id'] : null;
            $level = isset($item['level']) ? (int)$item['level'] : null;
            if ($id == $category->id) {
                if (!empty($item['children'])) {
                    foreach ($item['children'] as $childItem) {
                        if (!isset($childItem['id'])) continue;

                        $category->children[] = new Model\Product\Category($childItem);
                    }
                }
            } else if ($level < $category->level) {
                if (isset($item['children'][0]['id'])) {
                    $childItem = $item['children'][0];

                    $walk($childItem);
                    unset($item['children']);
                }
                $category->ascendants[] = new Model\Product\Category($item);
            }
        };

        $walk($query->getResult(), $category);

        $category->ascendants = array_reverse($category->ascendants, true);
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

    /**
     * @param Model\SearchResult $searchResult
     * @return Model\Product\Category[]
     */
    public function getObjectListBySearchResult(Model\SearchResult $searchResult) {
        $categories = [];
        foreach ($searchResult->categories as $searchCategory) {
            $category = new Model\Product\Category();
            $category->id = $searchCategory->id;
            $category->name = $searchCategory->name;
            $category->productCount = $searchCategory->productCount;
            $category->image = $searchCategory->image;

            $categories[] = $category;
        }

        return $categories;
    }
}