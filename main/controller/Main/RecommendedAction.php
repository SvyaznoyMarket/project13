<?php


namespace controller\Main;


class RecommendedAction {

    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     */
    public function execute(\Http\Request $request) {
        $client = \App::retailrocketClient();

        $cssClass = $request->query->get('class') ?: 'slideItem-main';
        $namePosition = $request->query->get('namePosition') ?: 'bottom';
        $sender = (array)$request->query->get('sender') + ['name' => null, 'position' => null, 'action' => null];

        /** @var \Model\Product\Entity[] $products */
        $products = [];
        $client->addQuery(
            'Recommendation/Popular',
            null,
            [
                'categoryIds' => 0,
                //'$filter'     => 'Price gt 3000',
            ],
            [],
            function($data) use (&$sender, &$products) {
                if (!is_array($data)) return;

                $ids = [];
                foreach ($data as $item) {
                    if (empty($item['ItemId'])) continue;

                    $ids[] = $item['ItemId'];
                }

                $sender['items'] = array_slice($ids, 0, 15);
                $products = array_map(function($productId) { return new \Model\Product\Entity(['id' => $productId]); }, $sender['items']);
            },
            null,
            null,
            '2.0' // version
        );
        $client->execute(null, 2);

        $sender['name'] = 'retailrocket';
        $sender['method'] = 'Popular';

        \RepositoryManager::product()->prepareProductQueries($products, 'media');
        $products = array_filter($products, function(\Model\Product\Entity $product) {
            return ($product->isAvailable() && !$product->isInShopShowroomOnly());
        });

        \App::coreClientV2()->execute();

        try {
            // TODO: вынести в репозиторий
            usort($products, function(\Model\Product\Entity $a, \Model\Product\Entity $b) {
                if ($b->getIsBuyable() != $a->getIsBuyable()) {
                    return ($b->getIsBuyable() ? 1 : -1) - ($a->getIsBuyable() ? 1 : -1); // сначала те, которые можно купить
                } else if ($b->getPrice() != $a->getPrice()) {
                    return $b->getPrice() - $a->getPrice();
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