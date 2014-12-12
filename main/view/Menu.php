<?php

namespace View;

use Model\Menu\BasicMenuEntity;

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

    /** Умная рекурсивная функция Егора для построения меню, но пока не используемая
     * @param \Model\Region\Entity $region
     * @throws \Exception
     * @return \Model\Menu\Entity[]
     */
    public function generate(\Model\Region\Entity $region = null) {
        $menu = [];

        $menuData = [];
        $categoryData = [];
        try {
            $exception = false;

            $this->repository->prepareCollection(
                function($data) use (&$menuData) {
                    $menuData = $data;
                },
                function(\Exception $e) use (&$exception) {
                    \App::logger()->error(new \Exception('Не удалось получить главное меню'), ['menu']);

                    $exception = $e;
                }
            );

            \RepositoryManager::productCategory()->prepareTreeCollection(
                $region,
                3,
                0,
                function($data) use (&$categoryData) {
                    $categoryData = $data;
                });

            \App::coreClientV2()->execute();

            if ($exception instanceof \Exception) {
                throw $exception;
            }
        } catch (\Exception $e) {
            $menuData = $this->repository->getCollection();
        }

        if (!isset($menuData['item'][0])) {
            throw new \Exception('Пустое главное меню');
        }

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

        $walkByMenuElementItem = function($elementItems, \Model\Menu\Entity $parentElement = null) use (&$menu, &$walkByMenuElementItem, &$categoryItemsById) {
            foreach ($elementItems as $elementItem) {
                if (isset($elementItem['disabled']) && (true === $elementItem['disabled'])) {
                    continue;
                }

                $element = null;

                $source = !empty($elementItem['source']['type']) ? ($elementItem['source'] + ['type' => null, 'id' => null]) : null;
                if ($source) {
                    $id = $source['id'];

                    if (('category-get' == $source['type']) && !empty($id)) {
                        $categoryItem = isset($categoryItemsById[$id]) ? $categoryItemsById[$id] : null;

                        $element = new \Model\Menu\Entity($elementItem);
                        $element->type = 'category';
                        $element->id = (string)$categoryItem['id'];
                        if (!$element->id && isset($elementItem['source']['id'])) {
                            $element->id = (string)$elementItem['source']['id'];
                        }

                        if (!$element->name) {
                            $element->name = (string)$categoryItem['name'];
                        }
                        $element->link = rtrim((string)$categoryItem['link'], '/');
                    } else if (('category-tree' == $source['type']) && !empty($id)) {
                        $elementItems = [];
                        $categoryItem = null;
                        foreach (isset($categoryItemsById[$id]['children'][0]) ? $categoryItemsById[$id]['children'] : [] as $categoryItem) {
                            $elementItems[] = [
                                'source' => [
                                    'type' => 'category-get',
                                    'id'   => $categoryItem['id'],
                                ],
                                'children' => [['source'=> [
                                    'type' => 'category-tree',
                                    'id'   => $categoryItem['id'],
                                ],]]
                            ];
                        }
                        unset($categoryItem);

                        $walkByMenuElementItem($elementItems, $parentElement);
                    } else if (('slice' == $source['type']) && !empty($source['url'])) {
                        $element = new \Model\Menu\Entity($elementItem);
                        $element->type = 'slice';
                        $element->id = $source['url'];
                        $element->link = '/slices/' . $source['url']; // FIXME
                    }
                } else {
                    $element = new \Model\Menu\Entity($elementItem);
                }

                if (!$element) continue;

                $element->class .= ((bool)$element->class ? ' ' : '') . 'mId' . md5(json_encode($element));

                if (isset($elementItem['children'][0])) {
                    $walkByMenuElementItem($elementItem['children'], $element);
                }

                $element->level = $parentElement ? ($parentElement->level + 1) : 1;

                if ($parentElement) {
                    $parentElement->child[] = $element;
                } else {
                    $menu[] = $element;
                }
            }
        };
        $walkByMenuElementItem($menuData['item']);

        return $menu;
    }

    /** Текущая функция для построения меню
     * @param \Model\Region\Entity $region
     * @return array
     * @throws \Exception
     */
    public function generate_new(\Model\Region\Entity $region = null){
        $menu = [];

        $menuData = [];
        $categoriesTree = [];
        $categoriesWithLogo = [];

        // Получаем данные из ядра
        try {
            $exception = false;

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

            // Получаем дерево категорий
            \RepositoryManager::productCategory()->prepareTreeCollection(
                $region, 3, 0,
                function($data) use (&$categoriesTree) {
                    if (is_array($data) && !empty($data)) {
                        foreach($data as $dataItem) $categoriesTree[$dataItem['id']] = new BasicMenuEntity($dataItem);
                    } else {
                        throw new \Exception('Не удалось получить категории');
                    }
                });

            // Получаем категории, для которых нужно показывать логотип вместо текста
            \App::scmsClient()->addQuery('category/get-by-filters',
                [   'filters' => ['appearance.use_logo' => 'true'],
                    'geo_id' => $region->getId()
                ],
                [],
                function($data) use (&$categoriesWithLogo) {
                    if (is_array($data)) {
                        foreach ($data as $item) {
                            $categoriesWithLogo[@$item['uid']] = (array)$item;
                        }
                    }
                }
            );

            \App::coreClientV2()->execute();

            if ($exception instanceof \Exception) {
                throw $exception;
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
        $this->setCategoryLogo($menu, $categoriesWithLogo);

        if ($this->page) $this->page->setGlobalParam('menu', $menu);

        return $menu;
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
            if (
                in_array('web-site', (array)@$item['tags']) // TODO: убрать как только, так сразу
                || in_array('site-web', (array)@$item['tags'])
            ) {
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

    /** Установка картинки вместо текста для категории второго уровня
     * @param $menu
     * @param array $categoriesWithLogo
     */
    private function setCategoryLogo(&$menu, array $categoriesWithLogo) {
        /** @var $menu BasicMenuEntity[] */
        foreach ($menu as $menuItem) {
            if (isset($categoriesWithLogo[$menuItem->ui])) {
                $menuItem->logo = @$categoriesWithLogo[$menuItem->ui]['properties']['appearance']['logo_path'];
                continue;
            }
            if (!empty($menuItem->children)) $this->setCategoryLogo($menuItem->children, $categoriesWithLogo);
        }
    }

}