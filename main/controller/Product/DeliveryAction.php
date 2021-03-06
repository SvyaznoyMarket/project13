<?php

namespace Controller\Product;
use \Model\Product\Entity as Product;
use Model\Product\Delivery\ProductDelivery;

class DeliveryAction {
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

        if (!\App::config()->product['deliveryCalc']) {
            return [
                'success' => false,
            ];
        }

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
                    }
                );

                \App::coreClientV2()->execute(null, 1);
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

            if ($productModel instanceof Product) {
                $productModel->delivery = new ProductDelivery($result, $productModel->getId());
            }

            foreach ($result['product_list'] as $item) {
                if (!in_array($item['id'], $productIds)) continue;

                $iProduct = [
                    'id'    => $item['id'],
                    //'token' => $item['token'],
                    'delivery' => [],
                ];

                if (isset($item['delivery_mode_list'])) foreach ($item['delivery_mode_list'] as $deliveryItem) {
                    if (!isset($deliveryItem['date_list']) || !is_array($deliveryItem['date_list'])) continue;

                    if (isset($item['prepay_rules']) && is_array($item['prepay_rules']) && $productModel) {
                        try {
                            $this->setPrepaidLabel($productModel, $item);
                        } catch (\Exception $e) {
                            \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' .  __LINE__], ['cart.split']);
                        }
                    }

                    try {
                        if (empty($deliveryItem['date_interval']) && \App::abTest()->isOrderWithDeliveryInterval() && ($date = key($deliveryItem['date_list']))) {
                            $date = new \DateTime($date);
                            $deliveryItem['date_interval'] = [
                                'from' => ($date->diff((new \DateTime())->setTime(0, 0, 0))->days > 1) ? $date->modify('-1 day')->format('Y-m-d') : $date->format('Y-m-d'),
                                'to'   => $date->modify('+2 day')->format('Y-m-d'),
                            ];
                        }
                    } catch (\Exception $e) {
                        \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' .  __LINE__], ['cart.split']);
                    }

                    $delivery = [
                        'id'    => $deliveryItem['id'],
                        'token' => $deliveryItem['token'],
                        'price' => (new \Helper\TemplateHelper())->formatPrice($deliveryItem['price']),
                        'shop'  => [],
                    ];

                    $firstDate = reset($deliveryItem['date_list']);
                    $firstDate = isset($firstDate['date']) ? new \DateTime($firstDate['date']) : null;
                    $delivery['date'] =
                        $firstDate
                        ? ['value' => $firstDate->format('d.m.Y'), 'name' => $helper->humanizeDate($firstDate)]
                        : []
                    ;
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
                                        $delivery['shop'][$key]['url'] = \App::router()->generateUrl('shop.show', ['pointToken' => $shop->getToken()]);
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

    public function map($productUi) {

        $result = [
            'success' => false
        ];

        /** @var \Model\Product\Entity[] $products */
        $products = [new \Model\Product\Entity(['ui' => $productUi])];
        \RepositoryManager::product()->prepareProductQueries($products);

        $splitResult = null;
        \App::coreClientV2()->addQuery('cart/split',
            [
                'geo_id'     => \App::user()->getRegionId(),
                'request_id' => \App::$id,
            ],
            [ 'cart' => [
                    'product_list' => [
                        [
                            'ui' => $productUi,
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

        if (!$products) {
            return new \Http\JsonResponse(['error' => 'Товар не найден']);
        }

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
                    'html'      => \App::templating()->render(
                        'order-v3/common/_map',
                        [
                            'dataPoints' => $map,
                            'visible' => true,
                            'class'   => 'jsDeliveryMapPoints',
                            'productUi' => $productUi,
                            'page'      => 'product',
                        ]
                    )
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

    /**
     * @param Product $product
     * @param array $deliveryItem
     */
    public function setPrepaidLabel(\Model\Product\Entity $product, array $deliveryItem) {
        $deliveryItem += ['prepay_rules' => []];

        $ruleData =
            isset($deliveryItem['prepay_rules']['priorities']) && is_array($deliveryItem['prepay_rules']['priorities'])
            ? $deliveryItem['prepay_rules']
            : [
                'priorities' => []
            ]
        ;

        foreach ($ruleData['priorities'] as $ruleName => $priority) {
            $ruleName = explode(':', $ruleName);
            $ruleName = reset($ruleName);

            $ruleItem = (array_key_exists($ruleName, $ruleData) && is_array($ruleData[$ruleName])) ? $ruleData[$ruleName] : null;

            if (!$ruleItem) {
                continue;
            }

            switch ($ruleName) {
                case 'deliveries':
                    foreach (array_keys($deliveryItem['delivery_mode_list']) as $deliveryId) {
                        if (!empty($ruleItem[$deliveryId]['prepay_sum'])) {
                            $product->needPrepayment = true;
                            break;
                        }
                    }
                    break;
                case 'labels':
                    if (($label = $product->getLabel()) && !empty($ruleItem[$label->id]['prepay_sum'])) {
                        $product->needPrepayment = true;
                    }
                    break;
                case 'others':
                    if (!empty($ruleItem['cost']['prepay_sum']) && ($ruleItem['cost']['prepay_sum'] > 100000)) {
                        $product->needPrepayment = true;
                    }
                    break;
            }
        }
    }
}