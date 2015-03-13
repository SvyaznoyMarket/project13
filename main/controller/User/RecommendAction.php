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

        // вы смотрели
        $viewedProductIds = [];
        //$data = $request->cookies->get('rrviewed');
        $data = $request->get('rrviewed');
        if (is_string($data)) {
            $data = explode(',', $data);
        }
        if (empty($data)) {
            $data = explode(',', (string)$request->cookies->get('product_viewed'));
        }
        if (is_array($data)) {
            $data = array_reverse(array_filter($data));
            $viewedProductIds = array_slice(array_unique($data), 0, 30);
        }

        $productIdsByType = (new \Controller\Main\Action())->getProductIdsFromRR($request, \App::config()->coreV2['timeout']);
        $recommendedProductIds = $productIdsByType['personal'] ?: $productIdsByType['popular'];

        $productIds = array_merge($viewedProductIds, $recommendedProductIds);

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

        $recommendedProducts = [];
        foreach ($recommendedProductIds as $productId) {
            $product = isset($productsById[$productId]) ? $productsById[$productId] : null;
            if (!$product) continue;

            $recommendedProducts[] = $product;
        }

        $viewedProducts = [];
        foreach ($viewedProductIds as $productId) {
            $product = isset($productsById[$productId]) ? $productsById[$productId] : null;
            if (!$product) continue;

            $viewedProducts[] = $product;
        }

        $page = new \View\User\RecommendPage();
        $page->setParam('recommendedProducts', $recommendedProducts);
        $page->setParam('viewedProducts', $viewedProducts);

        return new \Http\Response($page->show());
    }
}