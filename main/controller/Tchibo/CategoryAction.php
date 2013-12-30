<?php

namespace Controller\Tchibo;

class CategoryAction {

    public function execute(\Http\Request $request, $categoryPath) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $categoryToken = explode('/', $categoryPath);
        $categoryToken = end($categoryToken);

        /** @var $category \Model\Product\Category\Entity */
        $category = null;
        \RepositoryManager::productCategory()->prepareEntityByToken($categoryToken, \App::user()->getRegion(), function($data) use (&$category) {
            $data = reset($data);
            if ((bool)$data) {
                $category = new \Model\Product\Category\Entity($data);
            }
        });
        \App::coreClientV2()->execute();

        if (!$category) {
            throw new \Exception\NotFoundException(sprintf('Категория товара @%s не найдена.', $categoryToken));
        }

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
                $productsById[$gridCell->getId()] = null;
            }
        }

        foreach (array_chunk(array_keys($productsById), \App::config()->coreV2['chunk_size']) as $idsInChunk) {
            \RepositoryManager::product()->prepareCollectionById(array_keys($productsById), \App::user()->getRegion(), function($data) use (&$productsById) {
                foreach ($data as $item) {
                    $productsById[$item['id']] = new \Model\Product\CompactEntity($item);
                }
            });
        }
        \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);
        $productsById = array_filter($productsById);

        $page = new \View\Tchibo\CategoryPage();
        $page->setParam('gridCells', $gridCells);
        $page->setParam('productsById', $productsById);

        return new \Http\Response($page->show());
    }
}