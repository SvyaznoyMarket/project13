<?php

namespace Controller\Product;

class RecommendedAction {

    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::retailrocketClient();
        $templating = \App::closureTemplating();
        $region = \App::user()->getRegion();

        $productId = $request->get('productId') ?: null;

        if ($test = \App::abTest()->getTest('recommended_product')) {
            if ($test->getEnabled() && $test->getChosenCase() && ('old_recommendation' == $test->getChosenCase()->getKey())) {
                return (new \Controller\Product\OldRecommendedAction())->execute($request, $productId);
            }
        }

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
                    $client->addQuery('Recomendation/' . $sender['method'], $productId, $queryParams, [], function($data) use (&$sender, &$productIds) {
                        if (!is_array($data)) return;

                        $sender['items'] = array_slice($data, 0, 50);
                        $productIds = array_merge($productIds, $sender['items']);
                    });
                } else if ('similar' == $sender['type']) {
                    $sender['method'] = 'UpSellItemToItems';
                    $client->addQuery('Recomendation/' . $sender['method'], $productId, $queryParams, [], function($data) use (&$sender, &$productIds) {
                        if (!is_array($data)) return;

                        $sender['items'] = array_slice($data, 0, 50);
                        $productIds = array_merge($productIds, $sender['items']);
                    });
                } else if ('alsoViewed' == $sender['type']) {
                    $sender['method'] = 'ItemToItems';
                    $client->addQuery('Recomendation/' . $sender['method'], $productId, $queryParams, [], function($data) use (&$sender, &$productIds) {
                        if (!is_array($data)) return;

                        $sender['items'] = array_slice($data, 0, 50);
                        $productIds = array_merge($productIds, $sender['items']);
                    });
                } else if ('viewed' == $sender['type']) {
                    $sender['method'] = '';

                    //$ids = $request->cookies->get('rrviewed');
                    $ids = $request->get('rrviewed');
                    if (is_string($ids)) {
                        $ids = explode(',', $ids);
                    }
                    if (is_array($ids)) {
                        $sender['items'] = array_slice(array_unique($ids), 0, 50);
                        $productIds = array_merge($productIds, $sender['items']);
                    }
                }
            }
        }
        unset($sender);

        $client->execute(); // 1-й пакет запросов

        $productIds = array_values(array_unique($productIds));

        $productsById = [];
        foreach (array_chunk($productIds, \App::config()->coreV2['chunk_size'], true) as $productsInChunk) {
            \RepositoryManager::product()->prepareCollectionById($productsInChunk, $region, function($data) use (&$productsById) {
                foreach ((array)$data as $item) {
                    if (empty($item['id'])) continue;

                    $productsById[$item['id']] = new \Model\Product\Entity($item);
                }
            });
        }

        $client->execute(); // 2-й пакет запросов

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
            'success'   => false,
            'recommend' => [],
        ];

        $recommendData = [];
        foreach ($sendersByType as $type => $sender) {
            $products = [];
            foreach ($sender['items'] as $id) {
                $iProduct = isset($productsById[$id]) ? $productsById[$id] : null;
                if (!$iProduct) continue;

                $products[] = $iProduct;
            }

            if (!(bool)$products) {
                $recommendData[$type] = [
                    'success' => false,
                ];

                continue;
            }

            // сортировка
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

            $cssClass = '';
            $namePosition = null;
            if ('viewed' == $sender['type']) {
                $cssClass = 'slideItem-viewed';
                $namePosition = 'none';
            } else if ('alsoViewed' == $sender['type']) {
                $cssClass = 'slideItem-7item';
            }

            $recommendData[$type] = [
                'success' => true,
                'content' => $templating->render('product/__slider', [
                    'title'        => $this->getTitleByType($type),
                    'products'     => $products,
                    'count'        => count($products),
                    'sender'       => $sender,
                    'class'        => $cssClass,
                    'namePosition' => $namePosition,
                ]),
                'data' => [
                    'id'              => $product ? $product->getId() : null, //id товара (или категории, пользователя или поисковая фраза) к которому были отображены рекомендации
                    'method'          => $sender['method'], //алгоритм (ItemToItems, UpSellItemToItems, CrossSellItemToItems и т.д.)
                    'recommendations' => $sender['items'], //массив ids от Retail Rocket
                ],
                'hasBubble' => in_array($sender['type'], ['viewed']),
            ];
        }
        $responseData['recommend'] = $recommendData;

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
            'alsoBought'  => 'С этим товаром также покупают',
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
        foreach ((array)$request->query->get('senders') as $sender) {
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