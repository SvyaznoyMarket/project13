<?php

namespace EnterSite\Action\Product\Category;

use Enter\Exception\NotFound;
use EnterSite\ConfigTrait;
use Enter\Curl\Query;
use EnterSite\Model\Product\Category;

class GetObjectByQuery {
    use \EnterSite\ConfigTrait;

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

        $category = new Category($item);

        return $category;
    }
}