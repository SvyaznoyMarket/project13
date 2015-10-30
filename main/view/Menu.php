<?php

namespace View;

use Model\Menu\BasicMenuEntity;
use Model\Product\Category\TreeEntity;

class Menu {
    /** @var \Model\Menu\Repository */
    private $repository;
    /** @var \Routing\Router */
    private $router;
    /** @var \View\DefaultLayout|null */
    private $page;

    public function __construct(&$page = null) {
        $this->router = \App::router();
        $this->repository = \RepositoryManager::menu();
        if ($page instanceof \View\DefaultLayout) $this->page = $page;
    }

    /** Текущая функция для построения меню
     * @param \Model\Region\Entity $region
     * @return array
     * @throws \Exception
     */
    public function generate_new(\Model\Region\Entity $region){
        $menu = [];

        $menuData = [];
        $categoriesTree = [];
        $categoriesTreeData = [];
        $availableCategoriesDataByUi = [];

        // Получаем данные из ядра
        try {
            $exception = null;

            // Получаем главное меню из scms
            $this->repository->prepareCollection(
                function($data) use (&$menuData) {
                    $menuData = $data;
                },
                function(\Exception $e) use (&$exception) {
                    \App::logger()->error(new \Exception('Не удалось получить главное меню'), ['menu']);
                    $exception = $e;
                }
            );

            \App::scmsClient()->addQuery('api/category/tree', [
                'depth'           => 2,
                'is_load_parents' => true,
                'load_medias'     => true,
            ], [], function($data) use (&$categoriesTreeData) {
                $categoriesTreeData = $data;
            });

            \App::searchClient()->addQuery('category/get-available', [
                'depth'           => 2,
                'is_load_parents' => true,
                'region_id'       => $region->getId(),
            ], [], function($data) use(&$availableCategoriesDataByUi) {
                $availableCategoriesDataByUi = [];
                if (is_array($data)) {
                    foreach ($data as $item) {
                        if (isset($item['uid'])) {
                            $availableCategoriesDataByUi[$item['uid']] = $item;
                        }
                    }
                }
            });

            \App::scmsClient()->execute();

            call_user_func(function() use(&$categoriesTree, $categoriesTreeData, $availableCategoriesDataByUi) {
                if (is_array($categoriesTreeData) && !empty($categoriesTreeData)) {
                    $this->filterUnavailableCategories($categoriesTreeData, $availableCategoriesDataByUi);

                    foreach($categoriesTreeData as $item) {
                        $menuEntity = new BasicMenuEntity($item);
                        $categoriesTree[$menuEntity->id] = $menuEntity;
                    }
                } else {
                    throw new \Exception('Не удалось получить категории');
                }
            });

            if ($exception instanceof \Exception) {
                throw $exception;
            }

            if (!(bool)$menuData) {
                throw new \Exception('Не удалось получить главное меню');
            }
        } catch (\Exception $e) {
            $menuData = $this->repository->getCollection();
        }

        if (!isset($menuData['item'][0])) {
            throw new \Exception('Пустое главное меню');
        }

        foreach ($menuData['item'] as $item) {

            // источник - категория
            if (isset($item['source']['id']) && @$item['source']['type'] == 'category-get') {
                $menuItem = $this->getMenuItemById($item['source']['id'], $categoriesTree);
                if ($menuItem) {
                    if (isset($item['char'])) {
                        $menuItem->char = $item['char'];
                    } else {
                        $this->getImageFromMedias($menuItem, (array)@$item['medias']);
                    }
                    if ($item['name']) $menuItem->name = $item['name'];
                    $menu[] = $menuItem;
                }
            }

            // источник - слайс
            if (@$item['source']['type'] == 'slice') {
                $menuItem = new BasicMenuEntity([
                    'name'  => @$item['name'],
                    'link'  => \App::router()->generate('slice.show', ['sliceToken' => @$item['source']['url']])
                ]);
                $this->getImageFromMedias($menuItem, (array)@$item['medias']);
                $menu[] = $menuItem;
            }

            // источник - ссылка
            if (@$item['source']['type'] == 'reference') {
                $menuItem = new BasicMenuEntity([
                    'name'  => @$item['name'],
                    'link'  => $this->prepareLink(@$item['source']['url'])
                ]);
                $this->getImageFromMedias($menuItem, (array)@$item['medias']);
                $menu[] = $menuItem;
            }

        }

        $this->limitCategories($menu);

        if ($this->page) $this->page->setGlobalParam('menu', $menu);

        return $menu;
    }

    private function filterUnavailableCategories(&$categories, $availableCategoriesDataByUi) {
        if (is_array($categories)) {
            foreach($categories as $key => &$category) {
                if (!isset($category['uid']) || !isset($availableCategoriesDataByUi[$category['uid']])) {
                    unset($categories[$key]);
                }

                if (!empty($category['children'])) {
                    $this->filterUnavailableCategories($category['children'], $availableCategoriesDataByUi);
                }
            }

            $categories = array_values($categories);
        }
    }

    /** Рекурсивное получение категории из дерева категорий
     * @param $id
     * @param $categoryTree BasicMenuEntity[]
     * @return BasicMenuEntity|null
     */
    private function getMenuItemById($id, $categoryTree) {
        $innerResult = null;
        foreach ($categoryTree as $key => $item) {
            if ($innerResult) return $innerResult;
            if ($key == (int)$id) return $item;
            if ((bool)$item->children) $innerResult = $this->getMenuItemById($id, $item->children);
        }
        return null;
    }

    /** Возвращает относительный путь вместо полного
     * @param $link string|null
     * @return string
     */
    private function prepareLink($link) {
        return (string)preg_replace('/^\s*http:\/\/(www\.)?enter\.ru/', '', (string)$link);
    }

    /** Присваивает изображение для пункта меню
     * @param BasicMenuEntity $menuEntity
     * @param array $medias
     */
    private function getImageFromMedias(BasicMenuEntity &$menuEntity, array $medias) {
        foreach ($medias as $item) {
            if (in_array('site-web', (array)@$item['tags'])) {
                $menuEntity->image = @$item['sources'][0]['url'];
            }
        }
    }

    /** Ограничение количества категорий в меню 3-го уровня
     * @param $menu
     * @param int $limit Максимальное количество категорий
     */
    private function limitCategories(&$menu, $limit = 10){
        /** @var $menu BasicMenuEntity[] */
        foreach ($menu as $menu1) {
            if (empty($menu1->children)) continue;
            foreach ($menu1->children as $menu2) {
                if (count($menu2->children) > 10) {
                    $menu2->children = array_slice($menu2->children, 0, $limit);
                    $menu2->children[] = new BasicMenuEntity([
                        'name'  => 'Все категории',
                        'link'  => $menu2->link
                    ]);
                }
            }
        }
    }
}