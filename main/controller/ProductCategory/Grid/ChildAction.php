<?php

namespace Controller\ProductCategory\Grid;

use Model\Product\Category\Entity;

class ChildAction {
    /**
     * @param \Http\Request $request
     * @param \Model\Product\Category\Entity $category
     * @param array $catalogConfig
     * @return \Http\Response
     */
    public function executeByEntity(\Http\Request $request, \Model\Product\Category\Entity $category, $catalogConfig = []) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $region = \App::user()->getRegion();

        $rootCategoryIdInMenu = (!empty($catalogConfig['root_category_menu']['root_id']) && is_scalar($catalogConfig['root_category_menu']['root_id'])) ? (int)$catalogConfig['root_category_menu']['root_id'] : null;
        $rootCategoryInMenu = null;
        if ($rootCategoryIdInMenu) {
            \RepositoryManager::productCategory()->prepareTreeCollectionByRoot($rootCategoryIdInMenu, $region, 3, function($data) use (&$rootCategoryInMenu) {
                $data = is_array($data) ? reset($data) : [];
                if (isset($data['id'])) {
                    $rootCategoryInMenu = new \Model\Product\Category\TreeEntity($data);
                }
            });

            \App::searchClient()->execute();
        }

        $rootCategoryInMenuImage = null;
        if (isset($catalogConfig['root_category_menu']['image']) && !empty($catalogConfig['root_category_menu']['image'])) {
            $rootCategoryInMenuImage = $catalogConfig['root_category_menu']['image'];
        }

        if (!$category->grid) {
            \App::logger()->error(['message' => 'Не передан grid.items', 'sender' => __FILE__ . ' ' .  __LINE__], ['tchibo']);
            \App::exception()->add(new \Exception('Проблема с гридстером'));
        }

        /** @var $productsByUi \Model\Product\Entity[] */
        $productsByUi = [];
        foreach ($category->grid as $item) {
            if (\Model\GridCell\Entity::TYPE_PRODUCT === $item->getType() && $item->getObjectUi()) {
                $productsByUi[$item->getObjectUi()] = $item->getObjectUi();
            }
        }

        // SITE-2996 учет моделей
        // внимание! получаем ключи массива
        foreach (array_chunk(array_keys($productsByUi), \App::config()->coreV2['chunk_size']) as $uisInChunk) {
            \App::coreClientV2()->addQuery(
                'product/from-model',
                [
                    'uis'       => $uisInChunk,
                    'region_id' => $region->getId(),
                ],
                [],
                function($data) use (&$productsByUi) {
                    foreach ($data as $productUi => $replaceUi) {
                        if (array_key_exists($productUi, $productsByUi) && $replaceUi) {
                            $productsByUi[$productUi] = $replaceUi;
                        }
                    }
                },
                function(\Exception $e) {
                    \App::exception()->remove($e);
                }
            );
        }

        \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);

        // внимание! получаем значения массива
        foreach (array_chunk($productsByUi, \App::config()->coreV2['chunk_size'], true) as $uisInChunk) {
            \RepositoryManager::product()->prepareCollectionByUi(array_values($uisInChunk), \App::user()->getRegion(), function($data) use (&$productsByUi, &$uisInChunk) {
                foreach ($data as $item) {
                    $key = array_search($item['ui'], $productsByUi, true);
                    if (!isset($productsByUi[$key])) {
                        continue;
                    }
                    $productsByUi[$key] = new \Model\Product\Entity($item);
                }
            });
        }
        \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);

        $productsByUi = array_filter($productsByUi, function($product) {
            return $product instanceof \Model\Product\Entity;
        });

        \RepositoryManager::product()->prepareProductsMedias($productsByUi);
        \App::coreClientV2()->execute();

        if (
            ($category->getProductCount() == 0)
            && (bool)$category->getAncestor()
            && ($category->getAncestor()[0]->getToken() == 'tchibo')
            && (\App::config()->preview !== true)
            && !\App::config()->debug
        ) {
            return new \Http\RedirectResponse(\App::router()->generate('tchibo.where_buy', $request->query->all()));
        }

        // SITE-3970
        // Стили для названий категорий tchibo
        $tchiboMenuCategoryNameStyles = [];
        if (isset($catalogConfig['tchibo_menu']['style']['name']) && is_array($catalogConfig['tchibo_menu']['style']['name'])) {
            $tchiboMenuCategoryNameStyles = $catalogConfig['tchibo_menu']['style']['name'];
        }

        $page = new \View\ProductCategory\Grid\ChildCategoryPage();
        $page->setParam('gridCells', $category->grid);
        $page->setParam('category', $category);
        $page->setParam('catalogConfig', $catalogConfig);
        $page->setParam('productsByUi', $productsByUi);
        $page->setParam('rootCategoryInMenu', $rootCategoryInMenu);
        $page->setGlobalParam('tchiboMenuCategoryNameStyles', $tchiboMenuCategoryNameStyles);
        $page->setGlobalParam('rootCategoryInMenuImage', $rootCategoryInMenuImage);
        $page->setGlobalParam('isTchibo', ($category->getRoot() && 'Tchibo' === $category->getRoot()->getName()));

        return new \Http\Response($page->show());
    }
}