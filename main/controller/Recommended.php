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

        $recommendedProductsByType = call_user_func(function() use($request, $rrUserId) {
            $types = $request->query->get('types');
            if (!is_array($types)) {
                return [];
            }

            $types = array_flip($types);

            if (!$rrUserId) {
                unset($types['personal']);
            }

            // Если в personal не будет товаров, то их надо будет взять из popular
            if (isset($types['personal'])) {
                $types['popular'] = null;
            }

            return array_intersect_key([
                'alsoBought' => [],
                'popular' => [],
                'personal' => [],
            ], $types);
        });

        if ($recommendedProductsByType) {
            if (isset($recommendedProductsByType['alsoBought'])) {
                foreach ($productIds as $productId) {
                    $this->prepareRecommendationQuery('Recomendation/CrossSellItemToItems', $productId, [], $recommendedProductsByType['alsoBought']);
                }
            }

            if (isset($recommendedProductsByType['popular'])) {
                $this->prepareRecommendationQuery('Recomendation/ItemsToMain', null, [], $recommendedProductsByType['popular']);
            }

            if (isset($recommendedProductsByType['personal'])) {
                $this->prepareRecommendationQuery('Recomendation/PersonalRecommendation', null, ['rrUserId' => $rrUserId], $recommendedProductsByType['personal']);
            }

            \App::retailrocketClient()->execute();

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

            if (isset($recommendedProductsByType['alsoBought'])) {
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

            }
        }

        return new \Http\JsonResponse([
            'success'=> true,
            'recommend' => [
                'alsoBought' => !empty($recommendedProductsByType['alsoBought']) ? [
                    'content' => \App::closureTemplating()->render(\App::abTest()->isNewProductPage() ? 'product-page/blocks/slider' : 'product/__slider', [
                        'products' => array_values($recommendedProductsByType['alsoBought']),
                        'class' => sprintf('slideItem-%sitem', $showLimit ?: 7),
                        'title' => count($productIds) > 1 ? 'С этими товарами покупают' : 'С этим товаром покупают',
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
                        'class' => sprintf('slideItem-%sitem', $showLimit ?: 7),
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
                        'class' => sprintf('slideItem-%sitem', $showLimit ?: 7),
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