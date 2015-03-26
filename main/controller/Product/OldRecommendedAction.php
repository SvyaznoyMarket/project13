<?php

namespace Controller\Product;

class OldRecommendedAction {
    /**
     * @param string        $productId
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     */
    public function execute(\Http\Request $request, $productId) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $rrConfig = \App::config()->partners['RetailRocket'];
        $region = \App::user()->getRegion();

        $sendersByType = [];
        foreach ((array)$request->query->get('senders') as $sender) {
            if (!is_array($sender)) {
                continue;
            }

            $sender = array_merge(['name' => null, 'position' => null, 'type' => null, 'method' => null, 'from' => null], $sender);
            if (empty($sender['name'])) {
                $sender['name'] = 'enter';
            }

            $sendersByType[$sender['type']] = $sender;
        }

        $recommend = [
            'alsoBought' => null,
            'similar'    => null,
            'alsoViewed' => null,
        ];

        if (!\App::user()->getToken()) $recommend['personal'] = null;

        $recommEngine = [];
        $productsCollection = $recommend;
        $ids = $recommend;
        $controller = array_merge($recommend, [
            'alsoBought' => new \Controller\Product\UpsaleAction(),
            'similar'    => new \Controller\Product\SimilarAction(),
            'alsoViewed' => new \Controller\Product\AlsoViewedAction(),
            'personal' => new \Controller\Product\PersonalAction()
        ]);

        try {
            $product = \RepositoryManager::product()->getEntityById($productId);

            if (!$product) {
                throw new \Exception(sprintf('Товар #%s не найден', $productId));
            }

            $productId = $product->getId();

            // запрашиваем $ids продуктов для всех типов рекоммендаций
            $allIds = [];
            foreach ($ids as $type => $item) {
                $method = $controller[$type]->getRetailrocketMethodName();

                if ("personal" === $type) {
                    $queryUrl = "{$rrConfig['apiUrl']}Recomendation/$method/{$rrConfig['account']}/?rrUserId=" . $request->cookies->get('rrpusid');
                } else {
                    $queryUrl = "{$rrConfig['apiUrl']}Recomendation/$method/{$rrConfig['account']}/$productId";
                }

                \App::curl()->addQuery($queryUrl, [], function ($data) use (&$ids, &$allIds, $type, $product) {
                    $ids[$type] = $data;

                    // для блока "С этим товаром покупают" к $ids добавляем связные товары
                    if ('alsoBought' === $type && is_array($ids[$type])) {
                        $ids[$type] = array_unique(array_merge($product->getRelatedId(), $ids[$type]));
                    }

                    $allIds = array_unique(array_merge($allIds, (array)$ids[$type]));
                }, function(\Exception $e) {
                    \App::exception()->remove($e);
                }, $rrConfig['timeout']);
            }
            \App::curl()->execute(null, 1);

            // SITE-3625 Персонализация рекомендаций
            foreach ($ids as $type => &$item) {
                if (isset($ids['personal']) && $type !== 'personal') {
                    $personalIntersect = array_intersect($ids['personal'], $item); // пересечение рекомендаций
                    $personalDiff = array_diff($item, $ids['personal']); // diff рекомендаций
                    $item = array_merge($personalIntersect, $personalDiff); // персонализированный результат
                }
            }

            // SITE-3221 Исключить повторяющиеся товары из рекомендаций RR
            if (is_array($ids['similar']) && is_array($ids['alsoViewed'])) {
                $ids['alsoViewed'] = array_diff($ids['alsoViewed'], array_slice($ids['similar'], 0, 5));
            }

            /**
             * Для всех продуктов расставим и запомним источники (движок, Engine) рекомендаций
             */
            foreach ($ids as $type => $item) {
                if (!is_array($item)) continue;
                foreach($item as $id) {
                    $recommEngine[$id] = [
                        'id'        => $id,
                        'engine'    => $controller[$type]->getEngine() ?: $controller[$type]->getName(),
                        'name'      => $controller[$type]->getName(),
                        'method'    => $controller[$type]->getRetailrocketMethodName(),
                    ];
                }
            }
            foreach ($product->getRelatedId() as $id) {
                $recommEngine[$id] = [
                    'id'        => $id,
                    'engine'    => 'enter',
                    'name'      => 'enter',
                    'method'    => null,
                ];
            }

            // запрашиваем $collection товаров для всех типов рекоммендаций
            $collection = [];
            $chunckedIds = array_chunk($allIds, \App::config()->coreV2['chunk_size']);
            foreach ($chunckedIds as $chunk) {
                \RepositoryManager::product()->prepareCollectionById($chunk, $region,
                    function($data) use(&$collection, $recommEngine) {
                    foreach ($data as $value) {
                        if (!isset($value['id']) || !isset($value['link'])) continue;
                        $id = $value['id'];
                        if (isset($recommEngine[$id])) {
                            \Controller\Product\BasicRecommendedAction::prepareLink(
                                $value['link'], $recommEngine[$id]
                            );
                        }
                        $entity = new \Model\Product\Entity($value);
                        $collection[$entity->getId()] = $entity;
                    }
                });
            }
            \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);

            // разбиваем товары по типам рекомендаций
            foreach ($productsCollection as $type => $item) {
                if (!isset($ids[$type]) || !is_array($ids[$type])) continue;
                foreach ($ids[$type] as $id) {
                    if (
                        !array_key_exists($type, $productsCollection)
                        || !isset($collection[$id])
                    ) continue;

                    $productsCollection[$type][] = $collection[$id];
                }
            }

            // подготавливаем контент для всех типов рекомендаций
            foreach ($recommend as $type => $item) {
                if (empty($productsCollection[$type])) {
                    $recommend[$type] = [
                        'success' => false,
                    ];
                    continue;
                }
                if ('alsoBought' === $type && is_array($productsCollection[$type])) {
                    // SITE-2818 Из блока "С этим товаром покупают" убраем товары, которые есть только в магазинах ("Резерв" и витринные)
                    foreach ($productsCollection[$type] as $key => $value) {
                        if (!$value instanceof \Model\Product\BasicEntity) continue;
                        if ($value->isInShopOnly() || $value->isInShopStockOnly() || !$value->getIsBuyable()) {
                            unset($productsCollection[$type][$key]);
                        }
                    }

                    $products = array_slice($productsCollection[$type], 0, \App::config()->product['itemsInSlider'] * 2);
                } else {
                    $products = $this->prepareProducts($productsCollection[$type], $controller[$type]->getName());
                }

                if (!is_array($products)) {
                    throw new \Exception(sprintf('Not found products data in response. ActionType: %s', $controller[$type]->getActionType()));
                }

                $products = array_filter($products, function($product) {
                    return $product instanceof \Model\Product\BasicEntity;
                });

                $method = $controller[$type]->getRetailrocketMethodName() ? $controller[$type]->getRetailrocketMethodName() : null;

                // поставщик
                $sender = isset($sendersByType[$type]['type']) ? $sendersByType[$type] : [];
                if ($sender) {
                    $sender['method'] = $method;
                }

                $recommend[$type] = [
                    'success' => true,
                    'content' => \App::closureTemplating()->render('product/__slider', [
                        'title'                        => $controller[$type]->getActionTitle(),
                        'products'                     => $products,
                        'count'                        => count($products),
                        'isRetailrocketRecommendation' => true,
                        'retailrocketMethod'           => $method,
                        'retailrocketIds'              => $ids[$type] ? $ids[$type] : [],
                        'sender'                       => $sender,
                    ]),
                    'data' => [
                        'id' => $product->getId(),//id товара (или категории, пользователя или поисковая фраза) к которому были отображены рекомендации
                        'method' => $method,//алгоритм (ItemToItems, UpSellItemToItems, CrossSellItemToItems и т.д.)
                        'recommendations' => $ids[$type] ? $ids[$type] : [],//массив ids от Retail Rocket
                    ],
                ];
            }

            $responseData = [
                'success' => true,
                'recommend' => $recommend
            ];
        } catch (\Exception $e) {
            $responseData = [
                'success' => false,
                'error'   => ['code' => $e->getCode(), 'message' => $e->getMessage()],
            ];
        }

        return new \Http\JsonResponse($responseData);
    }


    /**
     * @param $products
     * @return mixed
     * @throws \Exception
     */
    protected function prepareProducts($products, $recommendName) {
        if (!(bool)$products) {
            throw new \Exception('Рекомендации не получены');
        }

        foreach ($products as $i => $product) {
            /* @var \Model\Product\Entity $product */

            if (!$product instanceof \Model\Product\BasicEntity || !$product->getIsBuyable())  {
                unset($products[$i]);
                continue;
            }

            /*$link = $product->getLink();
            $link = $link . (false === strpos($link, '?') ? '?' : '&') . 'sender=retailrocket|' . $product->getId();

            if ('upsale' == $recommendName) {
                $link = $link . (false === strpos($link, '?') ? '?' : '&') . 'from=cart_rec';
                $product->setIsUpsale(true);
            }

            $product->setLink($link);*/
            if ('upsale' === $recommendName) {
                $product->setIsUpsale(true);
            }
        }

        if (!(bool)$products) {
            throw new \Exception('Нет товаров');
        }

        return $products;
    }
}