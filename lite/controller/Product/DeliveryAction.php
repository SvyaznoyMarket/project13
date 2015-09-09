<?php

namespace Controller\Product;
use \Model\Product\Entity as Product;
use Model\Product\Delivery\ProductDelivery;

class DeliveryAction {
    use \EnterApplication\CurlTrait;

    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     * @throws \Exception\NotFoundException
     */
    public function execute(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http request');
        }

        return new \Http\JsonResponse($this->getResponseData($request->get('product'), $request->get('region')));
    }

    /**
     * @param array $product
     * @param int $region
     * @param \EnterQuery\Delivery\GetByCart|null $deliveryQuery
     * @param Product $productModel
     * @return array
     */
    public function getResponseData($product, $region = null, \EnterQuery\Delivery\GetByCart $deliveryQuery = null, &$productModel = null) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $helper = new \View\Helper();
        $user = \App::user();

        try {
            $productIds = array_column($product, 'id');

            $productData = [];
            foreach ((array)$product as $item) {
                if (!isset($item['id'])) {
                    continue;
                }

                $productData[] = [
                    'id'       => (int)$item['id'],
                    'quantity' => !empty($item['quantity']) ? (int)$item['quantity'] : 1,
                ];
            }

            if (!(bool)$productData) {
                throw new \Exception('Для расчета доставки не передены ид и количества товаров');
            }

            $regionId =
                $region
                    ? (int)$region
                    : $user->getRegionId();
            if (!$regionId) {
                $regionId = $user->getRegion()->getId();
            }

            $exception = null;
            $result = [];
            if ($deliveryQuery) {
                $result = [
                    'product_list'  => $deliveryQuery->response->products,
                    'interval_list' => $deliveryQuery->response->intervals,
                    'shop_list'     => $deliveryQuery->response->shops,
                    'geo_list'      => $deliveryQuery->response->regions,
                ];
            } else {
                \App::coreClientV2()->addQuery(
                    'delivery/calc2',
                    [
                        'geo_id' => $regionId
                    ],
                    [
                        'product_list' => $productData
                    ],
                    function($data) use (&$result) {
                        $result = array_merge([
                            'product_list'  => [],
                            'interval_list' => [],
                            'shop_list'     => [],
                            'geo_list'      => [],
                        ], $data);
                    },
                    function(\Exception $e) use (&$exception) {
                        $exception = $e;
                        \App::exception()->remove($e);
                    },
                    1.5 * \App::config()->coreV2['timeout']
                );

                \App::coreClientV2()->execute();
            }

            if ($exception instanceof \Exception) {
                throw $exception;
            }

            if (empty($result)) {
                throw new \Exception('При расчете доставки получен пустой ответ');
            }

            if (!(bool)$result['product_list']) {
                throw new \Exception('При расчете доставки получен пустой список товаров');
            }

            $responseData = [
                'success'          => true,
                'product'          => [],
                'region'           => [
                    'transportCompany' => \App::user()->getRegion()->getHasTransportCompany(),
                ],
            ];

            $shopData = &$result['shop_list'];
            /** @var \Model\Shop\Entity[] $shops */
            $shops = [];

            // получаем список магазинов
            /*
            \RepositoryManager::shop()->prepareCollectionById(
                array_values(array_map(function($shopItem){
                    return (int)$shopItem['id'];
                }, $shopData)),
                function($data) use (&$shops) {
                    foreach ($data as $item) {
                        if (!isset($item['id'])) continue;
                        $shop = new \Model\Shop\Entity($item);
                        $id = $shop->getId();
                        if ( !isset($shops[$id]) ) {
                            $shops[$id] = $shop;
                        }
                    }
                }
            );
            */
            foreach ($shopData as $shopItem) {
                if (!isset($shopItem['id'])) continue;

                $shop = new \Model\Shop\Entity($item);
                $id = $shop->getId();
                if ( !isset($shops[$id]) ) {
                    $shops[$id] = $shop;
                }
            }


            \App::coreClientV2()->execute();

            if ($productModel instanceof Product) $productModel->delivery = new ProductDelivery($result, $productModel->getId());

            foreach ($result['product_list'] as $item) {
                if (!in_array($item['id'], $productIds)) continue;

                $iProduct = [
                    'id'    => $item['id'],
                    //'token' => $item['token'],
                    'delivery' => [],
                ];

                if (isset($item['delivery_mode_list'])) foreach ($item['delivery_mode_list'] as $deliveryItem) {
                    if (!isset($deliveryItem['date_list']) || !is_array($deliveryItem['date_list'])) continue;

                    $delivery = [
                        'id'    => $deliveryItem['id'],
                        'token' => $deliveryItem['token'],
                        'price' => (new \Helper\TemplateHelper())->formatPrice($deliveryItem['price']),
                        'shop'  => [],
                    ];

                    $firstDate = reset($deliveryItem['date_list']);
                    $firstDate = isset($firstDate['date']) ? new \DateTime($firstDate['date']) : null;
                    $delivery['date'] = $firstDate ?
                        ['value' => $firstDate->format('d.m.Y'), 'name' => $helper->humanizeDate($firstDate)]
                        : [];
                    $delivery['days'] = $helper->getDaysDiff($firstDate);

                    $day = 0;
                    foreach ($deliveryItem['date_list'] as $dateItem) {
                        $day++;
                        if ($day > 7) continue;

                        if (in_array($delivery['token'], ['self', 'now'])) {
                            foreach (isset($dateItem['shop_list'][0]) ? $dateItem['shop_list'] : [] as $shopItem) {
                                if (!isset($shopItem['id']) || !isset($shopData[$shopItem['id']])) continue;

                                /*
                                $address = $shopData[$shopItem['id']]['address'];
                                if (isset($shopData[$shopItem['id']]) && ($regionId != $shopData[$shopItem['id']]['geo_id']) && isset($regionData[$shopData[$shopItem['id']]['geo_id']]['name'])) {
                                    $address = $regionData[$shopData[$shopItem['id']]['geo_id']]['name'] . ', ' . $address;
                                }
                                */

                                $shop = [
                                    'id'        => (int)$shopItem['id'],
                                    'name'      => $shopData[$shopItem['id']]['name'],
                                    // 'name'      => $address,
                                    'regime'    => $shopData[$shopItem['id']]['working_time'], // что за описка "regtime"?
                                    'latitude'  => $shopData[$shopItem['id']]['coord_lat'],
                                    'longitude' => $shopData[$shopItem['id']]['coord_long'],
                                ];

                                if (!in_array($shop, $delivery['shop'])) {
                                    $delivery['shop'][] = $shop;
                                }
                            }

                            // добавляем url к магазинам
                            foreach ($shops as $shop) {
                                foreach ($delivery['shop'] as $key => $shopItem) {
                                    if ($shop && ($shopItem['id'] == $shop->getId()) && $shop->getRegion()) {
                                        $delivery['shop'][$key]['url'] = \App::router()->generate('shop.show', ['regionToken' => $shop->getRegion()->getToken(), 'shopToken' => $shop->getToken()]);
                                    }
                                }
                            }

                        }
                    }

                    $iProduct['delivery'][] = $delivery;
                }

                $responseData['product'][] = $iProduct;
            }
        } catch(\Exception $e) {
            \App::logger()->error($e->getMessage(), ['delivery']);

            $responseData = [
                'success' => false,
                'error'   => [
                    'code' => $e->getCode(),
                    'message' => 'Не удалось расчитать доставку: ' . (\App::config()->debug ? ('' . $e->getMessage()) : ''),
                ],
            ];
        }

        return $responseData;
    }

    public function map ($productId, $productUi) {

        $result = [
            'success' => false
        ];

        /** @var \Model\Product\Entity[] $products */
        $products = [new \Model\Product\Entity(['id' => $productId])];
        \RepositoryManager::product()->prepareProductQueries($products);

        \App::coreClientV2()->execute();

        if (!$products) {
            return new \Http\JsonResponse(['error' => 'Товар не найден']);
        }

        // наличие в магазинах
        /** @var $shopStates \Model\Product\ShopState\Entity[] */
        $shopStates = [];
        call_user_func(function() use($products, &$shopStates) {
            $quantityByShop = [];
            foreach ($products[0]->getStock() as $stock) {
                $quantityShowroom = (int)$stock->getQuantityShowroom();
                $quantity = (int)$stock->getQuantity();
                $shopId = $stock->getShopId();
                if ((0 < $quantity + $quantityShowroom) && !empty($shopId)) {
                    $quantityByShop[$shopId] = [
                        'quantity' => $quantity,
                        'quantityShowroom' => $quantityShowroom,
                    ];
                }
            }

            if ($quantityByShop) {
                /** @var \EnterQuery\Shop\GetByIdList $shopQuery */
                $shopQuery = null;
                call_user_func(function() use ($products, &$shopQuery) {
                    $shopIds = [];
                    foreach ($products[0]->getStock() as $stock) {
                        if (!$stock->getShopId() || !($stock->getQuantity() + $stock->getQuantityShowroom())) continue;

                        $shopIds[] = $stock->getShopId();
                    }

                    if ($shopIds) {
                        $shopQuery = (new \EnterQuery\Shop\GetByIdList($shopIds))->prepare();
                    }
                });

                $this->getCurl()->execute();

                foreach ($shopQuery->response->shops as $item) {
                    $shop = new \Model\Shop\Entity($item);

                    if ($shop->getWorkingTimeToday()) {
                        $shopState = new \Model\Product\ShopState\Entity();

                        $shopState->setShop($shop);
                        $shopState->setQuantity(isset($quantityByShop[$shop->getId()]['quantity']) ? $quantityByShop[$shop->getId()]['quantity'] : 0);
                        $shopState->setQuantityInShowroom(isset($quantityByShop[$shop->getId()]['quantityShowroom']) ? $quantityByShop[$shop->getId()]['quantityShowroom'] : 0);

                        $shopStates[] = $shopState;
                    }
                }
            }
        });

        if (!$products[0]->getIsBuyable() && $products[0]->isInShopShowroom()) {
            $map = new \View\PointsMap\MapView();
            $map->isShowroom = true;
            $map->preparePointsWithShopStates($shopStates);

            return new \Http\JsonResponse([
                'result' => $map,
                'success' => true,
            ]);
        }

        $splitResult = null;
        \App::coreClientV2()->addQuery('cart/split',
            [
                'geo_id'     => \App::user()->getRegionId(),
                'request_id' => \App::$id,
            ],
            [ 'cart' => [
                    'product_list' => [
                        $productId => [
                            'id' => $productId,
                            'quantity'  => 1
                        ]
                    ]
                ]
            ],
            function($data) use(&$splitResult) {
                $splitResult = $data;
            }
        );

        \App::coreClientV2()->execute();

        $order = new \Model\OrderDelivery\Entity($splitResult);

        if ($order && $order->orders) {
            $map = new \View\PointsMap\MapView();
            $map->preparePointsWithOrder(reset($order->orders), $order);


            foreach ($products[0]->getStock() as $stock) {
                if ($stock->getQuantityShowroom() && $stock->getShopId()) {
                    foreach ($map->points as $point) {
                        if ($point->id == $stock->getShopId()) {
                            $point->productInShowroom = true;
                            break;
                        }
                    }
                }
            }

            // TODO нужен рефакторинг (отдавать через View)
            if (!\App::config()->lite['enabled']) {
                $result = [
                    'html'      => \App::templating()->render('order-v3/common/_map',
                        [
                            'dataPoints' => $map,
                            'visible' => true,
                            'class'   => 'jsDeliveryMapPoints',
                            'productUi' => $productUi,
                            'page'      => 'product'
                        ])
                ];
            } else {
                $map->uniqueCosts = $map->getUniquePointCosts();
                $map->uniqueDays = $map->getUniquePointDays();
                $map->uniqueTokens = $map->getUniquePointTokens();
                $result['result'] = $map;
            }

            $result['success'] = true;

        } else {
            $result['error'] = 'Ошибка разбиения';
        }

        return new \Http\JsonResponse($result);

    }
}