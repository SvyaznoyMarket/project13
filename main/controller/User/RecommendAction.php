<?php


namespace controller\User;


class RecommendAction {
    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     */
    public function execute(\Http\Request $request) {
        $client = \App::coreClientV2();
        $region = \App::user()->getRegion();

        $productIdsByType = (new \Controller\Main\Action())->getProductIdsFromRR($request, \App::config()->coreV2['timeout']);
        $productIds = $productIdsByType['personal'] ?: $productIdsByType['popular'];


        $productsById = [];
        foreach (array_chunk($productIds, \App::config()->coreV2['chunk_size'], true) as $productsInChunk) {
            \RepositoryManager::product()->prepareCollectionById($productsInChunk, $region, function($data) use (&$productsById) {
                foreach ((array)$data as $item) {
                    if (empty($item['id'])) continue;

                    $productsById[$item['id']] = new \Model\Product\Entity($item);
                }
            });
        }

        $client->execute();

        $page = new \View\User\RecommendPage();
        $page->setParam('products', $productsById);

        return new \Http\Response($page->show());
    }
}