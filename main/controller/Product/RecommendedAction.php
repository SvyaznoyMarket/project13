<?php

namespace Controller\Product;

class RecommendedAction {

    public function execute(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::retailrocketClient();
        $templating = \App::closureTemplating();
        $region = \App::user()->getRegion();

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

            // ид товаров
            $productIds = [];
            if ($productId) {
                $productIds[] = $productId;
            }

            // получение ид рекомендаций
            $sender = null;
            foreach ($sendersByType as &$sender) {
                if ('retailrocket' == $sender['name']) {
                    if ('alsoBought' == $sender['type']) {
                        $sender['method'] = 'CrossSellItemToItems';
                        $client->addQuery('Recomendation/' . $sender['method'], $productId, $queryParams, [], function($data) use (&$sender, &$productIds, &$productLimitInSlice) {
                            if (!is_array($data)) return;

                            $sender['items'] = array_slice($data, 0, $productLimitInSlice);
                            $productIds = array_merge($productIds, $sender['items']);
                        });
                    } else if ('similar' == $sender['type']) {
                        $sender['method'] = 'UpSellItemToItems';
                        $client->addQuery('Recomendation/' . $sender['method'], $productId, $queryParams, [], function($data) use (&$sender, &$productIds, &$productLimitInSlice) {
                            if (!is_array($data)) return;

                            $sender['items'] = array_slice($data, 0, $productLimitInSlice);
                            $productIds = array_merge($productIds, $sender['items']);
                        });
                    } else if ('alsoViewed' == $sender['type']) {
                        $sender['method'] = 'ItemToItems';
                        $client->addQuery('Recomendation/' . $sender['method'], $productId, $queryParams, [], function($data) use (&$sender, &$productIds, &$productLimitInSlice) {
                            if (!is_array($data)) return;

                            $sender['items'] = array_slice($data, 0, $productLimitInSlice);
                            $productIds = array_merge($productIds, $sender['items']);
                        });
                    }
                }

                if ('viewed' == $sender['type']) {
                    $sender['method'] = '';

                    //$data = $request->cookies->get('rrviewed');
                    $data = $request->get('rrviewed');
                    if (is_string($data)) {
                        $data = explode(',', $data);
                    }
                    if (empty($data)) {
                        $data = explode(',', (string)$request->cookies->get('product_viewed'));
                    }
                    if (is_array($data)) {
                        $data = array_reverse(array_filter($data));
                        $sender['items'] = array_slice(array_unique($data), 0, $productLimitInSlice);
                        $productIds = array_merge($productIds, $sender['items']);
                    }
                }
            }
            unset($sender);

            $client->execute(null, 1); // 1-й пакет запросов

            $productIds = array_filter(array_values(array_unique($productIds)));

            /** @var \Model\Product\Entity[] $productsById */
            $productsById = [];
            $medias = [];
            foreach (array_chunk($productIds, \App::config()->coreV2['chunk_size'], true) as $productsInChunk) {
                \RepositoryManager::product()->useV3()->withoutModels()->withoutPartnerStock()->prepareCollectionById($productsInChunk, $region, function($data) use (&$productsById) {
                    foreach ((array)$data as $item) {
                        if (empty($item['id'])) continue;

                        $productsById[$item['id']] = new \Model\Product\Entity($item);
                    }
                });

                \RepositoryManager::product()->prepareProductsMediasByIds($productsInChunk, $medias);
            }

            $client->execute(); // 2-й пакет запросов

            \RepositoryManager::product()->setMediasForProducts($productsById, $medias);

            /**
             * Главный товар
             * @var \Model\Product\Entity|null $product
             */
            $product = ($productId && isset($productsById[$productId])) ? $productsById[$productId] : null;
            if ($productId && !$product) {
                throw new \Exception(sprintf('Товар #%s не найден', $productId));
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
                foreach ($sender['items'] as $id) {
                    /** @var \Model\Product\Entity|null $iProduct */
                    $iProduct = isset($productsById[$id]) ? $productsById[$id] : null;
                    if (!$iProduct || !$iProduct->isAvailable() || $iProduct->isInShopShowroomOnly() || (5 == $iProduct->getStatusId())) continue;

                    $products[] = $iProduct;
                }

                if (!(bool)$products) {
                    $recommendData[$type] = [
                        'success' => false,
                    ];

                    continue;
                }

                // сортировка
                if ('viewed' != $sender['type']) {
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

                $template = \App::abTest()->isNewProductPage() && 'viewed' != $sender['type'] ? 'product-page/blocks/slider' : 'product/__slider';
//                $template = 'product/__slider';

                $recommendData[$type] = [
                    'success'   => true,
                    'content'   => $templating->render($template, [
                        'title'          => $this->getTitleByType($type),
                        'products'       => $products,
                        'count'          => count($products),
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