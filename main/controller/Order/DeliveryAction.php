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
        //\App::logger()->debug('Exec ' . __METHOD__);

        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http request');
        }

        $paypalECS = 1 === (int)$request->get('paypalECS');
        $lifeGift = 1 === (int)$request->get('lifeGift');
        $oneClick = 1 === (int)$request->get('oneClick');

        return new \Http\JsonResponse($this->getResponseData($paypalECS, $lifeGift, $oneClick));
    }

    /**
     * @param bool $paypalECS
     * @param bool $lifeGift
     * @param bool $oneClick
     * @return array
     */
    public function getResponseData($paypalECS = false, $lifeGift = false, $oneClick = false) {
        if (\App::config()->newDeliveryCalc) {
            return (new \Controller\Delivery\Action())->getResponseData($paypalECS, $lifeGift, $oneClick);
        }

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
            'lifeGift'  => false,
            'oneClick'  => false,
            'cart'      => [],
        ];

        try {

            if (true === $oneClick) {
                $responseData['cart']['sum'] = \App::user()->getOneClickCart()->getSum();

                $responseData['oneClick'] = true;

                $cartProducts = \App::user()->getOneClickCart()->getProducts();
            }  else if (true === $lifeGift) {
                $region = new \Model\Region\Entity(['id' => \App::config()->lifeGift['regionId']]); // TODO: осторожно, говонокодистое место

                $responseData['cart']['sum'] = \App::user()->getLifeGiftCart()->getSum();

                $responseData['lifeGift'] = true;

                $cartProducts = \App::user()->getLifeGiftCart()->getProducts();
            } else {
                $cartProducts = $cart->getProducts();
            }

            // проверка на пустую корзину
            if (!(bool)$cartProducts) {
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
                    'product'        => array_map(function(\Model\Cart\Product\Entity $cartProduct) {
                        return [
                            'id'       => $cartProduct->getId(),
                            'quantity' => $cartProduct->getQuantity(),
                        ];
                    }, $cartProducts),
                    'service'        => [],
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

            // типы доставок
            $deliveryTypeData = [];
            foreach (\RepositoryManager::deliveryType()->getCollection() as $deliveryType) {
                $deliveryTypeData[$deliveryType->getToken()] =  [
                    'id'          => $deliveryType->getId(),
                    'token'       => $deliveryType->getToken(),
                    'name'        => $deliveryType->getName(),
                    'shortName'   => $deliveryType->getShortName(),
                    'buttonName'  => $deliveryType->getButtonName(),
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
                        'name'     => 'PickPoint',
                        'unique'     => true,
                        'products' => [],
                    ],
                ],
                'pointsByDelivery'=> [
                    'self'      => ['token' => 'shops', 'changeName' => 'Сменить магазин'],
                    'now'       => ['token' => 'shops', 'changeName' => 'Сменить магазин'],
                    'pickpoint' => ['token' => 'pickpoints', 'changeName' => 'Сменить постамат'],
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
                $cartProduct = ($paypalECS || $lifeGift || $oneClick) ? reset($cartProducts) : $cart->getProductById($productId);
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
                        $productIdsByShop[$pointId][$deliveryItemTokenPrefix][] = $productId;
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

                if ($oneClick) {
                    $setUrl = $router->generate('cart.oneClick.product.set', ['productId' => $productId]);
                    $deleteUrl = $router->generate('cart.oneClick.product.delete', ['productId' => $productId]);
                } else if ($paypalECS) {
                    $setUrl = $router->generate('cart.paypal.product.set', ['productId' => $productId]);
                    $deleteUrl = $router->generate('cart.paypal.product.delete', ['productId' => $productId]);
                } else if ($lifeGift) {
                    $setUrl = $router->generate('cart.lifeGift.product.set', ['productId' => $productId]);
                    $deleteUrl = $router->generate('cart.lifeGift.product.delete', ['productId' => $productId]);
                } else {
                    $setUrl = $router->generate('cart.product.set', ['productId' => $productId]);
                    $deleteUrl = $router->generate('cart.product.delete', ['productId' => $productId]);
                }

                $responseData['products'][$productId] = [
                    'id'            => $productId,
                    'name'          => $productItem['name'],
                    'price'         => (int)$productItem['price'],
                    'sum'           => $cartProduct->getSum(),
                    'quantity'      => (int)$productItem['quantity'],
                    'stock'         => (int)$productItem['stock'],
                    'image'         => $productItem['media_image'],
                    'url'           => $productItem['link'],
                    'setUrl'        => $setUrl,
                    'deleteUrl'     => $deleteUrl,
                    'deliveries'    => $deliveryData,
                    'isPrepayment'  => false, // TODO: после доделок на ядре (добавятся данные product.label_id) нужно реализовать условие, есть ли у товара шильдик "предоплата" (SITE-3153)
                ];
            }

            // магазины
            foreach ($result['shops'] as $shopItem) {
                $shopId = (string)$shopItem['id'];
                if (!isset($productIdsByShop[$shopId])) continue;
                if (empty($shopItem['coord_lat']) || empty($shopItem['coord_long'])) {
                    \App::logger()->error(['Пустые координаты магазина', 'shop' => $shopItem], ['order']);
                    continue;
                }

                $responseData['shops'][] = [
                    'id'         => $shopId,
                    'name'       => $shopItem['name'],
                    'address'    => $shopItem['address'],
                    'regtime'    => $shopItem['working_time'],
                    'latitude'   => (float)$shopItem['coord_lat'],
                    'longitude'  => (float)$shopItem['coord_long'],
                    'products'   => isset($productIdsByShop[$shopId]) ? $productIdsByShop[$shopId] : [],
                    'pointImage' => '/images/marker.png',
                    'buttonName' => isset($deliveryTypeData['now']['buttonName']) ? $deliveryTypeData['now']['buttonName'] :
                            (isset($deliveryTypeData['standart']['buttonName']) ? $deliveryTypeData['standart']['buttonName'] : ''),
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
                                    (int)$pickpointItem['Status'] < 3 &&
                                    in_array( $pickpointItem['CitiName'], $deliveryRegions )
                                    //&& $pickpointItem['TypeTitle'] != 'ПВЗ' // В списке выбора показывать только точки pickpoint (АПТ), не показывать ПВЗ.
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
                        'pointImage'    => '/images/marker-pickpoint.png',
                        'buttonName'    => $deliveryTypeData['pickpoint']['buttonName'],
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

            if (!empty($responseData['products'])) {
                foreach ($responseData['products'] as $keyPi => $productItem) {
                    if (empty($productItem['deliveries'])) continue;
                    foreach ($productItem['deliveries'] as $keyDi => $deliveryItem) {
                        if ($keyDi == 'pickpoint') {
                            $dateData = reset($responseData['products'][$keyPi]['deliveries'][$keyDi]);
                            $responseData['products'][$keyPi]['deliveries'][$keyDi] = [];
                            foreach ($pickpoints as $keyPp => $pickpoint) {
                                $responseData['products'][$keyPi]['deliveries'][$keyDi][$pickpoint['Id']] = $dateData;
                            }
                        }
                    }
                }
            }
        } catch(\Exception $e) {
            $this->failResponseData($e, $responseData);
        }

        return $responseData;
    }
}