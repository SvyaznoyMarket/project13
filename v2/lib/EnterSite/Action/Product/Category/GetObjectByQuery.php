<?php

namespace EnterSite\Action\Product\Category;

use Enter\Curl\Query;
use Enter\Exception\NotFound;
use EnterSite\ConfigTrait;
use EnterSite\Model;

class GetObjectByQuery {
    use ConfigTrait;

    /**
     * @param Query $coreQuery
     * @param Query $adminQuery
     * @throws \Exception
     * @return \EnterSite\Model\Product\Category
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
                trigger_error(sprintf('Некорректный ответ от admin-сервиса: %s', $e->getMessage()), E_USER_ERROR);
            }
        }

        $category = new Model\Product\Category($item);

        return $category;
    }
}