<?php


namespace controller\User;


class RecommendAction extends PrivateAction {

    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     */
    public function execute(\Http\Request $request) {
        $client = \App::coreClientV2();
        $region = \App::user()->getRegion();
        $repository =  \RepositoryManager::product()->useV3()->withoutModels()->withoutPartnerStock();

            // вы смотрели
        $viewedProductIds = [];
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

        /** @var \Model\Product\Entity[] $productsById */
        $productsById = [];

        $repository->prepareCollectionById($productIds, $region, function($data) use (&$productsById) {
            foreach ((array)$data as $item) {
                $productsById[$item['id']] = new \Model\Product\Entity($item);
            }
            });

        $client->execute();

        \RepositoryManager::product()->enrichProductsFromScms($productsById, 'media label');
        $client->execute();

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

        // сортировка
        if (false) try {
            // TODO: вынести в репозиторий
            usort($recommendedProducts, function(\Model\Product\Entity $a, \Model\Product\Entity $b) {
                if ($b->getIsBuyable() != $a->getIsBuyable()) {
                    return ($b->getIsBuyable() ? 1 : -1) - ($a->getIsBuyable() ? 1 : -1); // сначала те, которые можно купить
                } else if ($b->isInShopOnly() != $a->isInShopOnly()) {
                    //return ($b->isInShopOnly() ? -1 : 1) - ($a->isInShopOnly() ? -1 : 1); // потом те, которые можно зарезервировать
                } else if ($b->isInShopShowroomOnly() != $a->isInShopShowroomOnly()) {// потом те, которые есть на витрине
                    return ($b->isInShopShowroomOnly() ? -1 : 1) - ($a->isInShopShowroomOnly() ? -1 : 1);
                } else {
                    return (int)rand(-1, 1);
                }
            });
        } catch (\Exception $e) {}

        $page = new \View\User\RecommendPage();
        $page->setParam('recommendedProducts', array_values($recommendedProducts));
        $page->setParam('viewedProducts', array_values($viewedProducts));

        return new \Http\Response($page->show());
    }
}