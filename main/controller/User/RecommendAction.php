<?php


namespace Controller\User;


class RecommendAction extends PrivateAction {

    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     */
    public function execute(\Http\Request $request) {
        // вы смотрели
        $viewedProductIds = \RepositoryManager::product()->getViewedProductIdsByHttpRequest($request, true);

        $recommendations = \App::richRelevanceClient()->query('recsForPlacements', [
            'placements' => 'personal_page.top',
        ]);

        $recommendedProductIds = $recommendations['personal_page.top']
            ? $recommendations['personal_page.top']->getProductIds()
            : [];

        /** @var \Model\Product\Entity[] $productsById */
        $productsById = [];
        foreach (array_merge($viewedProductIds, $recommendedProductIds) as $productId) {
            $productsById[$productId] = new \Model\Product\Entity(['id' => $productId]);
        }

        \RepositoryManager::product()->prepareProductQueries($productsById, 'media label category brand');
        \App::coreClientV2()->execute();

        $recommendedProducts = [];
        foreach ($recommendedProductIds as $productId) {
            $product = isset($productsById[$productId]) ? $productsById[$productId] : null;
            if (!$product) continue;

            // если товар недоступен для покупки - пропустить
            if (!$product->isAvailable() || $product->isInShopShowroomOnly() || $product->isInShopOnly()) continue;

            $recommendedProducts[] = $product;
        }

        $viewedProducts = [];
        foreach ($viewedProductIds as $productId) {
            $product = isset($productsById[$productId]) ? $productsById[$productId] : null;
            if (!$product) continue;

            $viewedProducts[] = $product;
        }

        $page = new \View\User\RecommendPage();
        $page->setParam('recommendedProducts', array_values($recommendedProducts));
        $page->setParam('viewedProducts', array_values($viewedProducts));

        return new \Http\Response($page->show());
    }
}