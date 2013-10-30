<?php

namespace Controller\Order;

class DeliveryAction {
    use ResponseDataTrait;

    /*private function d($var){
        file_put_contents('t.txt', print_r($var, 1) . "\n" . PHP_EOL, FILE_APPEND);
    }

    private function pr($var, $hint = null){
        print '<pre>';
        if ($hint) print '### '.$hint."\n".PHP_EOL;
        print_r($var);
        print '</pre>';
    }*/

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

        $paypalECS = 1 === (int)$request->get('paypalECS');

        return new \Http\JsonResponse($this->getResponseData($paypalECS));
    }

    /**
     * @param bool $paypalECS
     * @return array
     */
    public function getResponseData($paypalECS = false) {
        $router = \App::router();
        $client = \App::coreClientV2();
        $user = \App::user();
        $region = $user->getRegion();
        $cart = $user->getCart();
        $helper = new \View\Helper();

        \App::logger()->info(['action' => __METHOD__, 'paypalECS' => $paypalECS], ['order']);

        // данные для JsonResponse
        $responseData = [
            'time'      => strtotime(date('Y-m-d'), 0) * 1000,
            'action'    => [],
            'paypalECS' => false,
            'cart'      => [],
        ];

        try {
            if (true === $paypalECS) {
                $cartProduct = $cart->getPaypalProduct();
                if ($cartProduct) {
                    $responseData['cart']['sum'] = $cartProduct->getSum() + $cartProduct->getDeliverySum();
                }

                $responseData['paypalECS'] = true;

                $cartProducts = $cartProduct ? [$cartProduct] : [];
                $coupons = [];
                $blackcards = [];
            } else {
                $cartProducts = $cart->getProducts();
                $coupons = $cart->getCoupons();
                $blackcards = $cart->getBlackcards();
            }

            // проверка на пустую корзину
            if (!(bool)$cartProducts) {
                throw new \Exception('Корзина пустая');
            }

            // купоны
            $couponData = (\App::config()->coupon['enabled'] && ($coupon = reset($coupons)))
                ? [
                    ['number' => $coupon->getNumber()],
                ]
                : [];

            // черные карты
            $blackcardData = (\App::config()->blackcard['enabled'] && ($blackcard = reset($blackcards)))
                ? [
                    ['number' => $blackcard->getNumber()],
                ]
                : [];

            $result = null;
            $exception = null;
            $client->addQuery(
                'order/calc-tmp',
                [
                    'geo_id'  => $region->getId(),
                ],
                [
                    'product'        => array_map(function(\Model\Cart\Product\Entity $cartProduct) {
                        return [
                            'id'       => $cartProduct->getId(),
                            'quantity' => $cartProduct->getQuantity(),
                        ];
                    }, $cartProducts),
                    'service'        => [],
                    'coupon_list'    => $couponData,
                    'blackcard_list' => $blackcardData,
                ],
                function($data) use (&$result, &$shops) {
                    $result = $data;
                    \App::logger()->info(['action' => __METHOD__, 'core.response' => $result], ['order']);
                },
                function (\Exception $e) use (&$exception) {
                    $exception = $e;
                },
                \App::config()->coreV2['timeout'] * 4
            );
            $client->execute();
            if ($exception instanceof \Exception) {
                throw $exception;
            }

            if (!$paypalECS && \App::config()->blackcard['enabled'] && array_key_exists('blackcard_list', $result)) {
                foreach ($result['blackcard_list'] as $blackcardItem) {
                    $blackcardItem = array_merge([
                        'number'       => null,
                        'name'         => 'Карта',
                        'discount_sum' => 0,
                    ], (array)$blackcardItem);

                    $blackcard = new \Model\Cart\Blackcard\Entity($blackcardItem);
                    $cart->clearBlackcards();
                    $cart->setBlackcard($blackcard);
                }

                if (array_key_exists('action_list', $result)) {
                    $cart->setActionData((array)$result['action_list']);
                }
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

                if ('pickpoint' === $deliveryType->getToken()) {
                    $deliveryTypeData[$deliveryType->getToken()]['description'] = \App::closureTemplating()->render('order/newForm/__deliveryType-pickpoint-description');
                }
            }

            $responseData = array_merge($responseData, [
                'deliveryTypes'   => $deliveryTypeData,
                'deliveryStates'  => [
                    'self'               => [
                        'name'     => 'Самовывоз',
                        'unique'     => false,
                        'products' => [],
                    ],
                    'now'                => [
                        'name'     => 'Самовывоз',
                        'unique'     => false,
                        'products' => [],
                    ],
                    'standart_other'     => [
                        'name'     => 'Доставим',
                        'unique'     => false,
                        'products' => [],
                    ],
                    'standart_furniture' => [
                        'name'     => 'Доставим',
                        'unique'     => false,
                        'products' => [],
                    ],
                    'pickpoint' => [
                        'name'     => 'Pickpoint',
                        'unique'     => true,
                        'products' => [],
                    ],
                ],
                'pointsByDelivery'=> [
                    'self' => 'shops',
                    'now'  => 'shops',
                    'pickpoint' => 'pickpoints',
                ],
                'products'        => [],
                'shops'           => [],
                'pickpoints'      => [],
                'discounts'       => [],
            ]);

            // если недоступен заказ товара из магазина
            if (!\App::config()->product['allowBuyOnlyInshop'] && isset($responseData['deliveryStates']['now'])) {
                unset($responseData['deliveryStates']['now']);
            }

            // костыль
            $getDates = function(array $dateData) use (&$helper) {
                $return = [];

                foreach ($dateData as $dateItem) {
                    $time = strtotime($dateItem['date']);

                    $intervalData = [];
                    if (isset($dateItem['interval'])) {
                        foreach ((array)$dateItem['interval'] as $intervalItem) {
                            $intervalData[] = [
                                'start' => $intervalItem['time_begin'],
                                'end'   => $intervalItem['time_end'],
                            ];
                        }
                    }

                    $return[] = [
                        'name'      => str_replace(' ' . date('Y') . ' г.', '', $helper->dateToRu(date('Y-m-d', $time)) . ' г.'),
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
            // ид товаров для каждого пикпоинта
            $pickpointProductIds = [];

            foreach ($result['products'] as $productItem) {
                $productId = (string)$productItem['id'];

                /** @var $cartProduct \Model\Cart\Product\Entity|null */
                $cartProduct = $paypalECS ? reset($cartProducts) : $cart->getProductById($productId);
                if (!$cartProduct) {
                    \App::logger()->error(sprintf('Товар %s не найден в корзине', $productId));
                    continue;
                }

                $deliveryData = [];
                foreach ($productItem['deliveries'] as $deliveryItemToken => $deliveryItem) {

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

                    // если пикпоинт, то добавляем ид товара в соответствующий пикпоинт
                    if (in_array($deliveryItemTokenPrefix, ['pickpoint'])) {
                        $pickpointProductIds[] = $productId;
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

                if (!(bool)$deliveryData) {
                    $e = new \Curl\Exception('Товар недоступен для продажи', 800);
                    $e->setContent(['product_error_list' => [
                        ['code' => $e->getCode(), 'message' => $e->getMessage(), 'id' => $productId],
                    ]]);

                    throw $e;
                }

                $responseData['products'][$productId] = [
                    'id'         => $productId,
                    'name'       => $productItem['name'],
                    'price'      => (int)$productItem['price'],
                    'sum'        => $cartProduct->getSum(),
                    'quantity'   => (int)$productItem['quantity'],
                    'stock'      => (int)$productItem['stock'],
                    'image'      => $productItem['media_image'],
                    'url'        => $productItem['link'],
                    'setUrl'     =>
                        $paypalECS
                        ? $router->generate('cart.paypal.product.set', ['productId' => $productId, 'quantity' => $productItem['quantity']])
                        : $router->generate('cart.product.set', ['productId' => $productId, 'quantity' => $productItem['quantity']])
                    ,
                    'deleteUrl'  =>
                        $paypalECS
                        ? $router->generate('cart.paypal.product.delete', ['productId' => $productId])
                        : $router->generate('cart.product.delete', ['productId' => $productId])
                    ,
                    'deliveries' => $deliveryData,
                ];
            }

            // магазины
            foreach ($result['shops'] as $shopItem) {
                $shopId = (string)$shopItem['id'];
                if (!isset($productIdsByShop[$shopId])) continue;

                $responseData['shops'][] = [
                    'id'         => $shopId,
                    'name'       => $shopItem['name'],
                    'address'    => $shopItem['address'],
                    'regtime'     => $shopItem['working_time'],
                    'latitude'   => (float)$shopItem['coord_lat'],
                    'longitude'  => (float)$shopItem['coord_long'],
                    'products'   => isset($productIdsByShop[$shopId]) ? $productIdsByShop[$shopId] : [],
                ];
            }
            // сортировка магазинов
            if (14974 != $region->getId() && $region->getLatitude() && $region->getLongitude()) {
                usort($responseData['shops'], function($a, $b) use (&$region) {
                    if (!$a['latitude'] || !$a['longitude'] || !$b['latitude'] || !$b['longitude']) {
                        return 0;
                    }

                    return \Util\Geo::distance($a['latitude'], $a['longitude'], $region->getLatitude(), $region->getLongitude()) > \Util\Geo::distance($b['latitude'], $b['longitude'], $region->getLatitude(), $region->getLongitude());
                });
            }

            $pickpoints = [];

            if ( empty($pickpointProductIds) ) {
                \App::logger()->error('Рассчитанное значение $pickpointProductIds пусто', ['pickpoints']);
            } else {
                $deliveryRegions = [];
                if(!empty(reset($result['products'])['deliveries']['pickpoint']['regions'])) {
                    $deliveryRegions = array_map(
                        function($regionItem) {
                            return $regionItem['region'];
                        },
                        reset($result['products'])['deliveries']['pickpoint']['regions']
                    );
                }
                if ( empty($deliveryRegions) ) {
                    \App::logger()->error('Рассчитанное значение $deliveryRegions пусто', ['pickpoints']);
                }

                $ppClient = \App::pickpointClient();
                $ppClient->addQuery('postamatlist', [], [],
                    function($data) use (&$pickpoints, &$deliveryRegions) {
                        if ( !is_array($data) ) {
                            \App::logger()->error('Неожиданный ответ сервера на запрос postamatlist', ['pickpoints']);
                            return false;
                        }
                        $pickpoints = array_filter($data,
                            function($pickpointItem) use (&$deliveryRegions) {
                                return
                                    // Статус постамата: 1 – новый, 2 – рабочий, 3 - закрытый
                                    in_array( (int)$pickpointItem['Status'], [1,2] ) &&
                                    in_array( $pickpointItem['CitiName'], $deliveryRegions ) &&
                                    // В списке выбора показывать только точки pickpoint (АПТ), не показывать ПВЗ.
                                    $pickpointItem['TypeTitle'] != 'ПВЗ'
                                    ;
                            }
                        );
                        if ( empty($pickpoints) ) {
                            \App::logger()->error('Нет пикпойнтов в отфильтрованных данных', ['pickpoints']);
                        }
                    },
                    function (\Exception $e) use (&$exception) {
                        $exception = $e;
                    },
                    \App::config()->pickpoint['timeout']
                );
                $ppClient->execute();
            }

            // пикпоинты
            if ( empty($pickpoints) ) {
                \App::logger()->error('Список пикпойнтов пуст', ['pickpoints']);
                unset($responseData['deliveryStates']['pickpoint']);
            } else {
                foreach ($pickpoints as $pickpointItem) {
                    $responseData['pickpoints'][] = [
                        'id'            => (string)$pickpointItem['Id'],
                        'number'        => (string)$pickpointItem['Number'], //  Передавать корректный id постамата, использовать не id точки, а номер постамата
                        'name'          => $pickpointItem['Name'] . '; ' . $pickpointItem['Address'],
                        //'address'       => $pickpointItem['Address'],
                        'street'       => $pickpointItem['Street'],
                        'house'         => $pickpointItem['House'],
                        'regtime'       => $ppClient->worksTimePrepare($pickpointItem['WorkTime']),
                        'latitude'      => (float)$pickpointItem['Latitude'],
                        'longitude'     => (float)$pickpointItem['Longitude'],
                        'products'      => $pickpointProductIds,
                        'point_name'    => $pickpointItem['Name'],
                    ];
                }

                // сортировка пикпоинтов
                if (14974 != $region->getId() && $region->getLatitude() && $region->getLongitude()) {
                    usort($responseData['pickpoints'], function($a, $b) use (&$region) {
                        if (!$a['latitude'] || !$a['longitude'] || !$b['latitude'] || !$b['longitude']) {
                            return 0;
                        }

                        return \Util\Geo::distance($a['latitude'], $a['longitude'], $region->getLatitude(), $region->getLongitude()) > \Util\Geo::distance($b['latitude'], $b['longitude'], $region->getLatitude(), $region->getLongitude());
                    });
                }
            }

            // купоны
            if (!$paypalECS) {
                foreach ($cart->getCoupons() as $coupon) {
                    $responseData['discounts'][] = [
                        'type'      => 'coupon',
                        'name'      => $coupon->getName(),
                        'sum'       => $coupon->getDiscountSum(),
                        'error'     => $coupon->getError() ? ['code' => $coupon->getError()->getCode(), 'message' => \Model\Cart\Coupon\Entity::getErrorMessage($coupon->getError()->getCode()) ?: 'Неудалось активировать купон'] : null,
                        'deleteUrl' => $router->generate('cart.coupon.delete'),
                    ];
                }
            }

            // черные карты
            if (!$paypalECS) {
                foreach ($cart->getBlackcards() as $blackcard) {
                    $responseData['discounts'][] = [
                        'type'      => 'blackcard',
                        'name'      => $blackcard->getName(),
                        'sum'       => $blackcard->getDiscountSum(),
                        'error'     => $blackcard->getError() ? ['code' => $blackcard->getError()->getCode(), 'message' => \Model\Cart\Blackcard\Entity::getErrorMessage($blackcard->getError()->getCode()) ?: 'Неудалось активировать карту'] : null,
                        'deleteUrl' => $router->generate('cart.blackcard.delete'),
                    ];
                }
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
                throw new \Exception('Не вычислено ни одного типа доставки');
            }

            $responseData['deliveryTypes'] = array_values($responseData['deliveryTypes']);
            $responseData['success'] = true;
        } catch(\Exception $e) {
            $this->failResponseData($e, $responseData);
        }

        if(!empty($responseData['products'])) {
            foreach ($responseData['products'] as $keyPi => $productItem) {
                foreach ($productItem['deliveries'] as $keyDi => $deliveryItem) {
                    if($keyDi == 'pickpoint') {
                        $dateData = reset($responseData['products'][$keyPi]['deliveries'][$keyDi]);
                        $responseData['products'][$keyPi]['deliveries'][$keyDi] = [];
                        foreach ($pickpoints as $keyPp => $pickpoint) {
                            $responseData['products'][$keyPi]['deliveries'][$keyDi][$pickpoint['Id']] = $dateData;
                        }
                    }
                }
            }
        }

        return $responseData;
    }
}