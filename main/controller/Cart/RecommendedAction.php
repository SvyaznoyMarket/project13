<?php


namespace controller\Cart;


class RecommendedAction {
    /**
     * Требуемые рекомендации передаются в get массиве types. Допустимые значения: alsoBought, popupar, personal
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     */
    public function execute(\Http\Request $request) {
        $productRepository = \RepositoryManager::product();
        $retailRocketClient = \App::retailrocketClient();
        $retailRocketClientTimeout = 1.15;
        $cartProductIds = array_map(function(\Model\Cart\Product\Entity $product) { return $product->id; }, \App::user()->getCart()->getProductsById());
        $rrUserId = $request->cookies->get('rrpusid');

        // Возвращаемые рекомендации
        $recommendedProductsByType = call_user_func(function() use($request, $rrUserId) {
            $types = $request->query->get('types');
            if (!is_array($types)) {
                return [];
            }

            $types = array_flip($types);

            if (!$rrUserId) {
                unset($types['personal']);
            }

            return array_intersect_key([
                'alsoBought' => [],
                'popupar' => [],
                'personal' => [],
            ], $types);
        });

        if (isset($recommendedProductsByType['alsoBought'])) {
            foreach ($cartProductIds as $productId) {
                $retailRocketClient->addQuery(
                    'Recomendation/CrossSellItemToItems',
                    $productId,
                    [],
                    [],
                    function($productIds) use(&$recommendedProductsByType) {
                        if (is_array($productIds)) {
                            foreach ($productIds as $productId) {
                                $recommendedProductsByType['alsoBought'][$productId] = new \Model\Product\Entity(['id' => $productId]);
                            }
                        }
                    },
                    function(\Exception $e) {
                        \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' .  __LINE__], ['fatal', 'recommendation', 'retailrocket']);
                        \App::exception()->remove($e);
                    },
                    $retailRocketClientTimeout
                );
            }
        }

        if (isset($recommendedProductsByType['popular']) || isset($recommendedProductsByType['personal'])) {
            $retailRocketClient->addQuery(
                'Recomendation/ItemsToMain',
                null,
                [],
                [],
                function ($productIds) use (&$recommendedProductsByType) {
                    if (is_array($productIds)) {
                        foreach ($productIds as $productId) {
                            $recommendedProductsByType['popular'][$productId] = new \Model\Product\Entity(['id' => $productId]);
                        }
                    }
                },
                function (\Exception $e) {
                    \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' . __LINE__], ['fatal', 'recommendation', 'retailrocket']);
                    \App::exception()->remove($e);
                },
                $retailRocketClientTimeout
            );
        }

        if (isset($recommendedProductsByType['personal'])) {
            $retailRocketClient->addQuery(
                'Recomendation/PersonalRecommendation',
                null,
                ['rrUserId' => $rrUserId],
                [],
                function($productIds) use(&$recommendedProductsByType) {
                    if (is_array($productIds)) {
                        foreach ($productIds as $productId) {
                            $recommendedProductsByType['personal'][$productId] = new \Model\Product\Entity(['id' => $productId]);
                        }
                    }
                },
                function(\Exception $e) {
                    \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' .  __LINE__], ['fatal', 'recommendation', 'retailrocket']);
                    \App::exception()->remove($e);
                },
                $retailRocketClientTimeout
            );
        }

        $retailRocketClient->execute();

        $productsById = [];
        // Получаем товары от бэкэнда
        call_user_func(function() use(&$productsById, $recommendedProductsByType) {
            foreach ($recommendedProductsByType as $products) {
                foreach ($products as $product) {
                    /** @var \Model\Product\Entity $product */
                    $productsById[$product->id] = $product;
                }
            }

            \RepositoryManager::product()->prepareProductQueries($productsById, 'media label category');
            \App::coreClientV2()->execute();
        });

        // Удаляем из рекомендаций товары, которые не удалось получить от бэкэнда
        call_user_func(function() use(&$recommendedProductsByType, $productsById) {
            foreach ($recommendedProductsByType as $type => $products) {
                foreach ($products as $product) {
                    /** @var \Model\Product\Entity $product */
                    if (!isset($productsById[$product->id])) {
                        unset($recommendedProductsByType[$type][$product->id]);
                    }
                }
            }
        });

        // Если нет персональных рекомендаций, то выдадим половину популярных за персональные
        call_user_func(function() use(&$recommendedProductsByType) {
            if (!isset($recommendedProductsByType['personal'])) {
                return;
            }

            if (empty($recommendedProductsByType['personal']) && !empty($recommendedProductsByType['popular'])) {
                foreach ($recommendedProductsByType['popular'] as $key => $item) {
                    if ($key % 2) {
                        $recommendedProductsByType['personal'][$key] = $item;
                        unset($recommendedProductsByType['popular'][$key]);
                    }
                }
            }
        });

        foreach ($recommendedProductsByType as $type => $products) {
            $productRepository->filterRecommendedProducts($recommendedProductsByType[$type], $cartProductIds);
            $productRepository->sortRecommendedProducts($recommendedProductsByType[$type]);
        }

        return new \Http\JsonResponse([
            'success'=> true,
            'recommend' => [
                'alsoBought' => !empty($recommendedProductsByType['alsoBought']) ? [
                    'content' => \App::closureTemplating()->render(\App::abTest()->isNewProductPage() ? 'product-page/blocks/slider' : 'product/__slider', [
                        'products' => array_values($recommendedProductsByType['alsoBought']),
                        'class' => 'slideItem-7item',
                        'title' => count($cartProductIds) > 1 ? 'С этими товарами покупают' : 'С этим товаром покупают',
                        'sender' => [
                            'name' => 'retailrocket',
                            'method' => 'CrossSellItemToItems',
                            'position' => 'Basket',
                        ],
                    ]),
                    'success' => true,
                ] : [],
                'popular' => !empty($recommendedProductsByType['popular']) ? [
                    'content' => \App::closureTemplating()->render(\App::abTest()->isNewProductPage() ? 'product-page/blocks/slider' : 'product/__slider', [
                        'products' => array_values($recommendedProductsByType['popular']),
                        'class' => 'slideItem-7item',
                        'title' => 'Популярные товары',
                        'sender' => [
                            'name' => 'retailrocket',
                            'method' => 'ItemsToMain',
                            'position' => 'Basket',
                        ],
                    ]),
                    'success' => true,
                ] : [],
                'personal' => !empty($recommendedProductsByType['personal']) ? [
                    'content' => \App::closureTemplating()->render(\App::abTest()->isNewProductPage() ? 'product-page/blocks/slider' : 'product/__slider', [
                        'products' => array_values($recommendedProductsByType['personal']),
                        'class' => 'slideItem-7item',
                        'title' => 'Мы рекомендуем',
                        'sender' => [
                            'name' => 'retailrocket',
                            'method' => 'PersonalRecommendation',
                            'position' => 'Basket',
                        ],
                    ]),
                    'success' => true,
                ] : [],
            ],
        ]);
    }
} 