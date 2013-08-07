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
                },
                function (\Exception $e) use (&$exception) {
                    $exception = $e;
                },
                \App::config()->coreV2['timeout'] * 2
            );
            $client->execute();
            if ($exception instanceof \Curl\Exception) {
                // TODO
                throw $exception;
            }

            $responseData = [
                'time'            => time(),
                'deliveryTypes'   => [
                    'standart' => [
                        'token'       => 'standart',
                        'name'        => 'Доставка заказа курьером',
                        'description' => 'Мы привезем заказ по любому удобному вам адресу. Пожалуйста, укажите дату и время доставки.',
                        'states'      => ['standart_furniture', 'standart_other', 'self', 'now'],
                    ],
                    'self'     => [
                        'token'       => 'self',
                        'name'        => 'Самостоятельно заберу в магазине',
                        'description' => 'Вы можете самостоятельно забрать товар из ближайшего к вам магазина Enter. Услуга бесплатная! Резерв товара сохраняется 3 дня. Пожалуйста, выберите магазин.',
                        'states'      => ['self', 'now', 'standart_furniture', 'standart_other'],
                    ],
                ],
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

            // удаляем модификации доставок, в которых нет товаров
            foreach ($responseData['deliveryStates'] as $i => $deliveryStateItem) {
                if (!(bool)$deliveryStateItem['products']) {
                    unset($responseData['deliveryStates'][$i]);
                }
            }

            $responseData['deliveryTypes'] = array_values($responseData['deliveryTypes']);
            $responseData['success'] = true;
        } catch(\Exception $e) {
            if ($e instanceof \Curl\Exception) {
                $errorData = (array)$e->getContent();
                $errorData = isset($errorData['product_error_list']) ? (array)$errorData['product_error_list'] : [];
                if ((bool)$errorData) {
                    \App::exception()->remove($e);

                    // приукрашиваем сообщение об ошибке
                    switch ($e->getCode()) {
                        case 770:
                            $message = 'Невозможно расчитать доставку';
                            break;
                        default:
                            $message = 'Невозможно расчитать доставку';
                            break;
                    }
                    $e = new \Exception($message, $e->getCode());
                }

                $productDataById = [];
                foreach ($errorData as $errorItem) {
                    switch ($errorItem['code']) {
                        case 708:
                            $errorItem['message'] = !empty($errorItem['quantity_available']) ? sprintf('Доступно только %s шт.', $errorItem['quantity_available']) : $errorItem['message'];
                            break;
                        case 800:
                            $errorItem['message'] = 'Товар недоступен для продажи';
                            break;
                        default:
                            $errorItem['message'] = 'Товар не может быть заказан';
                            break;
                    }

                    $productDataById[$errorItem['id']] = [
                        'id'    => $errorItem['id'],
                        'error' => ['code' => $errorItem['code'], 'message' => $errorItem['message']],
                    ];
                }

                foreach (array_chunk(array_keys($productDataById), 100) as $idsInChunk) {
                    \RepositoryManager::product()->prepareCollectionById($idsInChunk, $region, function($data) use (&$productDataById, &$router, &$cart) {
                        foreach ($data as $item) {
                            $cartProduct = $cart->getProductById($item['id']);
                            if (!$cartProduct) {
                                \App::logger()->error(sprintf('Товар #%s не найден в корзине', $item['id']), ['order']);
                                continue;
                            }
                            $productDataById[$item['id']] = array_merge([
                                'id'         => (string)$item['id'],
                                'name'       => $item['name'],
                                'price'      => (int)$item['price'],
                                'sum'        => $cartProduct->getSum(),
                                'quantity'   => $cartProduct->getQuantity(),
                                'stock'      => (int)$item['stock'],
                                'image'      => $item['media_image'],
                                'url'        => $item['link'],
                                'addUrl'     => $router->generate('cart.product.set', ['productId' => $item['id'], 'quantity' => $cartProduct->getQuantity()]),
                                'deleteUrl'  => $router->generate('cart.product.delete', ['productId' => $item['id']]),
                                'deliveries' => [],
                            ], $productDataById[$item['id']]);
                        }
                    });
                }
                \App::coreClientV2()->execute();

                $responseData['products'] = $productDataById;
            }

            $responseData['success'] = false;
            $responseData['error'] = ['code' => $e->getCode(), 'message' => $e->getMessage()];
        }

        return new \Http\JsonResponse($responseData);
    }
}