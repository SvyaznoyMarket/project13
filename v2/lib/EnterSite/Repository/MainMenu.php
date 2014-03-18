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
     * @return Model\MainMenu\Element[]
     */
    public function getObjectByQuery(Query $menuListQuery, Query $categoryListQuery = null) {
        $menu = new Model\MainMenu();

        //$menuData = $menuListQuery->getResult();
        // TODO: исправить
        $menuData = json_decode(file_get_contents($this->getConfig()->dir . '/v2/data/cms/v2/main-menu.json'), true);
        $categoryData = $categoryListQuery->getResult();

        $categoryItemsById = [];
        // индексирование данных категорий по id
        $walkByCategoryData = function(&$categoryData) use (&$categoryItemsById, &$walkByCategoryData) {
            $categoryItem = null;
            foreach ($categoryData as &$categoryItem) {
                if (isset($categoryItem['id'])) $categoryItem['id'] = (string)$categoryItem['id'];
                if (isset($categoryItem['root_id'])) $categoryItem['root_id'] = (string)$categoryItem['root_id'];

                $categoryItemsById[$categoryItem['id']] = $categoryItem;

                if (isset($categoryItem['children'][0])) {
                    $walkByCategoryData($categoryItem['children']);
                }
            }
            unset($categoryItem);
        };
        $walkByCategoryData($categoryData);
        //die(var_dump(array_keys($categoryItemsById)));

        $walkByMenuElementItem = function($elementItems, Model\MainMenu\Element $parentElement = null) use (&$menu, &$walkByMenuElementItem) {
            foreach ($elementItems as $elementItem) {
                $menuElement = new Model\MainMenu\Element($elementItem);

                $source = (!empty($elementItem['source']) && is_scalar($elementItem['source'])) ? trim((string)$elementItem['source'], '/') : null;
                if ($source) {
                    $params = [];
                    if ((0 === strpos($source, 'category/get')) && !empty($params['id']) && isset($categoryItemsById[$params['id']])) {
                        $menuElement->name = (string)$categoryItemsById[$params['id']]['name'];
                        $menuElement->url = rtrim((string)$categoryItemsById[$params['id']]['link'], '/');
                    } else if ((0 === strpos($source, 'category/tree')) && !empty($params['root_id']) && isset($categoryItemsById[$params['root_id']])) {
                        //die(var_dump($categoryItemsById[$params['root_id']]));
                    }

                    if (isset($elementItem['children'][0])) {
                        $walkByMenuElementItem($elementItem['children'], $menuElement);
                    }
                }

                if ($parentElement) {
                    $parentElement->children[] = $menuElement;
                } else {
                    $menu->elements[] = $menuElement;
                }
            }
        };
        $walkByMenuElementItem($menuData['items']);

        //die(var_dump($menu));

        return $menu;
    }
}