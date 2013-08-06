<?php

namespace Controller\Order;

class DeliveryAction {
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

        try {
            // проверка на пустую корзину
            if ($cart->isEmpty()) {
                throw new \Exception('Корзина пустая');
            }

            $result = null;
            $calcException = null;
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
                },
                function (\Exception $e) use (&$calcException) {
                    $calcException = $e;
                },
                \App::config()->coreV2['timeout'] * 2
            );
            $client->execute();
            if ($calcException instanceof \Curl\Exception) {
                // TODO
                throw $calcException;
            }

            $responseData = [
                'time'            => time(),
                'deliveryTypes'   => [
                    'standart' => [
                        'token'  => 'standart',
                        'name'   => 'Доставка',
                        'states' => ['self', 'now', 'standart_furniture', 'standart_other'],
                    ],
                    'self'     => [
                        'token'  => 'self',
                        'name'   => 'Самовывоз',
                        'states' => ['standart_furniture', 'standart_other', 'self', 'now'],
                    ],
                ],
                'deliveryStates'  => [],
                'pointsByDelivery'=> [
                    'self' => 'shops',
                    'now'  => 'shops',
                ],
                'products'        => [],
                'shops'           => [],
            ];

            // костыль
            $getDates = function(array $dateData) {
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
                        'name'      => date('Y-m-d', $time),
                        'value'     => strtotime($dateItem['date'], 0) * 1000,
                        'day'       => (int)date('j', $time),
                        'dayOfWeek' => (int)date('w', $time),
                        'intervals' => $intervalData,
                    ];
                }

                return $return;
            };
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

                    // если доставка, ...
                    if ('standart' == $deliveryItemTokenPrefix) {
                        $deliveryItemTokenPrefix = $deliveryItemToken;
                        $pointId = 0;
                    }

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

            foreach ($result['shops'] as $shopItem) {
                $responseData['shops'][] = [
                    'id'         => (string)$shopItem['id'],
                    'name'       => $shopItem['name'],
                    'address'    => $shopItem['address'],
                    'regime'     => $shopItem['working_time'],
                    'latitude'   => (float)$shopItem['coord_lat'],
                    'longitude'  => (float)$shopItem['coord_long'],
                ];
            }

            $responseData['deliveryTypes'] = array_values($responseData['deliveryTypes']);
            $responseData['success'] = true;
        } catch(\Exception $e) {
            $responseData = [
                'success' => false,
                'error'   => ['code' => $e->getCode(), 'message' => $e->getMessage()],
            ];
        }

        return new \Http\JsonResponse($responseData);
    }
}