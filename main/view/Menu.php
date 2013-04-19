<?php

namespace View;

class Menu {
    /** @var \Model\Menu\Repository */
    private $repository;
    /** @var \Routing\Router */
    private $router;
    /** @var \Model\Product\Category\MenuEntity[] */
    private $rootCategoriesById = [];
    /** @var \Model\Product\Category\MenuEntity[] */
    private $categoriesById = [];
    /** @var \Model\Menu\Entity[] */
    private $menu;

    public function __construct() {
        $this->repository = \RepositoryManager::menu();
        $this->router = \App::router();
    }

    /**
     * @return \Model\Menu\Entity[]
     */
    public function generate() {
        $isFailed = false;
        $this->repository->prepareCollection(function($data) {
            $this->prepareMenu($data['item']);
        }, function(\Exception $e) use (&$isFailed) {
            \App::exception()->remove($e);
            $isFailed = true;
        });

        \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium'], \App::config()->coreV2['retryCount']);

        if ($isFailed) {
            $this->menu = $this->repository->getCollection();
        }

        // сбор категорий для ACTION_PRODUCT_CATALOG
        \RepositoryManager::productCategory()->prepareTreeCollection(\App::user()->getRegion(), 3, function($data) {
            foreach ($data as $item) {
                $this->rootCategoriesById[$item['id']] = new \Model\Product\Category\MenuEntity($item);
            }
        });

        // сбор категорий для ACTION_PRODUCT_CATEGORY
        \RepositoryManager::productCategory()->prepareCollectionById(array_keys($this->categoriesById), \App::user()->getRegion(), function($data) {
            foreach ($data as $item) {
                $this->categoriesById[$item['id']] = new \Model\Product\Category\MenuEntity($item);
            }
        });

        \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium'], \App::config()->coreV2['retryCount']);

        $this->fillMenu($this->menu);
        \App::debug()->add('time.main-menu.catalog', sprintf('%s ms', round(\Debug\Timer::get('main-menu.catalog')['total'], 3) * 1000), 92);

        return $this->menu;
    }

    /**
     * Создание объектов меню, сбор категорий
     *
     * @param $data
     * @param \Model\Menu\Entity $parent
     */
    public function prepareMenu($data, \Model\Menu\Entity $parent = null) {
        foreach ($data as $item) {
            $iMenu = new \Model\Menu\Entity($item);
            if ($parent) {
                $parent->addChild($iMenu);
            } else {
                $this->menu[] = $iMenu;
            }
            if (\Model\Menu\Entity::ACTION_PRODUCT_CATEGORY == $iMenu->getAction()) {
                if ($categoryId = $iMenu->getFirstItem()) {
                    $this->categoriesById[$categoryId] = null;
                }
            }

            if (isset($item['child']) && is_array($item['child'])) {
                $this->prepareMenu($item['child'], $iMenu);
            }
        }
    }

    /**
     * @param \Model\Menu\Entity[] $menu
     */
    public function fillMenu($menu) {
        foreach ($menu as $iMenu) {
            if ((bool)$iMenu->getChild()) {
                $this->fillMenu($iMenu->getChild());
            }

            // ссылка
            if (\Model\Menu\Entity::ACTION_LINK == $iMenu->getAction()) {
                $iMenu->setLink($iMenu->getFirstItem());
            // категория товара
            } else if (\Model\Menu\Entity::ACTION_PRODUCT_CATEGORY == $iMenu->getAction()) {
                $categoryId = $iMenu->getFirstItem();
                /** @var \Model\Product\Category\MenuEntity $category */
                $category = ($categoryId && isset($this->categoriesById[$categoryId])) ? $this->categoriesById[$categoryId] : null;
                if (!$category) {
                    \App::logger()->warn(sprintf('Не найдена категория #%s для элемента меню %s', $categoryId, $iMenu->getName()));
                    continue;
                }

                $iMenu->setLink($category->getLink());
            // ветка категории товара
            } else if (\Model\Menu\Entity::ACTION_PRODUCT_CATALOG == $iMenu->getAction()) {
                $categoryId = $iMenu->getFirstItem();
                /** @var \Model\Product\Category\MenuEntity $category */
                $category = ($categoryId && isset($this->rootCategoriesById[$categoryId])) ? $this->rootCategoriesById[$categoryId] : null;
                if (!$category) {
                    \App::logger()->warn(sprintf('Не найдена категория #%s для элемента меню %s', $categoryId, $iMenu->getName()));
                    continue;
                }

                $iMenu->setLink($category->getLink());
                \Debug\Timer::start('main-menu.catalog');
                $this->fillCatalogMenu($iMenu, $category);
                \Debug\Timer::stop('main-menu.catalog');
            }
        }
    }

    /**
     * @param \Model\Menu\Entity                 $iMenu
     * @param \Model\Product\Category\MenuEntity $category
     */
    private function fillCatalogMenu(\Model\Menu\Entity $iMenu, \Model\Product\Category\MenuEntity $category) {
        foreach ($category->getChild() as $childCategory) {
            $child = new \Model\Menu\Entity([
                'action' => \Model\Menu\Entity::ACTION_PRODUCT_CATALOG,
                'name'   => $childCategory->getName(),
                'item'   => [$childCategory->getId()],
            ]);
            $child->setLink($childCategory->getLink());
            $child->setImage($childCategory->getImageUrl(0));
            $iMenu->addChild($child);

            if ($childCategory->countChild()) {
                $this->fillCatalogMenu($child, $childCategory);
            }
        }

        if ((2 == $category->getLevel()) && ($category->countChild() > \Model\Product\Category\MenuEntity::MAX_CHILD)) {
            $child = new \Model\Menu\Entity([
                'action' => \Model\Menu\Entity::ACTION_PRODUCT_CATEGORY,
                'name'   => 'Все разделы',
                'item'   => [$category->getId()],
            ]);
            $child->setLink($category->getLink());
            $iMenu->addChild($child);
        }
    }
}