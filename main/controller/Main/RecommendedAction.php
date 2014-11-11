<?php


namespace controller\Main;


class RecommendedAction {

    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     */
    public function execute(\Http\Request $request) {
        $region = \App::user()->getRegion();

        $cssClass = $request->query->get('class') ?: 'slideItem-main';
        $namePosition = $request->query->get('namePosition') ?: 'bottom';

        $productIds = [];
        $recommendController = new \Controller\Product\BasicRecommendedAction();

        $productIds = array_merge($productIds, (array)$recommendController->getProductsIdsFromRetailrocket(null, $request, 'ItemsToMain'));

        /* Получаем продукты из ядра */
        $products = [];
        foreach (array_chunk($productIds, \App::config()->coreV2['chunk_size'], true) as $productsInChunk) {
            \RepositoryManager::product()->prepareCollectionById($productsInChunk, $region, function($data) use (&$products) {
                foreach ($data as $item) {
                    if (empty($item['id'])) continue;

                    $products[] = new \Model\Product\Entity($item);
                }
            });
        }

        \App::coreClientV2()->execute();

        try {
            usort($products, function(\Model\Product\Entity $a, \Model\Product\Entity $b) {
                if ($b->getIsBuyable() != $a->getIsBuyable()) {
                    return ($b->getIsBuyable() ? 1 : -1) - ($a->getIsBuyable() ? 1 : -1); // сначала те, которые можно купить
                } else if ($b->isInShopOnly() != $a->isInShopOnly()) {
                    return ($b->isInShopOnly() ? -1 : 1) - ($a->isInShopOnly() ? -1 : 1); // потом те, которые можно зарезервировать
                } else if ($b->isInShopShowroomOnly() != $a->isInShopShowroomOnly()) {// потом те, которые есть на витрине
                    return ($b->isInShopShowroomOnly() ? -1 : 1) - ($a->isInShopShowroomOnly() ? -1 : 1);
                } else {
                    return (int)rand(-1, 1);
                }
            });
        } catch (\Exception $e) {}

        $products = array_slice($products, 0, 35);

        /* Рендерим слайдер */
        $slider = \App::closureTemplating()->render('product/__slider', [
            'products'     => $products,
            'count'        => count($products),
            'class'        => $cssClass,
            'namePosition' => $namePosition,
        ]);

        $recommend = [];
        $recommend['alsoBought'] = [
            'content'   => $slider,
            'success'   => true
        ];

        return new \Http\JsonResponse(['success'=> true, 'recommend' => $recommend]);
    }

} 