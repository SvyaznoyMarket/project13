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
     * @param \Model\Region\Entity $region
     * @return \Model\Menu\Entity[]
     */
    public function generate(\Model\Region\Entity $region = null) {
        static $instance;
        if ($instance) {
            return $instance;
        }

        if (!$region) {
            $region = \App::user()->getRegion();
        }

        $isFailed = false;
        $this->repository->prepareCollection(function($data) {
            $this->prepareMenu($data['item']);
        }, function(\Exception $e) use (&$isFailed) {
            \App::exception()->add($e);
            \App::logger()->error(new \Exception('Не удалось получить главное меню'), ['menu']);
            $isFailed = true;
        });
        \App::coreClientV2()->execute();

        if ($isFailed) {
            $this->menu = $this->repository->getCollection();
        }

        // сбор категорий для ACTION_PRODUCT_CATALOG
        \RepositoryManager::productCategory()->prepareTreeCollection($region, 3, 0, function($data) {
            foreach ($data as $item) {
                $category = new \Model\Product\Category\MenuEntity($item);
                foreach ($category->getChild() as $childCat) {
                    $this->categoriesById[$childCat->getId()] = $childCat;
                }
                $this->rootCategoriesById[$item['id']] = $category;
            }
        });

        // сбор категорий для ACTION_PRODUCT_CATEGORY
        \RepositoryManager::productCategory()->prepareCollectionById(array_keys($this->categoriesById), $region, function($data) {
            foreach ($data as $item) {
                $this->categoriesById[$item['id']] = new \Model\Product\Category\MenuEntity($item);
            }
        });

        \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium'], \App::config()->coreV2['retryCount']);


        \App::scmsClient()->addQuery('category/get-by-filters', ['filters' => ['appearance.use_logo' => 'true'], 'geo_id' => $region->getId()], [], function($data) {
            if (is_array($data)) {
                $categoriesByUi = $this->indexCategoriesByUi(array_merge($this->rootCategoriesById, $this->categoriesById));
                foreach ($data as $itemData) {
                    $itemData = \RepositoryManager::productCategory()->convertScmsDataToOldCmsData($itemData);
                    if (isset($categoriesByUi[$itemData['ui']])) {
                        /** @var \Model\Product\Category\MenuEntity $category */
                        $category = $categoriesByUi[$itemData['ui']];
                        if (isset($itemData['use_logo'])) {
                            $category->setUseLogo($itemData['use_logo']);
                        }

                        if (isset($itemData['logo_path'])) {
                            $category->setLogoPath($itemData['logo_path']);
                        }
                    }
                }
            }
        });

        \App::scmsClient()->execute();

        $this->fillMenu($this->menu);

        $instance = $this->menu;
        return $this->menu;
    }

    /**
     * @return \Model\Product\Category\MenuEntity|null
     */
    public function indexCategoriesByUi($categories) {
        $result = [];
        foreach ($categories as $category) {
            /** @var \Model\Product\Category\MenuEntity $category */
            $result[$category->getUi()] = $category;
            $result = array_merge($result, $this->indexCategoriesByUi($category->getChild()));
        }

        return $result;
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
                $parent->child[] = $iMenu;
            } else {
                $this->menu[] = $iMenu;
            }
            if (\Model\Menu\Entity::ACTION_PRODUCT_CATEGORY == $iMenu->action) {
                if ($categoryId = $iMenu->firstItem) {
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
            if ((bool)$iMenu->child) {
                $this->fillMenu($iMenu->child);
            }

            // ссылка
            if (\Model\Menu\Entity::ACTION_LINK == $iMenu->action) {
                $iMenu->link = $iMenu->firstItem;
            // категория товара
            } else if (\Model\Menu\Entity::ACTION_PRODUCT_CATEGORY == $iMenu->action) {
                $categoryId = $iMenu->firstItem;
                /** @var \Model\Product\Category\MenuEntity $category */
                $category = ($categoryId && isset($this->categoriesById[$categoryId])) ? $this->categoriesById[$categoryId] : null;
                if (!$category) {
                    \App::logger()->warn(sprintf('Не найдена категория #%s для элемента меню %s', $categoryId, $iMenu->name));
                    continue;
                }

                $iMenu->link = $category->getLink();
                $iMenu->useLogo = $category->getUseLogo();
                $iMenu->logoPath = $category->getLogoPath();
            // ветка категории товара
            } else if (\Model\Menu\Entity::ACTION_PRODUCT_CATALOG == $iMenu->action) {
                $categoryId = $iMenu->firstItem;
                /** @var \Model\Product\Category\MenuEntity $category */
                $category = ($categoryId && isset($this->rootCategoriesById[$categoryId])) ? $this->rootCategoriesById[$categoryId] : null;
                if (!$category && $categoryId && isset($this->categoriesById[$categoryId])) {
                    $category = $this->categoriesById[$categoryId];
                }

                if (!$category) {
                    \App::logger()->warn(sprintf('Не найдена категория #%s для элемента меню %s', $categoryId, $iMenu->name));
                    continue;
                }

                $iMenu->link = $category->getLink();
                $iMenu->useLogo = $category->getUseLogo();
                $iMenu->logoPath = $category->getLogoPath();
                $this->fillCatalogMenu($iMenu, $category);
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
                'action'   => \Model\Menu\Entity::ACTION_PRODUCT_CATALOG,
                'name'     => $childCategory->getName(),
                'item'     => [$childCategory->getId()],
                'useLogo'  => $childCategory->getUseLogo(),
                'logoPath' => $childCategory->getLogoPath(),
            ]);
            $child->link = $childCategory->getLink();
            $child->image = $childCategory->getImageUrl(0);
            $iMenu->child[] = $child;

            if ($childCategory->countChild()) {
                $this->fillCatalogMenu($child, $childCategory);
            }
        }

        if ((2 == $category->getLevel()) && ($category->countChild() > \Model\Product\Category\MenuEntity::MAX_CHILD)) {
            $child = new \Model\Menu\Entity([
                'action'   => \Model\Menu\Entity::ACTION_PRODUCT_CATEGORY,
                'name'     => 'Все разделы',
                'item'     => [$category->getId()],
                'useLogo'  => $category->getUseLogo(),
                'logoPath' => $category->getLogoPath(),
            ]);
            $child->link = $category->getLink();
            $iMenu->child[] = $child;
        }
    }

}