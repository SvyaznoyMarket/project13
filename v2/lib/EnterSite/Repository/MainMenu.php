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
     * @return \EnterSite\Model\MainMenu\Element[]
     */
    public function getObjectByQuery(Query $menuListQuery, Query $categoryListQuery = null) {
        $menu = new Model\MainMenu();

        //$menuData = $menuListQuery->getResult();
        $menuData = json_decode(file_get_contents($this->getConfig()->dir . '/v2/data/cms/v2/main-menu.json'), true);
        $categoryData = $categoryListQuery->getResult();

        foreach ($menuData['items'] as $elementItem) {
            $element = new Model\MainMenu\Element($elementItem);

            $menu->elements[] = $element;
        }

        return $menu;
    }
}