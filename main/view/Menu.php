<?php

namespace View;

class Menu {
    /** @var \Model\Menu\Repository */
    private $repository;
    /** @var \Routing\Router */
    private $router;
    /** @var \Model\Product\Category\MenuEntity[] */
    private $categoriesById = [];

    public function __construct() {
        $this->repository = \RepositoryManager::menu();
        $this->router = \App::router();
    }

    /**
     * @return \Model\Menu\Entity[]
     */
    public function generate() {
        /** @var $menu \Model\Menu\Entity[] */
        $menu = [];
        $this->repository->prepareCollection(function($data) use (&$menu) {
            foreach ($data['item'] as $item) {
                $menu[] = new \Model\Menu\Entity($item);
            }
        }, function(\Exception $e) use (&$menu) {
            \App::exception()->remove($e);
            $menu = $this->repository->getCollection();
        });

        \RepositoryManager::productCategory()->prepareTreeCollection(\App::user()->getRegion(), 3, function($data) {
            $walk = function(&$data) use (&$walk) {
                foreach ($data as $item) {
                    $this->categoriesById[$item['id']] = new \Model\Product\Category\MenuEntity($item);

                    if (isset($item['children'])) {
                        $walk($item['children']);
                    }
                }
            };
            $walk($data);
        });

        \App::coreClientV2()->execute();

        //$menu = $this->repository->getCollection(); //для тестирования
        $this->walkOnMenu($menu);

        return $menu;
    }

    /**
     * @param \Model\Menu\Entity[] $menu
     */
    private function walkOnMenu($menu) {
        /** @var $iMenu \Model\Menu\Entity  */
        foreach ($menu as $iMenu) {
            /** @var \Model\Menu\Entity $iMenu */
            $this->setLink($iMenu, \App::router(), $this->categoriesById);

            if (\Model\Menu\Entity::ACTION_PRODUCT_CATALOG == $iMenu->getAction()) {
                $items = $iMenu->getItem();
                $id = reset($items);
                /** @var \Model\Product\Category\MenuEntity $category */
                $category = ($id && isset($this->categoriesById[$id])) ? $this->categoriesById[$id] : null;
                if (!$category) {
                    \App::logger()->error(sprintf('Не найдена категория #%s для элемента меню %s', $id, $iMenu->getName()));
                    continue;
                }

                if (2 == $category->getLevel()) {
                    $iMenu->setImage($category->getImageUrl(0));
                }

                if ($category->getLevel() <= 2) {
                    foreach ($category->getChild() as $childCategory) {
                        $child = new \Model\Menu\Entity([
                            'action' => \Model\Menu\Entity::ACTION_PRODUCT_CATALOG,
                            'name'   => $childCategory->getName(),
                            'item'   => [$childCategory->getId()],
                        ]);
                        $iMenu->addChild($child);
                    }

                    if ((2 == $category->getLevel()) && ($category->countChild() > \Model\Product\Category\MenuEntity::MAX_CHILD)) {
                        $child = new \Model\Menu\Entity([
                            'action' => \Model\Menu\Entity::ACTION_PRODUCT_CATEGORY,
                            'name'   => 'Все разделы',
                            'item'   => [$category->getId()],
                        ]);
                        $iMenu->addChild($child);
                    }
                }
            }

            if ((bool)$iMenu->getChild()) {
                $this->walkOnMenu($iMenu->getChild());
            }
        }
    }

    /**
     * @param \Model\Menu\Entity $iMenu
     * @throws \Exception
     */
    public function setLink(\Model\Menu\Entity $iMenu) {
        $link = null;

        try {
            $items = $iMenu->getItem();
            if (!(bool)$items) {
                return;
            }

            if (!is_array($items)) {
                $items = [$items];
            }

            switch ($iMenu->getAction()) {
                case \Model\Menu\Entity::ACTION_LINK:
                    $iMenu->setLink(is_array($items) ? reset($items) : (string)$items);
                    break;
                case \Model\Menu\Entity::ACTION_PRODUCT_CATEGORY:
                case \Model\Menu\Entity::ACTION_PRODUCT_CATALOG:
                    $id = reset($items);
                    /** @var $category \Model\Product\Category\Entity */
                    $category = ($id && isset($this->categoriesById[$id])) ? $this->categoriesById[$id] : null;
                    if ($category) {
                        $iMenu->setLink($category->getLink());
                    } else {
                        \App::logger()->error(sprintf('Для меню не найдена категория товара #%s', $id));
                    }

                    break;
                case \Model\Menu\Entity::ACTION_PRODUCT:
                    $products = [];
                    foreach ($items as $id) {
                        if (!isset($productsById[$id])) {
                            \App::logger()->error(sprintf('Для меню не найден товар #%s', $id));
                            continue;
                        }
                        $products[] = $productsById[$id];
                    }

                    if (1 == count($items)) {
                        $product = reset($products);
                        $iMenu->setLink($this->router->generate('product', array('productPath' => $product->getPath())));
                    } else {
                        $barcodes = array_map(function ($product) { /** @var $product \Model\Product\Entity */ return $product->getBarcode(); }, $products);
                        $iMenu->setLink($this->router->generate('product.set', array('productBarcodes' => implode(',', $barcodes))));
                    }

                    break;
            }
        } catch (\Exception $e) {
            \App::logger()->error($e);
        }
    }
}