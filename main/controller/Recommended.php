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
        $showLimit = (int)$request->get('showLimit') ?: null;
        $recommendedProductsByType = call_user_func(function() use($request) {
            $types = $request->query->get('types');
            if (!is_array($types)) {
                return [];
            }
            $types = array_flip($types);
            return $types;
        });

        $recommendHtml = [];

        if ($recommendedProductsByType) {
            $recommendations = \App::richRelevanceClient()->query(
                'recsForPlacements',
                [
                    'placements' => implode('|', array_keys($recommendedProductsByType)),
                    'productId' => implode('|', is_array($request->query->get('productIds')) ? $request->query->get('productIds') : [])
                ]
            );

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
                if (is_array($recommendedProductsByType)) {
                    foreach ($recommendedProductsByType as $type => $products) {
                        if (is_array($products)) {
                            foreach ($products as $product) {
                                /** @var \Model\Product\Entity $product */
                                if (!isset($productsById[$product->id])) {
                                    unset($recommendedProductsByType[$type][$product->id]);
                                }
                            }
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
        }

        return new \Http\JsonResponse([
            'success'=> true,
            'recommend' => $recommendHtml,
        ]);
    }
} 