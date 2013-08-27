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

        return new \Http\JsonResponse($this->getResponseData());
    }

    /**
     * @return array
     */
    public function getResponseData() {
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

            // купоны
            $coupons = $cart->getCoupons();
            $couponData = (\App::config()->coupon['enabled'] && ($coupon = reset($coupons)))
                ? [
                    ['number' => $coupon->getNumber()],
                ]
                : [];

            // черные карты
            $blackcards = $cart->getBlackcards();
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
                    'product'        => $cart->getProductData(),
                    'service'        => $cart->getServiceData(),
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
                \App::config()->coreV2['timeout'] * 2
            );
            $client->execute();
            if ($exception instanceof \Exception) {
                throw $exception;
            }

            if (\App::config()->blackcard['enabled'] && array_key_exists('blackcard_list', $result)) {
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
                'discounts'       => [],
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

            foreach ($result['products'] as $productItem) {
                $productId = (string)$productItem['id'];

                /** @var $cartProduct \Model\Cart\Product\Entity|null */
                $cartProduct = $cart->getProductById($productId);
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
                    'setUrl'     => $router->generate('cart.product.set', ['productId' => $productId, 'quantity' => $productItem['quantity']]),
                    'deleteUrl'  => $router->generate('cart.product.delete', ['productId' => $productId]),
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

            // купоны
            foreach ($cart->getCoupons() as $coupon) {
                $responseData['discounts'][] = [
                    'type'      => 'coupon',
                    'name'      => $coupon->getName(),
                    'sum'       => $coupon->getDiscountSum(),
                    'error'     => $coupon->getError() ? ['code' => $coupon->getError()->getCode(), 'message' => \Model\Cart\Coupon\Entity::getErrorMessage($coupon->getError()->getCode()) ?: 'Неудалось активировать купон'] : null,
                    'deleteUrl' => $router->generate('cart.coupon.delete'),
                ];
            }

            // черные карты
            foreach ($cart->getBlackcards() as $blackcard) {
                $responseData['discounts'][] = [
                    'type'      => 'blackcard',
                    'name'      => $blackcard->getName(),
                    'sum'       => $blackcard->getDiscountSum(),
                    'error'     => $blackcard->getError() ? ['code' => $blackcard->getError()->getCode(), 'message' => \Model\Cart\Blackcard\Entity::getErrorMessage($blackcard->getError()->getCode()) ?: 'Неудалось активировать карту'] : null,
                    'deleteUrl' => $router->generate('cart.blackcard.delete'),
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

        return $responseData;
    }
}