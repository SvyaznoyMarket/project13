<?php

namespace EnterSite\Repository;

use Enter\Curl\Query;
use Enter\Exception;
use EnterSite\ConfigTrait;
use EnterSite\Model;

class MainMenu {
    use ConfigTrait;

    /**
     * @param Query $menuListQuery
     * @param Query $categoryListQuery
     * @return Model\MainMenu[]
     */
    public function getObjectListByQuery(Query $menuListQuery, Query $categoryListQuery = null) {
        $menuList = [];

        $menuData = $menuListQuery->getResult();
        $categoryData = $categoryListQuery->getResult();

        foreach ($menuData as $menuItem) {

        }

        return $menuList;
    }
}