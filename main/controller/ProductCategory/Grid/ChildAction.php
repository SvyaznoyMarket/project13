<?php

namespace Controller\ProductCategory\Grid;

class ChildAction {
    /**
     * @param \Http\Request $request
     * @param \Model\Product\Category\Entity $category
     * @param array $catalogConfig
     * @return \Http\Response
     */
    public function executeByEntity(\Http\Request $request, \Model\Product\Category\Entity $category, $catalogConfig = []) {
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

        /** @var $productsById \Model\Product\Entity[] */
        $productsById = [];
        /** @var $grid \Model\GridCell\Entity[] */
        $gridCells = [];
        foreach ($result as $item) {
            if (!is_array($item)) continue;
            $gridCell = new \Model\GridCell\Entity($item);
            $gridCells[] = $gridCell;

            if ((\Model\GridCell\Entity::TYPE_PRODUCT === $gridCell->getType()) && $gridCell->getId()) {
                $productsById[$gridCell->getId()] = $gridCell->getId();
            }
        }

        // SITE-2996 учет моделей
        // внимание! получаем ключи массива
        foreach (array_chunk(array_keys($productsById), \App::config()->coreV2['chunk_size']) as $idsInChunk) {
            \App::coreClientV2()->addQuery(
                'product/from-model',
                [
                    'ids'       => $idsInChunk,
                    'region_id' => $region->getId(),
                ],
                [],
                function($data) use (&$productsById) {
                    foreach ($data as $productId => $replaceId) {
                        if (array_key_exists($productId, $productsById) && $replaceId) {
                            $productsById[$productId] = $replaceId;
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
        foreach (array_chunk($productsById, \App::config()->coreV2['chunk_size'], true) as $idsInChunk) {
            \RepositoryManager::product()->prepareCollectionById(array_values($idsInChunk), \App::user()->getRegion(), function($data) use (&$productsById, &$idsInChunk) {
                foreach ($data as $item) {
                    if (!isset($productsById[$item['id']])) {
                        continue;
                    }
                    $productsById[$item['id']] = new \Model\Product\Entity($item);
                }
            });
        }
        \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);
        $productsById = array_filter($productsById, function($product) {
            return $product instanceof \Model\Product\BasicEntity;
        });

        $page = new \View\ProductCategory\Grid\ChildCategoryPage();
        $page->setParam('gridCells', $gridCells);
        $page->setParam('category', $category);
        $page->setParam('catalogConfig', $catalogConfig);
        $page->setParam('productsById', $productsById);
        $page->setParam('rootCategoryInMenu', $rootCategoryInMenu);

        return new \Http\Response($page->show());
    }
}