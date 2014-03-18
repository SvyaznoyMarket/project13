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

        $walkByMenuElementItem = function($elementItems, Model\MainMenu\Element $parentElement = null) use (&$menu, &$walkByMenuElementItem, &$categoryItemsById) {
            $elementItem = null;
            foreach ($elementItems as &$elementItem) {
                $element = null;
                if (!isset($elementItem['children'])) {
                    $elementItem['children'] = [];
                }

                $source = (!empty($elementItem['source']) && is_scalar($elementItem['source'])) ? trim((string)$elementItem['source'], '/') : null;
                if ($source) {
                    $params = [];
                    parse_str(parse_url($source, PHP_URL_QUERY), $params);

                    if ((0 === strpos($source, 'category/get')) && !empty($params['id']) && isset($categoryItemsById[$params['id']])) {
                        $element = new Model\MainMenu\Element($elementItem);
                        $element->name = (string)$categoryItemsById[$params['id']]['name'];
                        $element->url = rtrim((string)$categoryItemsById[$params['id']]['link'], '/');
                    } else if ((0 === strpos($source, 'category/tree')) && !empty($params['root_id']) && isset($categoryItemsById[$params['root_id']])) {
                        $element = $parentElement;
                        $parentElement = null;
                        $categoryItem = null;
                        foreach ($categoryItemsById[$params['root_id']]['children'] as &$categoryItem) {
                            $elementItem['children'][] = [
                                'source' => 'category/get?id=' . $categoryItem['id'],
                            ];
                        }
                        unset($categoryItem);
                    }

                    if (isset($elementItem['children'][0])) {
                        $walkByMenuElementItem($elementItem['children'], $element);
                    }
                }

                if (!$element) continue;

                if ($parentElement) {
                    $parentElement->children[] = $element;
                } else {
                    $menu->elements[] = $element;
                }
            }
            unset($elementItem);
        };
        $walkByMenuElementItem($menuData['items']);

        //die(json_encode($menu->elements, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        return $menu;
    }
}