<?php

namespace Enter\Site\Action\Product\Category;

use Enter\Exception\NotFound;
use Enter\Site\ConfigTrait;
use Enter\Curl\Query;
use Enter\Site\Model\Product\Category;

class GetObjectByQuery {
    use ConfigTrait;

    /**
     * @param Query $coreQuery
     * @param Query $adminQuery
     * @throws \Exception
     * @return Category
     */
    public function execute(Query $coreQuery, Query $adminQuery = null) {
        $category = null;

        $item = $coreQuery->getResult();
        if (!$item) {
            throw new NotFound('Категория товара не найдена');
        }

        if ($adminQuery) {
            try {
                $item = array_merge($item, $adminQuery->getResult());
            } catch (\Exception $e) {
                var_dump($e);
                // TODO $httpResponse->status = 500;
            }
        }

        $category = new Category($item);

        return $category;
    }
}