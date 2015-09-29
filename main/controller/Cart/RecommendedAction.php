<?php


namespace controller\Cart;


class RecommendedAction {
    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     */
    public function execute(\Http\Request $request) {
        // Если корзина не пустая
        if (\App::user()->getCart()->count()) {
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
        $cart = \App::user()->getCart();
        $cartProductIds = [];
        /** @var \Model\Product\Entity[] $productsById */
        $productsById = [];
        $recommendController = new \Controller\Product\BasicRecommendedAction();

        /* Для всех продуктов корзины получим рекомендации */
        /* Неплохо распараллелить запросы, ну да ладно */
        foreach ($cart->getProductsById() as $product) {
            $cartProductIds[] = $product->id;

            foreach ((array)$recommendController->getProductsIdsFromRetailrocket($product, $request, 'CrossSellItemToItems') as $productId) {
                $productsById[$productId] = new \Model\Product\Entity(['id' => $productId]);
            }
        }

        \RepositoryManager::product()->prepareProductQueries($productsById, 'media label category brand');

        \App::coreClientV2()->execute();

        $productsById = array_filter($productsById, function(\Model\Product\Entity $product) use(&$cartProductIds) {
            return (!in_array($product->id, $cartProductIds) && $product->isAvailable() && !$product->isInShopShowroomOnly() && !$product->isInShopOnly());
        });

        try {
            // TODO: вынести в репозиторий
            usort($productsById, function(\Model\Product\Entity $a, \Model\Product\Entity $b) {
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

        $productsById = array_slice($productsById, 0, 30);

        /* Рендерим слайдер */
        $slider = \App::closureTemplating()->render(\App::abTest()->isNewProductPage() ? 'product-page/blocks/slider' : 'product/__slider', [
            'products'  => $productsById,
            'class'     => 'slideItem-7item',
            'title'     => $cart->count() > 1 ? 'С этими товарами покупают' : 'С этим товаром покупают',
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
        $productIdsByType = (new \Controller\Main\Action())->getProductIdsFromRR($request, \App::config()->coreV2['timeout']);
        /** @var \Model\Product\Entity[] $productsById */
        $productsById = [];
        foreach ($productIdsByType as $ids) {
            if (!$ids) continue;
            foreach ($ids as $productId) {
                $productsById[$productId] = new \Model\Product\Entity(['id' => $productId]);
            }
        }

        \RepositoryManager::product()->prepareProductQueries($productsById, 'media label category brand');
        \App::coreClientV2()->execute();

        $productsById = array_filter($productsById, function(\Model\Product\Entity $product) use(&$cartProductIds) {
            return ($product->isAvailable() && !$product->isInShopShowroomOnly() && !$product->isInShopOnly());
        });

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