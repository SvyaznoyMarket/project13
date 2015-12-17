<?php
namespace Controller;

class Recommended {
    /**
     * GET параметр "types" - возвращаемые рекомендации. Допустимые значения: alsoBought, popular, personal
     * GET параметр "productIds" - ID товаров, для которых будут получены рекомендации alsoBought и которые будут
     *                             исключены из всех рекомендуемых товаров
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     */
    public function execute(\Http\Request $request) {
        $productRepository = \RepositoryManager::product();
        $productIds = is_array($request->query->get('productIds')) ? $request->query->get('productIds') : [];
        $rrUserId = $request->cookies->get('rrpusid');

        $showLimit = (int)$request->get('showLimit') ?: null;

        $recommendedProductsByType = call_user_func(function() use($request) {
            $types = $request->query->get('types');
            if (!is_array($types)) {
                return [];
            }

            $types = array_flip($types);

            // Если в personal не будет товаров, то их надо будет взять из popular
            if (isset($types['personal'])) {
                $types['popular'] = null;
            }

            return array_intersect_key([
                'cart_page.rr1' => [],
                'cart_page.rr2' => [],
                'cart_page.rr3' => [],
            ], $types);
        });

        $recommendations = \App::richRelevanceClient()->query('recsForPlacements', [
            'placements' => implode('|', array_keys($recommendedProductsByType)),
        ]);

        if ($recommendedProductsByType) {
            /*if (isset($recommendedProductsByType['alsoBought'])) {
                foreach ($productIds as $productId) {
                    $this->prepareRecommendationQuery('Recomendation/CrossSellItemToItems', $productId, [], $recommendedProductsByType['alsoBought']);
                }
            }

            if (isset($recommendedProductsByType['popular'])) {
                $this->prepareRecommendationQuery('Recomendation/ItemsToMain', null, [], $recommendedProductsByType['popular']);
            }

            if (isset($recommendedProductsByType['personal']) && $rrUserId) {
                $this->prepareRecommendationQuery('Recomendation/PersonalRecommendation', null, ['rrUserId' => $rrUserId], $recommendedProductsByType['personal']);
            }

            \App::retailrocketClient()->execute();*/

            $productsById = [];
            // Получаем товары от бэкэнда
            call_user_func(function() use(&$productsById, $recommendations) {
                foreach ($recommendations as $recommendation) {
                    foreach ($recommendation->getProductsById() as $product) {
                        $productsById[$product->id] = $product;
                    }
                }

                \RepositoryManager::product()->prepareProductQueries($productsById, 'media label category');
                \App::coreClientV2()->execute();
            });

            foreach ($recommendations as $recommendation) {
                $recommendation->replaceProducts($productsById);
            }

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
                    $i = 0;
                    foreach ($recommendedProductsByType['popular'] as $key => $item) {
                        if ($i % 2) {
                            $recommendedProductsByType['personal'][$key] = $item;
                            unset($recommendedProductsByType['popular'][$key]);
                        }
                        $i++;
                    }
                }
            });

            /*if (isset($recommendedProductsByType['alsoBought'])) {
                $productRepository->filterRecommendedProducts($recommendedProductsByType['alsoBought'], $productIds);
                $productRepository->sortRecommendedProducts($recommendedProductsByType['alsoBought']);
            }

            if (isset($recommendedProductsByType['popular'])) {
                $productRepository->filterRecommendedProducts($recommendedProductsByType['popular'], $productIds);
                $productRepository->sortRecommendedProducts($recommendedProductsByType['popular']);
            }

            if (isset($recommendedProductsByType['personal'])) {
                $productRepository->filterRecommendedProducts($recommendedProductsByType['personal'], $productIds);
                $productRepository->sortRecommendedProducts($recommendedProductsByType['personal']);

            }*/
        }

        return new \Http\JsonResponse([
            'success'=> true,
            'recommend' => [
                'cart_page.rr3' => isset($recommendations['cart_page.rr3']) ? [
                    'content' => \App::closureTemplating()->render(
                        'product-page/blocks/slider',
                        [
                            'products' => $recommendations['cart_page.rr3']->getProductsById(),
                            'class' => sprintf('slideItem-%sitem', $showLimit ?: 7),
                            'title' => count($productIds) > 1 ? 'С этими товарами покупают' : 'С этим товаром покупают',
                            'sender' => [
                                'name' => 'rich',
                                'method' => 'cart_page.rr3',
                                'position' => 'Basket',
                            ],
                        ]
                    ),
                    'success' => true,
                ] : [],
                'cart_page.rr1' => isset($recommendations['cart_page.rr1']) ? [
                    'content' => \App::closureTemplating()->render(
                        'product-page/blocks/slider',
                        [
                            'products' => $recommendations['cart_page.rr1']->getProductsById(),
                            'class' => sprintf('slideItem-%sitem', $showLimit ?: 7),
                            'title' => $recommendations['cart_page.rr1']->message,
                            'sender' => [
                                'name' => 'rich',
                                'method' => 'cart_page.rr2',
                                'position' => 'Basket',
                            ],
                        ]
                    ),
                    'success' => true,
                ] : [],
                'cart_page.rr2' => isset($recommendations['cart_page.rr2']) ? [
                    'content' => \App::closureTemplating()->render(
                        'product-page/blocks/slider',
                        [
                            'products' => $recommendations['cart_page.rr2']->getProductsById(),
                            'class' => sprintf('slideItem-%sitem', $showLimit ?: 7),
                            'title' => $recommendations['cart_page.rr2']->message,
                            'sender' => [
                                'name' => 'rich',
                                'method' => 'cart_page.rr2',
                                'position' => 'Basket',
                            ],
                        ]
                    ),
                    'success' => true,
                ] : [],
            ],
        ]);
    }

    /**
     * @param string $method
     * @param null|string|int $itemId
     * @param array $params
     * @param array $recommendedProducts
     */
    private function prepareRecommendationQuery($method, $itemId = null, array $params = [], array &$recommendedProducts) {
        \App::retailrocketClient()->addQuery(
            $method,
            $itemId,
            $params,
            [],
            function($productIds) use(&$recommendedProducts) {
                if (is_array($productIds)) {
                    foreach ($productIds as $productId) {
                        $recommendedProducts[$productId] = new \Model\Product\Entity(['id' => $productId]);
                    }
                }
            },
            function(\Exception $e) {
                \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' .  __LINE__], ['fatal', 'recommendation', 'retailrocket']);
                \App::exception()->remove($e);
            },
            1.15
        );
    }
} 