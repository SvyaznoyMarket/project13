<?php

namespace Controller\ProductCategory\Grid;

class ChildAction {
    /**
     * @param \Http\Request $request
     * @param $categoryPath
     * @return \Http\RedirectResponse|\Http\Response
     * @throws \Exception\NotFoundException
     */
    public function executeByPath(\Http\Request $request, $categoryPath) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $region = \App::user()->getRegion();

        $categoryToken = explode('/', $categoryPath);
        $categoryToken = end($categoryToken);

        /** @var $category \Model\Product\Category\Entity */
        $category = null;

        $shopScriptException = null;
        $shopScriptSeo = [];
        /** @var $category \Model\Product\Category\Entity */
        if (\App::config()->shopScript['enabled']) {
            try {
                $shopScript = \App::shopScriptClient();
                $shopScript->addQuery(
                    'category/get-seo',
                    [
                        'slug'   => $categoryToken,
                        'geo_id' => \App::user()->getRegion()->getId(),
                    ],
                    [],
                    function ($data) use (&$shopScriptSeo) {
                        if ($data && is_array($data)) $shopScriptSeo = reset($data);
                    },
                    function (\Exception $e) use (&$shopScriptException) {
                        $shopScriptException = $e;
                    }
                );
                $shopScript->execute();
                if ($shopScriptException instanceof \Exception) {
                    throw $shopScriptException;
                }

                // если shopscript вернул редирект
                if (!empty($shopScriptSeo['redirect']['link'])) {
                    $redirect = $shopScriptSeo['redirect']['link'];
                    if(!preg_match('/^http/', $redirect)) {
                        $redirect = (preg_match('/^http/', \App::config()->mainHost) ? '' : 'http://') .
                            \App::config()->mainHost .
                            (preg_match('/^\//', $redirect) ? '' : '/') .
                            $redirect;
                    }
                    return new \Http\RedirectResponse($redirect);
                }

                if (empty($shopScriptSeo['ui'])) {
                    throw new \Exception\NotFoundException(sprintf('Не получен ui для категории товара @%s', $categoryToken));
                }

                // запрашиваем категорию по ui
                \RepositoryManager::productCategory()->prepareEntityByUi($shopScriptSeo['ui'], $region, function($data) use (&$category) {
                    $data = reset($data);
                    if ((bool)$data) {
                        $category = new \Model\Product\Category\Entity($data);
                    }
                });
            } catch (\Exception $e) { // если не плучилось добыть seo-данные или категорию по ui, пробуем старый добрый способ
                \RepositoryManager::productCategory()->prepareEntityByToken($categoryToken, $region, function($data) use (&$category) {
                    $data = reset($data);
                    if ((bool)$data) {
                        $category = new \Model\Product\Category\Entity($data);
                    }
                });
            }

        } else {
            \RepositoryManager::productCategory()->prepareEntityByToken($categoryToken, $region, function($data) use (&$category) {
                $data = reset($data);
                if ((bool)$data) {
                    $category = new \Model\Product\Category\Entity($data);
                }
            });
        }
        \App::coreClientV2()->execute();

        if (!$category) {
            throw new \Exception\NotFoundException(sprintf('Категория товара @%s не найдена.', $categoryToken));
        }

        return $this->executeByEntity($category, $request);
    }

    /**
     * @param \Model\Product\Category\Entity $category
     * @param \Http\Request $request
     * @return \Http\Response
     */
    public function executeByEntity(\Model\Product\Category\Entity $category, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $region = \App::user()->getRegion();

        /** @var $productsById \Model\Product\Entity[] */
        $productsById = [];

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
                    if (false === $productId = array_search($item['id'], $idsInChunk)) continue;
                    $productsById[$productId] = new \Model\Product\CompactEntity($item);
                }
            });
        }
        \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);
        $productsById = array_filter($productsById);

        $page = new \View\ProductCategory\Grid\ChildCategoryPage();
        $page->setParam('gridCells', $gridCells);
        $page->setParam('category', $category);
        $page->setParam('productsById', $productsById);

        return new \Http\Response($page->show());
    }
}