<?php

namespace Controller\Search;

class RecommendedAction {
    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     */
    public function execute(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::retailrocketClient();
        $templating = \App::closureTemplating();
        $recommendController = new \Controller\Product\RecommendedAction();
        $region = \App::user()->getRegion();

        $searchQuery = (new \Controller\Search\Action())->getSearchQueryByRequest($request);

        // поставщик из http-запроса
        $sendersByType = $recommendController->getSendersIndexedByTypeByHttpRequest($request);

        // ид пользователя retail rocket
        $queryParams = [
            'keyword' => $searchQuery,
        ];
        if ($rrUserId = $request->cookies->get('rrpusid')) {
            //$queryParams['rrUserId'] = $rrUserId;
        }

        // ид товаров
        $productIds = [];

        // получение ид рекомендаций
        $sender = null;
        foreach ($sendersByType as &$sender) {
            if ('retailrocket' == $sender['name']) {
                if ('search' == $sender['type']) {
                    $sender['method'] = 'SearchToItems';
                    $client->addQuery('Recomendation/' . $sender['method'], null, $queryParams, [], function($data) use (&$sender, &$productIds) {
                        if (!is_array($data)) return;

                        $sender['items'] = array_slice($data, 0, 50);
                        $productIds = array_merge($productIds, $sender['items']);
                    });
                }
            }
        }
        unset($sender);

        $client->execute(); // 1-й пакет запросов

        $productIds = array_values(array_unique($productIds));

        /** @var \Model\Product\Entity[] $productsById */
        $productsById = [];
        foreach ($productIds as $productId) {
            $productsById[$productId] = new \Model\Product\Entity(['id' => $productId]);
        }

        \RepositoryManager::product()->prepareProductQueries($productsById, 'category label media brand');

        $client->execute(); // 2-й пакет запросов

        $productsById = array_filter($productsById, function(\Model\Product\Entity $product) {
            return ($product->isAvailable() && !$product->isInShopShowroomOnly());
        });

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

            $recommendData[$type] = [
                'success' => true,
                'content' => $templating->render('product/__slider', [
                    'title'                        => $recommendController->getTitleByType($type),
                    'products'                     => $products,
                    'class'                        => 'slideItem-7item',
                    'sender'                       => $sender,
                ]),
                'data' => [
                    'id'              => $searchQuery, //id товара (или категории, пользователя или поисковая фраза) к которому были отображены рекомендации
                    'method'          => $sender['method'], //алгоритм (ItemToItems, UpSellItemToItems, CrossSellItemToItems и т.д.)
                    'recommendations' => $sender['items'], //массив ids от Retail Rocket
                ],
            ];
        }
        $responseData['recommend'] = $recommendData;

        // http-ответ
        return new \Http\JsonResponse($responseData);
    }
}