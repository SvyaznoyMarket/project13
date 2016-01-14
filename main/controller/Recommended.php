<?php
namespace Controller;

use Model\RetailRocket\RetailRocketRecommendation;

class Recommended {
    /**
     * GET параметр "types" - возвращаемые рекомендации. Допустимые значения: alsoBought, popular, personal
     * GET параметр "productIds" - ID товаров, для которых будут получены рекомендации alsoBought и которые будут
     *                             исключены из всех рекомендуемых товаров
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     */
    public function execute(\Http\Request $request) {
        $productIds = is_array($request->query->get('productIds')) ? $request->query->get('productIds') : [];
        $rrUserId = $request->cookies->get('rrpusid');
        $sender = @$request->get('sender', [])['name'] ? : 'retailrocket';
        $recommendations = [];

        $showLimit = (int)$request->get('showLimit') ?: null;

        $recommendedProductsByType = call_user_func(function() use($request) {

            $types = $request->query->get('types');

            if (!is_array($types)) {
                return [];
            }

            $types = array_flip($types);

            return $types;
        });

        if ($sender == 'rich') {
            $recommendations = \App::richRelevanceClient()->query(
                'recsForPlacements',
                [
                    'placements' => implode('|', array_keys($recommendedProductsByType)),
                    'productId' => implode('|', $productIds)
                ]
            );
        }

        if ($recommendedProductsByType) {

            if (isset($recommendedProductsByType['alsoBought'])) {
                foreach ($productIds as $productId) {
                    $this->prepareRecommendationQuery(
                        'Recomendation/CrossSellItemToItems',
                        $productId,
                        [],
                        $recommendations,
                        'alsoBought'
                    );
                }
            }

            if (isset($recommendedProductsByType['popular'])) {
                $this->prepareRecommendationQuery(
                    'Recomendation/ItemsToMain',
                    null,
                    [],
                    $recommendations,
                    'popular'
                );
            }

            if (isset($recommendedProductsByType['personal']) && $rrUserId) {
                $this->prepareRecommendationQuery(
                    'Recomendation/PersonalRecommendation',
                    null,
                    ['rrUserId' => $rrUserId],
                    $recommendations,
                    'personal'
                );
            }

            \App::retailrocketClient()->execute();

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

        $recommendHtml = [];
        foreach ($recommendations as $recommendation) {
            $recommendHtml[$recommendation->getPlacement()] = [
                'content' => \App::closureTemplating()->render(
                    'product-page/blocks/slider',
                    [
                        'products' => $recommendation->getProductsById(),
                        'class' => sprintf('slideItem-%sitem', $showLimit ?: 7),
                        'title' => $recommendation->getMessage(),
                        'sender' => [
                            'name' => $recommendation->getSenderName(),
                            'method' => $recommendation->getPlacement(),
                            'position' => 'Basket',
                        ],
                    ]
                ),
                'success' => true,
            ];
        }

        return new \Http\JsonResponse([
            'success'=> true,
            'recommend' => $recommendHtml,
        ]);
    }

    /**
     * @param string $method
     * @param null|string|int $itemId
     * @param array $params
     * @param array $recommendations
     * @param string $key
     */
    private function prepareRecommendationQuery($method, $itemId = null, array $params = [], array &$recommendations, $key) {
        \App::retailrocketClient()->addQuery(
            $method,
            $itemId,
            $params,
            [],
            function($data) use(&$recommendations, $key) {

                switch ($key) {
                    case 'alsoBought': $message = 'С этим товаром покупают'; break;
                    case 'personal': $message = 'Мы рекомендуем'; break;
                    case 'popular': $message = 'Популярные товары'; break;
                    default: $message = 'Рекомендации';
                }
                $recommendations[$key] = new RetailRocketRecommendation([
                    'products' => $data,
                    'placement' => $key,
                    'message'   => $message
                ]);
            },
            function(\Exception $e) {
                \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' .  __LINE__], ['fatal', 'recommendation', 'retailrocket']);
                \App::exception()->remove($e);
            },
            1.15
        );
    }
} 