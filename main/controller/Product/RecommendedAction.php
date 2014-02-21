<?php

namespace Controller\Product;

class RecommendedAction {
    /**
     * @param string        $productId
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     */
    public function execute(\Http\Request $request, $productId) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $rrConfig = \App::config()->partners['RetailRocket'];
        $region = \App::user()->getRegion();

        $recommend = [
            'alsoBought' => null,
            'similar'    => null,
            'alsoViewed' => null
        ];
        $productsCollection = $recommend;
        $ids = $recommend;
        $controller = array_merge($recommend, [
            'alsoBought' => new \Controller\Product\UpsaleAction(),
            'similar'    => new \Controller\Product\SimilarAction(),
            'alsoViewed' => new \Controller\Product\AlsoViewedAction()
        ]);

        $responseData = [];
        try {
            $product = \RepositoryManager::product()->getEntityById($productId);

            if (!$product) {
                throw new \Exception(sprintf('Товар #%s не найден', $productId));
            }

            $key = \App::abTest()->getCase()->getKey();
            \App::logger()->info(sprintf('abTest.key=%s, response.cookie.switch=%s', $key, $request->cookies->get('switch')));

            $productId = $product->getId();

            // запрашиваем $ids продуктов для всех типов рекоммендаций
            $allIds = [];
            foreach ($ids as $type => $item) {
                $method = $controller[$type]->getRetailrocketMethodName();
                $queryUrl = "{$rrConfig['apiUrl']}Recomendation/$method/{$rrConfig['account']}/$productId";

                \App::curl()->addQuery($queryUrl, [], function ($data) use (&$ids, &$allIds, $type, $product) {
                    $ids[$type] = $data;

                    // для блока "С этим товаром также покупают" к $ids добавляем связные товары
                    if ('alsoBought' === $type) {
                        $ids[$type] = array_unique(array_merge($product->getRelatedId(), $ids[$type]));
                    }

                    $allIds = array_unique(array_merge($allIds, $ids[$type]));
                }, function(\Exception $e) {
                    \App::exception()->remove($e);
                }, $rrConfig['timeout']);
            }
            \App::curl()->execute(null, 1);

            // запрашиваем $collection товаров для всех типов рекоммендаций
            $collection = [];
            $chunckedIds = array_chunk($allIds, \App::config()->coreV2['chunk_size']);
            foreach ($chunckedIds as $chunk) {
                \RepositoryManager::product()->prepareCollectionById($chunk, $region, function($data) use(&$collection) {
                    foreach ($data as $value) {
                        $entity = new \Model\Product\Entity($value);
                        $collection[$entity->getId()] = $entity;
                    }
                });
            }
            \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['medium']);

            // разбиваем товары по типам рекомендаций
            foreach ($productsCollection as $type => $item) {
                foreach ($ids[$type] as $id) {
                    $productsCollection[$type][] = $collection[$id];
                }
            }

            // подготавливаем контент для всех типов рекомендаций
            foreach ($recommend as $type => $item) {
                if ('alsoBought' === $type) {
                    // SITE-2818 Из блока "С этим товаром также покупают" убраем товары, которые есть только в магазинах ("Резерв" и витринные)
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

                $recommend[$type] = [
                    'success' => true,
                    'content' => \App::closureTemplating()->render('product/__slider', [
                        'title' => $controller[$type]->getActionTitle(),
                        'products' => $products,
                    ]),
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
            /* @var product Model\Product\Entity */

            if (!$product instanceof \Model\Product\BasicEntity || !$product->getIsBuyable())  {
                unset($products[$i]);
                continue;
            }

            $link = $product->getLink();
            $link = $link . (false === strpos($link, '?') ? '?' : '&') . 'sender=retailrocket|' . $product->getId();

            if ('upsale' == $recommendName) {
                $link = $link . (false === strpos($link, '?') ? '?' : '&') . 'from=cart_rec';
                $product->setIsUpsale(true);
            }

            $product->setLink($link);
        }

        if (!(bool)$products) {
            throw new \Exception('Нет товаров');
        }

        return $products;
    }
}