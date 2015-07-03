<?php


namespace controller\Cart;


class RecommendedAction {
    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     */
    public function execute(\Http\Request $request) {
        // Если корзина не пустая
        if (!\App::user()->getCart()->isEmpty()) {
            return $this->getForNonEmptyCart($request);
        } else { // ... иначе, если пустая корзина
            return $this->getForEmptyCart($request);
        }
    }

    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     * @throws \Exception
     */
    public function getForNonEmptyCart(\Http\Request $request) {
        $region = \App::user()->getRegion();

        $cart = \App::user()->getCart();
        $cartProductIds = [];
        $productIds = [];
        $recommendController = new \Controller\Product\BasicRecommendedAction();

        /* Для всех продуктов корзины получим рекомендации */
        /* Неплохо распараллелить запросы, ну да ладно */
        foreach ($cart->getProducts() as $product) {
            $cartProductIds[] = $product->getId();
            $productIds = array_merge($productIds, (array)$recommendController->getProductsIdsFromRetailrocket($product, $request, 'CrossSellItemToItems'));
        }

        /* Получаем продукты из ядра */
        /** @var \Model\Product\Entity[] $products */
        $products = [];
        $medias = [];
        foreach (array_chunk($productIds, \App::config()->coreV2['chunk_size'], true) as $productsInChunk) {
            \RepositoryManager::product()->useV3()->withoutModels()->withoutPartnerStock()->prepareCollectionById($productsInChunk, $region, function($data) use (&$products, &$cartProductIds) {
                if (!is_array($data)) return;

                foreach ($data as $item) {
                    if (empty($item['id'])) continue;
                    if (in_array($item['id'], $cartProductIds)) continue;

                    $iProduct = new \Model\Product\Entity($item);
                    // если товар недоступен для покупки - пропустить
                    if (!$iProduct->isAvailable() || $iProduct->isInShopShowroomOnly() || $iProduct->isInShopOnly()) continue;
                    $products[$iProduct->getId()] = $iProduct;
                }
            });

            \RepositoryManager::product()->prepareProductsMediasByIds($productsInChunk, $medias);
        }

        \App::coreClientV2()->execute();

        \RepositoryManager::product()->setMediasForProducts($products, $medias);

        try {
            // TODO: вынести в репозиторий
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

        $products = array_slice($products, 0, 30);

        /* Рендерим слайдер */
        $slider = \App::closureTemplating()->render(\App::abTest()->isNewProductPage() ? 'product-page/blocks/slider' : 'product/__slider', [
            'products'  => $products,
            'count'     => count($products),
            'class'     => 'slideItem-7item',
            'title'     => count($cart->getProducts()) > 1 ? 'С этими товарами покупают' : 'С этим товаром покупают',
            'sender'    => [
                'name'     => 'retailrocket',
                'method'   => 'CrossSellItemToItems',
                'position' => 'Basket',
            ],
        ]);

        $recommend = [];
        $recommend['alsoBought'] = [
            'content'   => $slider,
            'success'   => true,
        ];

        return new \Http\JsonResponse(['success'=> true, 'recommend' => $recommend]);
    }

    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     * @throws \Exception
     */
    public function getForEmptyCart(\Http\Request $request) {
        $region = \App::user()->getRegion();

        $productIdsByType = (new \Controller\Main\Action())->getProductIdsFromRR($request, \App::config()->coreV2['timeout']);
        $productIds = [];
        foreach ($productIdsByType as $ids) {
            if (!$ids) continue;
            $productIds = array_merge($productIds, $ids);
        }

        /* Получаем продукты из ядра */
        /** @var \Model\Product\Entity[] $productsById */
        $productsById = [];
        $medias = [];
        foreach (array_chunk($productIds, \App::config()->coreV2['chunk_size'], true) as $productsInChunk) {
            \RepositoryManager::product()->useV3()->withoutModels()->withoutPartnerStock()->prepareCollectionById($productsInChunk, $region, function($data) use (&$productsById, &$cartProductIds) {
                foreach ($data as $item) {
                    if (empty($item['id'])) continue;

                    $iProduct = new \Model\Product\Entity($item);
                    // если товар недоступен для покупки - пропустить
                    if (!$iProduct->isAvailable() || $iProduct->isInShopShowroomOnly() || $iProduct->isInShopOnly()) continue;

                    $productsById[$iProduct->getId()] = $iProduct;
                }
            });

            \RepositoryManager::product()->prepareProductsMediasByIds($productsInChunk, $medias);
        }

        \App::coreClientV2()->execute();

        \RepositoryManager::product()->setMediasForProducts($productsById, $medias);

        $responseData = ['success'=> true, 'recommend' => []];
        foreach ($productIdsByType as $type => $productIds) {
            $products = [];
            foreach ($productIds as $id) {
                $product = isset($productsById[$id]) ? $productsById[$id] : null;
                if (!$product) continue;

                $products[$id] = $product;
            }

            try {
                // TODO: вынести в репозиторий
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

            /* Рендерим слайдер */
            $slider = \App::closureTemplating()->render(\App::abTest()->isNewProductPage() ? 'product-page/blocks/slider' : 'product/__slider', [
                'products'  => $products,
                'count'     => count($products),
                'class'     => 'slideItem-7item',
                'title'     => ('popular' === $type) ? 'Популярные товары' : 'Мы рекомендуем',
                'sender'    => [
                    'name'     => 'retailrocket',
                    'method'   => ('popular' === $type) ? 'PersonalRecommendation' : 'ItemsToMain', // FIXME
                    'position' => 'Basket',
                ],
            ]);

            $responseData['recommend']['popular' === $type ? 'alsoBought' : 'main'] = [
                'content'   => $slider,
                'success'   => true,
            ];
        }

        return new \Http\JsonResponse($responseData);
    }
} 