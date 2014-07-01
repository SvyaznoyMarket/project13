<?php

namespace Controller\ProductCategory\Grid;

class ChildAction {
    /**
     * @param \Http\Request $request
     * @param \Model\Product\Category\Entity $category
     * @param array $catalogConfig
     * @return \Http\Response
     */
    public function executeByEntity(\Http\Request $request, \Model\Product\Category\Entity $category, $catalogConfig = [], $shopScriptSeo = []) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $region = \App::user()->getRegion();

        $rootCategoryIdInMenu = (!empty($catalogConfig['root_category_menu']['root_id']) && is_scalar($catalogConfig['root_category_menu']['root_id'])) ? (int)$catalogConfig['root_category_menu']['root_id'] : null;
        \RepositoryManager::productCategory()->prepareTreeCollectionByRoot($rootCategoryIdInMenu, $region, 3, function($data) use (&$rootCategoryInMenu) {
            $data = is_array($data) ? reset($data) : [];
            if (isset($data['id'])) {
                $rootCategoryInMenu = new \Model\Product\Category\TreeEntity($data);
            }
        });

        $result = [];
        \App::shopScriptClient()->addQuery(
            'category/get-meta',
            [
                'slug' => [$category->getToken()],
            ],
            [],
            function($data) use (&$result) {
                if (is_array($data)) {
                    $data = reset($data);
                }
                if (isset($data['grid_data']) && is_array($data['grid_data'])) {
                    $result = $data['grid_data'];
                }
            }
        );
        \App::shopScriptClient()->execute();

        /** @var $productsByUi \Model\Product\Entity[] */
        $productsByUi = [];
        /** @var $grid \Model\GridCell\Entity[] */
        $gridCells = [];
        foreach ($result as $item) {
            if (!is_array($item)) continue;
            $gridCell = new \Model\GridCell\Entity($item);
            $gridCells[] = $gridCell;

            if ((\Model\GridCell\Entity::TYPE_PRODUCT === $gridCell->getType()) && $gridCell->getUi()) {
                $productsByUi[$gridCell->getUi()] = $gridCell->getId();
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
                    if (!isset($productsByUi[$item['ui']])) {
                        continue;
                    }
                    $productsByUi[$item['ui']] = new \Model\Product\Entity($item);
                }
            });
        }
        \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);
        $productsByUi = array_filter($productsByUi, function($product) {
            return $product instanceof \Model\Product\BasicEntity;
        });

        if ($category->getProductCount() == 0 && (bool) $category->getAncestor() && $category->getAncestor()[0]->getToken() == 'tchibo') {
            return new \Http\RedirectResponse(\App::router()->generate('content', ['token' => \App::config()->tchibo['whereToBuyPage']]));
        }

        $page = new \View\ProductCategory\Grid\ChildCategoryPage();
        $page->setParam('gridCells', $gridCells);
        $page->setParam('category', $category);
        $page->setParam('catalogConfig', $catalogConfig);
        $page->setParam('productsByUi', $productsByUi);
        $page->setParam('rootCategoryInMenu', $rootCategoryInMenu);
        $page->setParam('shopScriptSeo', $shopScriptSeo);

        return new \Http\Response($page->show());
    }
}