<?php

namespace Controller\Delivery;

use Controller\Order\ResponseDataTrait;

class Action {
    use ResponseDataTrait;

    /**
     * @param bool $paypalECS
     * @param bool $lifeGift
     * @param bool $oneClick
     * @return array
     */
    public function getResponseData($paypalECS = false, $lifeGift = false, $oneClick = false) {
        $router = \App::router();
        $client = \App::coreClientV2();
        $user = \App::user();
        $region = $user->getRegion();
        $cart = $user->getCart();
        $helper = new \View\Helper();

        \App::logger()->info(['action' => __METHOD__, 'paypalECS' => $paypalECS], ['order']);

        // данные для JsonResponse
        $responseData = [
            'time'           => strtotime(date('Y-m-d'), 0) * 1000,
            'action'         => [],
            'paypalECS'      => false,
            'lifeGift'       => false,
            'oneClick'       => false,
            'cart'           => [],
            'defPoints'      => [],
            'deliveryStates' => [],
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
                'order/calc-tmp2',
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

            if (array_key_exists('action_list', $result) && !empty($result['action_list'])) {
                $cart->setActionData((array)$result['action_list']);
            } else {
                $cart->clearActionData();
            }

            // Типы доставок
            if (isset($result['delivery_types'])) {
                foreach ($result['delivery_types'] as $token => $type) {
                    $typeDataFromRepository = \RepositoryManager::deliveryType()->getEntityByToken($token);
                    if (!$typeDataFromRepository) continue;

                    $responseData['deliveryTypes'][$token] =  [
                        'id'          => $type['id'],
                        'token'       => $token,
                        'name'        => $typeDataFromRepository->getName(),
                        'shortName'   => $typeDataFromRepository->getShortName(),
                        'buttonName'  => $typeDataFromRepository->getButtonName(),
                        'description' => $typeDataFromRepository->getDescription(),
                        'states'      => isset($result['delivery_types'][$token]['methods']) ? (array)$result['delivery_types'][$token]['methods'] : [],
                    ];

                    if ('self_partner_pickpoint' === $typeDataFromRepository->getToken()) {
                        $responseData['deliveryTypes'][$token]['description'] = \App::closureTemplating()->render('order/newForm/__deliveryType-pickpoint-description');
                    }
                }
            }

            // Delivery Methods
            if (isset($result['delivery_methods'])) {
                foreach ($result['delivery_methods'] as $item) {
                    $token = $item['token'];

                    // TODO перенести в модель
                    switch (true) {
                        case ($token == 'standart'):
                        case ($token == 'standart_pred_supplier'):
                            $item['name'] = 'Доставим';
                            break;
                        case ($token == 'self'):
                        case ($token == 'now'):
                            $item['name'] = 'Самовывоз';
                            break;
                        case ($token == 'self_partner_pickpoint'):
                            $item['name'] = 'PickPoint';
                            break;
                        case ($token == 'self_svyaznoy'):
                            $item['name'] = "Самовывоз (ЗАО «Связной-Логистика»)";
                            break;
                        case ($token == 'standart_svyaznoy'):
                            $item['name'] = "Доставим (ЗАО «Связной-Логистика»)";
                            break;
                        case ($token == 'standart_bu'):
                            $item['name'] = "Доставим (ООО «Ювелирный торговый дом»)";
                            break;
                        case ($token == 'standart_fortochki'):
                            $item['name'] = "Доставим (ООО «Пауэр Интернэшнл-шины»)";
                            break;
                        default: $item['name'] = "";
                    }

                    $responseData['deliveryStates'][$token] = $item;
                }
            }

            // Points By Delivery
            $pointTokens = array_map(
                function($state){return isset($state['point_token']) ? $state['point_token'] : null;},
                $responseData['deliveryStates']
            );
            $responseData['pointsByDelivery'] = [
                'self_pred_supplier'      => [
                    'token' => isset($pointTokens['self_pred_supplier']) ? $pointTokens['self_pred_supplier'] : null,
                    'changeName' => 'Сменить магазин'
                ],
                'standart_pred_supplier'  => [
                    'token' => isset($pointTokens['standart_pred_supplier']) ? $pointTokens['standart_pred_supplier'] : null,
                    'changeName' => 'Сменить магазин'
                ],
                'self'      => ['token' => 'shops', 'changeName' => 'Сменить магазин'],
                'self_svyaznoy'      => ['token' => 'shops_svyaznoy', 'changeName' => 'Сменить магазин'],
                'now'       => ['token' => 'shops', 'changeName' => 'Сменить магазин'],
                'self_partner_pickpoint' => ['token' => 'self_partner_pickpoint', 'changeName' => 'Сменить постамат', 'point_token' => 'self_partner_pickpoint'],
            ];

            // если недоступен заказ товара из магазина
            if (!\App::config()->product['allowBuyOnlyInshop'] && isset($responseData['deliveryStates']['now'])) {
                unset($responseData['deliveryStates']['now']);
            }

            // ид товаров для каждого магазина
            $productIdsByShop = [];
            // ид товаров для каждого пикпоинта
            $pickpointProductIds = [];

            // Products
            foreach ($result['products'] as $productItem) {
                $productId = (string)$productItem['id'];

                /** @var $cartProduct \Model\Cart\Product\Entity|null */
                $cartProduct = ($paypalECS || $lifeGift || $oneClick) ? $cartProducts[$productId] : $cart->getProductById($productId);
                if (!$cartProduct) {
                    \App::logger()->error(sprintf('Товар %s не найден в корзине', $productId));
                    continue;
                }

                foreach ($productItem['delivery_methods'] as $deliveryMethod) {
                    $points = [];
                    $responseData['defPoints'][$deliveryMethod['token']] = null;
                    foreach ($deliveryMethod['points'] as $point) {
                        if ($point['id']) {

                            if ( null === $responseData['defPoints'][$deliveryMethod['token']]) {
                                $responseData['defPoints'][$deliveryMethod['token']] = $point['id'];
                            }

                            $dates = [];
                            foreach ($point['dates'] as $dateItem) {
                                $time = strtotime($dateItem['date']);

                                if (!isset($dateItem['intervals'][0])) {
                                    $dateItem['intervals'] = [];
                                }

                                $interval = null;
                                foreach ($dateItem['intervals'] as &$interval) {
                                    unset($interval['id']); // TODO: убрать на ядре
                                }
                                unset($interval);

                                $dates[] = [
                                    'name'      => str_replace(' ' . date('Y', $time) . ' г.', '', $helper->dateToRu(date('Y-m-d', $time)) . ' г.'),
                                    'value'     => strtotime($dateItem['date'], 0) * 1000,
                                    'day'       => (int)date('j', $time),
                                    'dayOfWeek' => (int)date('w', $time),
                                    'intervals' => $dateItem['intervals'],
                                ];
                            }
                            $point['dates'] = $dates;

                            $points[$point['id']] = $point;

                            // если самовывоз, то добавляем ид товара в соответствующий магазин
                            if (in_array($deliveryMethod['token'], ['self', 'now', 'self_svyaznoy', 'self_pred_supplier'])) {
                                if (!isset($productIdsByShop[$point['id']])) {
                                    $productIdsByShop[$point['id']] = [];
                                }
                                if (!isset($productIdsByShop[$point['id']][$deliveryMethod['token']])) {
                                    $productIdsByShop[$point['id']][$deliveryMethod['token']] = [];
                                }
                                $productIdsByShop[$point['id']][$deliveryMethod['token']][] = $productId;
                            }
                        } else {
                            $points[] = $point;
                        }

                        // если пикпоинт, то добавляем ид товара в соответствующий пикпоинт
                        if ('self_partner_pickpoint' === $deliveryMethod['token'] && !in_array($productId, $pickpointProductIds)) {
                            $pickpointProductIds[] = $productId;
                        }
                    }
                    $deliveryData[$deliveryMethod['token']] = $points;
                    if (null === $responseData['defPoints'][$deliveryMethod['token']]) {
                        $responseData['defPoints'][$deliveryMethod['token']] = 0;
                    }
                }

                if (!(bool)$deliveryData) {
                    $e = new \Curl\Exception('Товар недоступен для продажи', 800);
                    $e->setContent(['product_error_list' => [
                        ['code' => $e->getCode(), 'message' => $e->getMessage(), 'id' => $productId],
                    ]]);

                    throw $e;
                }

                switch (true) {
                    case $oneClick:
                        $setUrl = $router->generate('cart.oneClick.product.change', ['productId' => $productId]);
                        $deleteUrl = $router->generate('cart.oneClick.product.delete', ['productId' => $productId]);
                        break;
                    case $paypalECS:
                        $setUrl = $router->generate('cart.paypal.product.set', ['productId' => $productId]);
                        $deleteUrl = $router->generate('cart.paypal.product.delete', ['productId' => $productId]);
                        break;
                    case $lifeGift:
                        $setUrl = $router->generate('cart.lifeGift.product.set', ['productId' => $productId]);
                        $deleteUrl = $router->generate('cart.lifeGift.product.delete', ['productId' => $productId]);
                        break;
                    default:
                        $setUrl = $router->generate('cart.product.set', ['productId' => $productId]);
                        $deleteUrl = $router->generate('cart.product.delete', ['productId' => $productId]);
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
                    'setUrl'     => $setUrl,
                    'deleteUrl'  => $deleteUrl,
                    'deliveries' => $deliveryData,
                ];
            }

            //if (isset($result['shops_svyaznoy'])) $result['shops'] = array_merge($result['shops'], $result['shops_svyaznoy']);

            // Магазины
            foreach (['shops', 'shops_svyaznoy'] as $shopToken) {

            if (isset($result[$shopToken])) {
                foreach ($result[$shopToken] as $shopItem) {
                    $shopId = (string)$shopItem['id'];
                    if (!isset($productIdsByShop[$shopId])) continue;
                    if (empty($shopItem['coord_lat']) || empty($shopItem['coord_long'])) {
                        \App::logger()->error(['Пустые координаты магазина', 'shop' => $shopItem], ['order']);
                        continue;
                    }

                    // CORE-2090
                    if (isset($shopItem['owner'])) {
                        switch ($shopItem['owner']) {
                            case 'svyaznoy': $mapPoint = '/images/marker-svyaznoy.png'; break;
                            case 'enter':
                            default: $mapPoint = '/images/marker.png'; break;

                        }
                    }

                    $responseData[$shopToken][] = [
                        'id'         => $shopId,
                        'name'       => $shopToken == 'shops_svyaznoy' ? $shopItem['address'] : $shopItem['name'],
                        'address'    => $shopItem['address'],
                        'regtime'    => $shopItem['working_time'],
                        'latitude'   => (float)$shopItem['coord_lat'],
                        'longitude'  => (float)$shopItem['coord_long'],
                        'products'   => isset($productIdsByShop[$shopId]) ? $productIdsByShop[$shopId] : [],
                        'pointImage' => isset($mapPoint) ? $mapPoint : '/images/marker.png',
                        'buttonName' => 'Забрать из этого магазина',
                    ];
                }
                // сортировка магазинов
                if (\App::config()->region['defaultId'] != $region->getId() && $region->getLatitude() && $region->getLongitude() && !empty($responseData[$shopToken])) {
                    usort($responseData[$shopToken], function($a, $b) use (&$region) {
                        if (!$a['latitude'] || !$a['longitude'] || !$b['latitude'] || !$b['longitude']) {
                            return 0;
                        }

                        return \Util\Geo::distance($a['latitude'], $a['longitude'], $region->getLatitude(), $region->getLongitude()) > \Util\Geo::distance($b['latitude'], $b['longitude'], $region->getLatitude(), $region->getLongitude());
                    });
                }
            }

            }

            // Пикпоинты
            $pickpoints = [];
            if ( empty($pickpointProductIds) ) {
                \App::logger()->error('Рассчитанное значение $pickpointProductIds пусто', ['pickpoints']);
            } else {
                $deliveryRegions = [];
                foreach ($result['products'] as $p) {
                    foreach ($p['delivery_methods'] as $deliveryMethod) {
                        if ('self_partner_pickpoint' !== $deliveryMethod['token']) continue;
                        if (!$deliveryMethod['points']) continue;

                        foreach ($deliveryMethod['points'] as $pointItem) {
                            foreach ($pointItem['regions'] as $regionItem) {
                                $deliveryRegions[] = $regionItem['region'];
                            }
                        }
                    }
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

            if (empty($pickpoints)) {
                \App::logger()->error('Список пикпойнтов пуст', ['pickpoints']);
                unset($responseData['deliveryStates']['pickpoint']);
            } else {
                foreach ($pickpoints as $pickpointItem) {
                    $responseData['self_partner_pickpoint'][] = [
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
                        'buttonName'    => \RepositoryManager::deliveryType()->getEntityByToken('self_partner_pickpoint')->getButtonName(),
                    ];
                }

                // сортировка пикпоинтов
                if (14974 != $region->getId() && $region->getLatitude() && $region->getLongitude() && !empty($responseData['pickpoints'])) {
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
                if (!(bool)array_intersect($deliveryTypeItem['states'], array_keys($responseData['deliveryStates']))) {
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
                        if ($keyDi !== 'self_partner_pickpoint') continue;
                        $dateData = reset($responseData['products'][$keyPi]['deliveries'][$keyDi]);
                        $responseData['products'][$keyPi]['deliveries'][$keyDi] = [];
                        foreach ($pickpoints as $keyPp => $pickpoint) {
                            $responseData['products'][$keyPi]['deliveries'][$keyDi][$pickpoint['Id']] = $dateData;
                        }
                    }
                }
            }

            if ($oneClick && \App::request()->get('shopId')) $responseData = $this->filterForReserve($responseData, \App::request()->get('shopId'));

        } catch(\Exception $e) {
            $this->failResponseData($e, $responseData);
        }

        return $responseData;
    }

    /**
     * Функция, фильтрующая результат для кнопки "Резерв" ( SITE-3950 )
     * Оставляет только deliveryType['now'], ставит магазин с id == $shopId первым в списке
     *
     * @param $data mixed
     * @param $shopId string
     * @return mixed
     */
    private function filterForReserve($data, $shopId) {
        $result = $data;

        /* Unset deliveryTypes */
        $result['deliveryTypes'] = [];
        $arrayWithNow = array_filter($data['deliveryTypes'], function($type) {
            return $type['token'] == 'now';
        });

        if (!(bool) $arrayWithNow) {
            $product = reset($data['products']);
            $name = $product['name'];
            $quantity = $product['quantity'];
            $data['error']['message'] = "Cегодня невозможно забрать $name в количестве $quantity шт. Вы можете попробовать другой тип доставки.";
            return $data;
        } else {
            $result['deliveryTypes'][] = reset($arrayWithNow);
        }

        /* Sorting shops */
        $firstShop = $lastShops = [];

        array_walk($result['shops'], function ($val) use (&$firstShop, &$lastShops, $shopId){
            if ($val['id'] == $shopId) $firstShop[] = $val;
            else $lastShops[] = $val;
        });

        $result['shops'] = array_merge($firstShop, $lastShops);

        return $result;
    }

}
