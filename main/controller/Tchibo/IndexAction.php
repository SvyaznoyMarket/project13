<?php

namespace Controller\Tchibo;

class IndexAction {

    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        /** @var $productsById \Model\Product\Entity[] */
        $productsById = [];

        /** @var $grid \Model\GridCell\Entity[] */
        $gridCells = [];
        foreach ((array)\App::dataStoreClient()->query('/grid/3646.json') as $item) {
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

        $page = new \View\Tchibo\IndexPage();
        $page->setParam('gridCells', $gridCells);
        $page->setParam('productsById', $productsById);

        return new \Http\Response($page->show());
    }
}