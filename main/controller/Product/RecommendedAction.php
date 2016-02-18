<?php

namespace Controller\Product;

use Model\Product\Entity as Product;
use Model\RetailRocket\RetailRocketRecommendation;

class RecommendedAction {

    public function execute(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::retailrocketClient();
        $templating = \App::closureTemplating();

        $productLimitInSlice = 15;

        $productId = $request->get('productId');
        if (empty($productId)) {
            $productId = null;
        }

        try {
            // поставщик из http-запроса
            $sendersByType = $this->getSendersIndexedByTypeByHttpRequest($request);

            // ид пользователя retail rocket
            $queryParams = [];
            if ($rrUserId = $request->cookies->get('rrpusid')) {
                $queryParams['rrUserId'] = $rrUserId;
            }

            // ID товаров
            $productIds = [];
            $productsById = [];

            if ($productId) {
                $productIds[] = $productId;
                $productsById[$productId] = new Product(['id' => $productId]);
            }

            // получение ид рекомендаций
            $sender = null;
            $recommendations = [];
            $noRichQueryExecuted = true;

            foreach ($sendersByType as &$sender) {

                if ('rich' == $sender['name'] && $noRichQueryExecuted) {

                    $recommendations = \App::richRelevanceClient()->query('recsForPlacements', [
                        'placements' => 'item_page.cross_sell|item_page.rr1|item_page.rr2|item_page.not_in_stock',
                        'productId' => $productId,
                    ]);

                    $noRichQueryExecuted = false;

                    foreach ($recommendations as $recommendation) {
                        $productsById = array_replace($productsById, $recommendation->getProductsById());
                    }

                } elseif ('retailrocket' == $sender['name']) {

                    if ('alsoBought' == $sender['type']) {
                        $sender['method'] = 'CrossSellItemToItems';
                        $client->addQuery('Recomendation/' . $sender['method'], $productId, $queryParams, [],
                            function($data) use (&$recommendations, &$sender, &$productsById, &$productLimitInSlice) {
                                $recommendations['alsoBought'] = new RetailRocketRecommendation([
                                    'products'  => $data,
                                    'position'  => 'alsoBought',
                                    'message'   => 'С этим товаром покупают'

                                ]);
                                $productsById = array_replace($productsById, $recommendations['alsoBought']->getProductsById());
                            }
                        );
                    } else if ('similar' == $sender['type']) {
                        $sender['method'] = 'UpSellItemToItems';
                        $client->addQuery('Recomendation/' . $sender['method'], $productId, $queryParams, [],
                            function($data) use (&$recommendations, &$sender, &$productsById, &$productLimitInSlice) {
                                $recommendations['similar'] = new RetailRocketRecommendation([
                                    'products'  => $data,
                                    'position'  => 'similar',
                                    'message'   => 'Похожие товары'

                                ]);
                                $productsById = array_replace($productsById, $recommendations['similar']->getProductsById());
                            }
                        );
                    } else if ('alsoViewed' == $sender['type']) {
                        $sender['method'] = 'ItemToItems';
                        $client->addQuery('Recomendation/' . $sender['method'], $productId, $queryParams, [],
                            function($data) use (&$recommendations, &$sender, &$productsById, &$productLimitInSlice) {
                                $recommendations['alsoViewed'] = new RetailRocketRecommendation([
                                    'products'  => $data,
                                    'position'  => 'alsoViewed',
                                    'message'   => 'C этим товарам также смотрят'

                                ]);
                                $productsById = array_replace($productsById, $recommendations['alsoViewed']->getProductsById());
                            }
                        );
                    }
                }

                // Просмотренные товары
                if ('viewed' == $sender['type']) {
                    $sender['method'] = '';

                    $data = $request->get('rrviewed');
                    if (is_string($data)) {
                        $data = explode(',', $data);
                    }
                    if (empty($data)) {
                        $data = explode(',', (string)$request->cookies->get('product_viewed'));
                    }
                    if (is_array($data)) {
                        $data = array_reverse(array_filter($data, function($productId) { return (int)$productId; }));
                        $sender['items'] = array_slice(array_unique($data), 0, $productLimitInSlice);
                        $productIds = array_merge($productIds, $sender['items']);
                    }
                }
            }
            unset($sender);

            $client->execute(null, 1); // 1-й пакет запросов

            $productIds = array_filter(array_values(array_unique($productIds)));

            /** @var \Model\Product\Entity[] $productsById */

            if (!$productsById) {
                call_user_func(
                    function () use (&$productsById, &$productIds) {
                        foreach ($productIds as $productId) {
                            $productsById[$productId] = new \Model\Product\Entity(['id' => $productId]);
                        }
                    });
            }

            \RepositoryManager::product()->prepareProductQueries($productsById, 'media label category brand');
            $client->execute();

            /**
             * Главный товар
             * @var \Model\Product\Entity|null $product
             */
            $product = ($productId && isset($productsById[$productId])) ? $productsById[$productId] : null;
            if ($productId && !$product) {
                // throw new \Exception(sprintf('Товар #%s не найден', $productId));
            }

            // SITE-3221 Исключить повторяющиеся товары из рекомендаций RR
            if (isset($sendersByType['alsoViewed']) && isset($sendersByType['similar'])) {
                $sendersByType['alsoViewed']['items'] = array_diff($sendersByType['alsoViewed']['items'], array_slice($sendersByType['similar']['items'], 0, 5));
            }

            // ответ
            $responseData = [
                'success'   => true,
                'recommend' => [],
            ];

            $recommendData = [];
            foreach ($sendersByType as $type => $sender) {
                $products = [];

                if (array_key_exists($type, $recommendations)) {
                    foreach ($recommendations[$type]->getProductsById() as $recProduct) {
                        /** @var \Model\Product\Entity|null $iProduct */
                        $iProduct = isset($productsById[$recProduct->id]) ? $productsById[$recProduct->id] : null;
                        if (!$iProduct || !$iProduct->isAvailable() || $iProduct->isInShopShowroomOnly(
                            ) || (5 == $iProduct->getStatusId())
                        ) {
                            continue;
                        }

                        $products[] = $iProduct;
                    }
                }

                if ($type == 'viewed') {
                    $products = $productsById;
                }

                if (!(bool)$products) {
                    $recommendData[$type] = [
                        'success' => false,
                    ];

                    continue;
                }

                $cssClass = '';
                $namePosition = null;
                $rowsCount = 1;
                if ('viewed' == $sender['type']) {
                    $cssClass = 'slideItem-viewed';
                    $namePosition = 'none';
                } else if (in_array($sender['type'], ['alsoViewed'])) {
                    $cssClass = 'slideItem-7item';
                }

                if (('similar' == $sender['type']) && ('ProductMissing' == $sender['position'])) {
                    $cssClass = 'slideItem-3item';
                    $rowsCount = 2;
                }

                $template = ('viewed' != $sender['type']) ? 'product-page/blocks/slider' : 'product/__slider';
                if (\App::config()->lite['enabled']) $template = 'product/blocks/slider';

                $recommendData[$type] = [
                    'success'   => true,
                    'content'   => $templating->render($template, [
                        'title'          => isset($recommendations[$type]) ? $recommendations[$type]->getMessage() : $this->getTitleByType($type),
                        'products'       => $products,
                        'sender'         => $sender,
                        'sender2'        => (string)$request->get('sender2'),
                        'class'          => $cssClass,
                        'rowsCount'      => $rowsCount,
                        'namePosition'   => $namePosition,
                        'isCompact'      => in_array($sender['type'], ['viewed']),
                        'containerStyle' => (in_array($sender['type'], ['similar']) && !$product->isAvailable()) ? 'width: 430px;' : '',
                    ]),
                    'data'      => [
                        'id'              => $product ? $product->getId() : null, //id товара (или категории, пользователя или поисковая фраза) к которому были отображены рекомендации
                        'method'          => $sender['method'], //алгоритм (ItemToItems, UpSellItemToItems, CrossSellItemToItems и т.д.)
                        'recommendations' => $sender['items'], //массив ids от Retail Rocket
                    ],
                ];
            }
            $responseData['recommend'] = $recommendData;

        } catch (\Exception $e) {
            $responseData = [
                'success' => false,
                'error'   => ['code' => $e->getCode(), 'message' => $e->getMessage()],
            ];
        }

        // http-ответ
        return new \Http\JsonResponse($responseData);
    }

    /**
     * @param string $type
     * @return string
     */
    public function getTitleByType($type) {
        $titlesByType = [
            'accessorize' => 'Аксессуары',
            'alsoBought'  => 'С этим товаром покупают',
            'similar'     => 'Похожие товары',
            'alsoViewed'  => 'С этим товаром также смотрят',
            'search'      => 'Возможно, вам подойдут',
            'viewed'      => 'Вы смотрели',
        ];

        return isset($titlesByType[$type]) ? $titlesByType[$type] : 'Мы рекомендуем';
    }

    /**
     * @param \Http\Request $request
     * @return array
     */
    public function getSendersIndexedByTypeByHttpRequest(\Http\Request $request) {
        // поставщик из http-запроса
        $sendersByType = [];
        foreach ((array)$request->get('senders') as $sender) {
            if (!is_array($sender)) {
                continue;
            }

            $sender = array_merge(['name' => null, 'position' => null, 'type' => null, 'method' => null, 'items' => []], $sender);
            if (empty($sender['name'])) {
                $sender['name'] = 'enter';
            }

            $sendersByType[$sender['type']] = $sender;
        }

        return $sendersByType;
    }
}