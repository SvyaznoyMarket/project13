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
        $sender = (array)$request->query->get('sender') + ['name' => null, 'position' => null, 'action' => null];

        $productIds = [];
        $recommendController = new \Controller\Product\BasicRecommendedAction();

        $productIds = array_merge($productIds, (array)$recommendController->getProductsIdsFromRetailrocket(null, $request, 'ItemsToMain'));
        $sender['name'] = 'retailrocket';
        $sender['method'] = 'MainToMain';

        /* Получаем продукты из ядра */
        $products = [];
        foreach (array_chunk($productIds, \App::config()->coreV2['chunk_size'], true) as $productsInChunk) {
            \RepositoryManager::product()->prepareCollectionById($productsInChunk, $region, function($data) use (&$products) {
                foreach ((array)$data as $item) {
                    if (empty($item['id'])) continue;

                    /*
                    \Controller\Product\BasicRecommendedAction::prepareLink(
                        $item['link'], ['engine' => 'retailrocket', 'method' => 'MainToMain', 'id' => $item['id']]
                    );
                    */

                    $product = new \Model\Product\Entity($item);
                    // если товар недоступен для покупки - пропустить
                    if (
                        !$product->getIsBuyable()
                        && !$product->isInShopShowroomOnly()
                    ) {
                        continue;
                    }

                    $products[] = $product;
                }
            });
        }

        \App::coreClientV2()->execute();

        try {
            // TODO: вынести в репозиторий
            usort($products, function(\Model\Product\Entity $a, \Model\Product\Entity $b) {
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

        $products = array_slice($products, 0, 35);

        $title = 'Мы рекомендуем';
        if ('Main' == $sender['position']) {
            $title = 'Популярные товары';
        }

        /* Рендерим слайдер */
        $slider = \App::closureTemplating()->render('product/__slider', [
            'products'     => $products,
            'count'        => count($products),
            'class'        => $cssClass,
            'namePosition' => $namePosition,
            'sender'       => $sender,
            'title'        => $title,
        ]);

        $recommend = [];
        $recommend['main'] = [
            'content' => $slider,
            'success' => true
        ];

        return new \Http\JsonResponse(['success'=> true, 'recommend' => $recommend]);
    }

} 