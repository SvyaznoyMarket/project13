<?php

namespace Controller\Order;

class DeliveryAction {
    use ResponseDataTrait;

    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse|\Http\RedirectResponse|\Http\Response
     * @throws \Exception\NotFoundException
     * @throws \Exception
     */
    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http request');
        }

        $router = \App::router();
        $client = \App::coreClientV2();
        $user = \App::user();
        $region = $user->getRegion();
        $cart = $user->getCart();
        $helper = new \View\Helper();

        // данные для JsonResponse
        $responseData = [
            'time'   => strtotime(date('Y-m-d'), 0) * 1000,
            'action' => [],
        ];

        try {
            // проверка на пустую корзину
            if ($cart->isEmpty()) {
                throw new \Exception('Корзина пустая');
            }

            $result = null;
            $exception = null;
            $client->addQuery(
                'order/calc-tmp',
                [
                    'geo_id'  => $region->getId(),
                ],
                [
                    'product' => $cart->getProductData(),
                    'service' => $cart->getServiceData(),
                ],
                function($data) use (&$result, &$shops) {
                    $result = $data;
                    \App::logger()->info(['action' => __METHOD__, 'core.response' => $result], ['order']);
                },
                function (\Exception $e) use (&$exception) {
                    $exception = $e;
                },
                \App::config()->coreV2['timeout'] * 2
            );
            $client->execute();
            if ($exception instanceof \Exception) {
                throw $exception;
            }

            // типы доставок
            $deliveryTypeData = [];
            foreach (\RepositoryManager::deliveryType()->getCollection() as $deliveryType) {
                $deliveryTypeData[$deliveryType->getToken()] =  [
                    'id'          => $deliveryType->getId(),
                    'token'       => $deliveryType->getToken(),
                    'name'        => $deliveryType->getName(),
                    'shortName'   => $deliveryType->getShortName(),
                    'description' => $deliveryType->getDescription(),
                    'states'      => $deliveryType->getPossibleMethodTokens(),
                    'ownStates'   => $deliveryType->getMethodTokens(),
                ];
            }

            $responseData = array_merge($responseData, [
                'deliveryTypes'   => $deliveryTypeData,
                'deliveryStates'  => [
                    'self'               => [
                        'name'     => 'Самовывоз',
                        'products' => [],
                    ],
                    'now'                => [
                        'name'     => 'Самовывоз',
                        'products' => [],
                    ],
                    'standart_other'     => [
                        'name'     => 'Доставим',
                        'products' => [],
                    ],
                    'standart_furniture' => [
                        'name'     => 'Доставим',
                        'products' => [],
                    ],
                ],
                'pointsByDelivery'=> [
                    'self' => 'shops',
                    'now'  => 'shops',
                ],
                'products'        => [],
                'shops'           => [],
            ]);

            // костыль
            $getDates = function(array $dateData) use (&$helper) {
                $return = [];

                foreach ($dateData as $dateItem) {
                    $time = strtotime($dateItem['date']);

                    $intervalData = [];
                    foreach ($dateItem['interval'] as $intervalItem) {
                        $intervalData[] = [
                            'start' => $intervalItem['time_begin'],
                            'end'   => $intervalItem['time_end'],
                        ];
                    }

                    $return[] = [
                        'name'      => str_replace(date('Y') . ' г.', '', $helper->dateToRu(date('Y-m-d', $time)) . ' г.'),
                        'value'     => strtotime($dateItem['date'], 0) * 1000,
                        'day'       => (int)date('j', $time),
                        'dayOfWeek' => (int)date('w', $time),
                        'intervals' => $intervalData,
                    ];
                }

                return $return;
            };

            // ид товаров для каждого магазина
            $productIdsByShop = [];

            foreach ($result['products'] as $productItem) {
                /** @var $cartProduct \Model\Cart\Product\Entity|null */
                $cartProduct = $cart->getProductById($productItem['id']);
                if (!$cartProduct) {
                    \App::logger()->error(sprintf('Товар %s не найден в корзине', $productItem['id']));
                    continue;
                }

                $deliveryData = [];
                foreach ($productItem['deliveries'] as $deliveryItemToken => $deliveryItem) {
                    $productId = (string)$productItem['id'];
                    list($deliveryItemTokenPrefix, $pointId) = array_pad(explode('_', $deliveryItemToken), 2, null);

                    // если доставка, модифицируем префикс токена и точку получения товаров
                    if ('standart' == $deliveryItemTokenPrefix) {
                        $deliveryItemTokenPrefix = $deliveryItemToken;
                        $pointId = 0;
                    }

                    // если самовывоз, то добавляем ид товара в соответствующий магазин
                    if (in_array($deliveryItemTokenPrefix, ['self', 'now'])) {
                        if (!isset($productIdsByShop[$pointId])) {
                            $productIdsByShop[$pointId] = [];
                        }
                        $productIdsByShop[$pointId][] = $productId;
                    }

                    // добавляем ид товара в соответствующий метод доставки
                    if (!isset($responseData['deliveryStates'][$deliveryItemTokenPrefix])) {
                        $responseData['deliveryStates'][$deliveryItemTokenPrefix] = [
                            'products' => [],
                        ];
                    }
                    if (!in_array($productId, $responseData['deliveryStates'][$deliveryItemTokenPrefix]['products'])) {
                        $responseData['deliveryStates'][$deliveryItemTokenPrefix]['products'][] = $productId;
                    }

                    if (!isset($deliveryData[$deliveryItemTokenPrefix])) {
                        $deliveryData[$deliveryItemTokenPrefix] = [];
                    }
                    $deliveryData[$deliveryItemTokenPrefix][$pointId] = [
                        'price' => (int)$deliveryItem['price'],
                        'dates' => $getDates($deliveryItem['dates']),
                    ];
                }

                $responseData['products'][$productItem['id']] = [
                    'id'         => $productId,
                    'name'       => $productItem['name'],
                    'price'      => (int)$productItem['price'],
                    'sum'        => $cartProduct->getSum(),
                    'quantity'   => (int)$productItem['quantity'],
                    'stock'      => (int)$productItem['stock'],
                    'image'      => $productItem['media_image'],
                    'url'        => $productItem['link'],
                    'addUrl'     => $router->generate('cart.product.set', ['productId' => $productItem['id'], 'quantity' => $productItem['quantity']]),
                    'deleteUrl'  => $router->generate('cart.product.delete', ['productId' => $productItem['id']]),
                    'deliveries' => $deliveryData,
                ];
            }

            // магазины
            foreach ($result['shops'] as $shopItem) {
                $shopId = (string)$shopItem['id'];
                $responseData['shops'][] = [
                    'id'         => $shopId,
                    'name'       => $shopItem['name'],
                    'address'    => $shopItem['address'],
                    'regime'     => $shopItem['working_time'],
                    'latitude'   => (float)$shopItem['coord_lat'],
                    'longitude'  => (float)$shopItem['coord_long'],
                    'products'   => isset($productIdsByShop[$shopId]) ? $productIdsByShop[$shopId] : [],
                ];
            }

            // удаляем методы доставок, в которых нет товаров
            foreach ($responseData['deliveryStates'] as $i => $deliveryStateItem) {
                if (!(bool)$deliveryStateItem['products']) {
                    unset($responseData['deliveryStates'][$i]);
                }
            }

            // удаляем типы доставок, у которых не осталось методов доставок
            foreach ($responseData['deliveryTypes'] as $i => $deliveryTypeItem) {

                if (!(bool)array_intersect($deliveryTypeItem['ownStates'], array_keys($responseData['deliveryStates']))) {
                    unset($responseData['deliveryTypes'][$i]);
                }
            }
            if (!(bool)$responseData['deliveryTypes']) {
                throw new \Exception('Не вычеслено ни одного типа доставки');
            }

            $responseData['deliveryTypes'] = array_values($responseData['deliveryTypes']);
            $responseData['success'] = true;
        } catch(\Exception $e) {
            $this->failResponseData($e, $responseData);
        }

        return new \Http\JsonResponse($responseData);
    }
}