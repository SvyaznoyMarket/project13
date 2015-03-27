<?php

namespace Controller\Order;

use View\Order\NewForm\Form as Form;

class CreateAction {
    use ResponseDataTrait;
    use FormTrait;

    const TYPE_PAYMENT_CREDIT = 6;

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

        $user = \App::user();
        $cart = $user->getCart();

        // форма
        $form = $this->getForm();
        // данные для тела JsonResponse
        $responseData = [
            'time'      => strtotime(date('Y-m-d'), 0) * 1000,
            'action'    => [],
            'paypalECS' => false,
        ];
        // массив кукисов
        $cookies = [];

        try {

            (new \Controller\OrderV3\OrderV3())->logger(['action' => 'create-old-delivery']);

            // проверка на пустую корзину
            if ($cart->isEmpty()) {
                throw new \Exception('Корзина пустая');
            }
            // проверка на обязательный параметр
            if (!is_array($request->get('order'))) {
                throw new \Exception(sprintf('Запрос не содержит параметра %s %s', 'order', json_encode($request->request->all(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)));
            }

            \App::logger()->info(['request.order' => $request->get('order')], ['order']);

            // обновление формы из параметров запроса
            $form->fromArray($request->get('order'));
            // валидация формы
            $this->validateForm($form);
            if (!$form->isValid()) {
                throw new \Exception('Форма заполнена неверно');
            }

            $cartCoupons = $cart->getCoupons();
            if (\App::config()->coupon['enabled'] && (bool)$cartCoupons) {
                // если заказ разбился более чем на один подзаказ, то ...
                if (count($form->getPart()) > 1) {
                    // очищаем данные купона
                    $responseData['action']['alert'] = [
                        'message' => 'Не удалось применить скидку. Свяжитесь с оператором Контакт-cENTER ' . \App::config()->company['phone'],
                        'cancel'  => false,
                    ];
                } else {
                    // если всё ок, то применяем купон и запоминаем его номер
                    /** @var $couponEntity \Model\Cart\Coupon\Entity **/
                    $couponEntity = reset($cartCoupons);
                    if ($couponEntity && !$couponEntity->getError()) {
                        $form->setCouponNumber($couponEntity->getNumber());
                    }
                }
            }

            // TODO: прибавить к cartSum стоимость доставки
            $cartSum = $user->getCart()->getSum();
            if ($form->getPaymentMethodId() && ($cartSum > \App::config()->order['maxSumOnline']) && in_array($form->getPaymentMethodId(), [\Model\PaymentMethod\Entity::QIWI_ID, \Model\PaymentMethod\Entity::WEBMONEY_ID])) {
                $paymentMethod = \RepositoryManager::paymentMethod()->getEntityById($form->getPaymentMethodId());
                throw new \Exception(sprintf('Невозможно оформить заказ на %d рублей с выбранным способом оплаты %s', $cartSum, $paymentMethod ? $paymentMethod->getName() : ''));
            }

            // создание заказов в ядре
            $createdOrders = $this->saveOrders($form);

            // сохранение заказов в сессии
            // TODO: можно переделать вынести данные клиента: номер телефона и купона (одинаковые для всех заказов) за границы array_map
            \App::session()->set(\App::config()->order['sessionName'] ?: 'lastOrder', array_map(function(\Model\Order\CreatedEntity $createdOrder) use ($form) {
                return [
                    'number' => $createdOrder->getNumber(),
                    'phone' => $form->getMobilePhone(),
                    'coupon_number' => $form->getCouponNumber(),
                ];
            }, $createdOrders));

            /** @var $firstCreatedOrder \Model\Order\CreatedEntity */
            $firstCreatedOrder = reset($createdOrders);
            if ($firstCreatedOrder && $firstCreatedOrder->getPaymentUrl()) {
                // сохранение урла для редиректа в сессии
                \App::session()->set('paymentUrl', $firstCreatedOrder->getPaymentUrl());
            }

            // подписка пользователя
            $this->subscribeUser($form);

            $responseData['success'] = true;
            $responseData['redirect'] = \App::router()->generate('order.complete');

            try {
                // сохранение формы в кукисах
                $this->saveForm($form, $cookies);

                // удаление флага "Купи в кредит"
                $cookies[] = new \Http\Cookie('credit_on', '', time() - 3600);

                // очистка корзины
                $user->getCart()->clear();
            } catch (\Exception $e) {
                \App::logger()->error($e, ['order']);
            }
        } catch(\Exception $e) {
            $responseData['form'] = [
                'error' => $this->updateErrors($e, $form),
            ];

            $this->failResponseData($e, $responseData);

            $this->logErrors($request, $e, $form, __METHOD__);

            // в этом месте важно передать редирект, его ждёт от нас JS
            if (!isset($responseData['redirect'])) {
                $responseData['redirect'] = \App::router()->generate('order');
                //$responseData['redirect'] = \App::router()->generate('cart');
            }
            // можно добавить сохранение в куку введённого адреса и метро,
            // дабы пользователю не вводить их заново при повторном заказе
        }

        // JsonResponse
        $response = new \Http\JsonResponse($responseData);
        foreach ($cookies as $cookie) {
            $response->headers->setCookie($cookie);
        }

        return $response;
    }

    /**
     * @param Form $form
     * @return \Model\Order\CreatedEntity[]
     * @throws \Exception
     */
    private function saveOrders(Form $form) {
        $request = \App::request();
        $user = \App::user();
        $userEntity = $user->getEntity();

        //$user->getCart()->fill();

        if (!$form->isValid()) {
            throw new \Exception('Невалидная форма заказа %s');
        }

        /** @var $deliveryTypesById \Model\Shop\Entity[] */
        $shopsById = [];
        foreach (\RepositoryManager::shop()->getCollectionByRegion($user->getRegion()) as $shop) {
            $shopsById[$shop->getId()] = $shop;
        }

        if (!(bool)$form->getPart()) {
            throw new \Exception(sprintf('Не получены подзаказы для запроса %s', json_encode($request->request->all(), JSON_UNESCAPED_UNICODE)));
        }

        $data = [];
        $bMeta = false;
        foreach ($form->getPart() as $orderPart) {
            /** @var $deliveryType \Model\DeliveryType\Entity|null */
            $deliveryType = \RepositoryManager::deliveryType()->getEntityByMethodToken($orderPart->getDeliveryMethodToken());
            if (!$deliveryType) {
                \App::logger()->error(['action' => __METHOD__, 'message' => sprintf('Неизвестный метод доставки %s', $orderPart->getDeliveryMethodToken())], ['order']);
                continue;
            }

            // общие данные заказа
            $orderData = [
                'type_id'           => \Model\Order\Entity::TYPE_ORDER,
                'geo_id'            => $user->getRegion()->getId(),
                'user_id'           => $userEntity ? $userEntity->getId() : null,
                'is_legal'          => $userEntity ? $userEntity->getIsCorporative() : false,
                'payment_id'        => $form->getPaymentMethodId(),
                'credit_bank_id'    => $form->getCreditBankId(),
                'last_name'         => $form->getLastName(),
                'first_name'        => $form->getFirstName(),
                'email'             => $form->getEmail(),
                'mobile'            => $form->getMobilePhone(),
                'address_street'    => null,
                'address_number'    => null,
                'address_building'  => null,
                'address_apartment' => null,
                'address_floor'     => null,
                'extra'             => $form->getComment(),
                'bonus_card_number' => $form->getBonusCardnumber(),
                'delivery_type_id'  => $deliveryType->getId(),
                'delivery_type_token' => $orderPart->getDeliveryMethodToken(),
                'delivery_price'      => $orderPart->getDeliveryPrice(),
                'delivery_period'   => $orderPart->getInterval() ? [$orderPart->getInterval()->getStartAt(), $orderPart->getInterval()->getEndAt()] : null,
                'delivery_date'     => $orderPart->getDate() instanceof \DateTime ? $orderPart->getDate()->format('Y-m-d') : null,
                'ip'                => $request->getClientIp(),
                'product'           => [],
                'payment_params'    => [
                    'qiwi_phone' => $form->getQiwiPhone(),
                ],
            ];

            // Валидация формы на сервере до запроса к ядру
            if ( self::TYPE_PAYMENT_CREDIT === $orderData['payment_id'] && empty($orderData['credit_bank_id']) ) {
                // ошибка. если не указан ли банк для кредита
                throw new \Exception('Не выбран банк!', 729); // текст ошибки заменится, см main/controller/Order/ResponseDataTrait.php
            }

            // станция метро
            if ($user->getRegion()->getHasSubway()) {
                $orderData['subway_id'] = $form->getSubwayId();
            }

            // адрес
            if (!in_array($deliveryType->getToken(), [\Model\DeliveryType\Entity::TYPE_SELF, \Model\DeliveryType\Entity::TYPE_NOW])) {
                $orderData['address_street'] = $form->getAddressStreet();
                $orderData['address_number'] = $form->getAddressNumber();
                $orderData['address_building'] = $form->getAddressBuilding();
                $orderData['address_apartment'] = $form->getAddressApartment();
                $orderData['address_floor'] = $form->getAddressFloor();
            }

            // данные для самовывоза [self, now]
            if (in_array($deliveryType->getToken(), [\Model\DeliveryType\Entity::TYPE_SELF, \Model\DeliveryType\Entity::TYPE_NOW])) {
                if ($orderPart->getPointId()) {
                    $orderData['shop_id'] = $orderPart->getPointId();
                    $orderData['subway_id'] = null;
                } else {
                    \App::logger()->error(sprintf('Неизвестный магазин #%s', $orderPart->getPointId()), ['order']);
                }
            }

            $isPickpoint = ( $deliveryType->getToken() === \Model\DeliveryType\Entity::TYPE_PICKPOINT ) ? true :false;

            if ( $isPickpoint ) {
                $orderData['id_pickpoint'] = $orderPart->getPointId();
                $orderData['name_pickpoint'] = $orderPart->getPointName();
                //$orderData['point_address'] = $orderPart->getPointAddress();
                $orderData['address_street'] = $orderPart->getPointAddress()['street'];
                $orderData['address_building'] = $orderPart->getPointAddress()['house'];
            }

            // подарочный сертификат
            if (1 == count($form->getPart()) && $form->getPaymentMethodId() == \Model\PaymentMethod\Entity::CERTIFICATE_ID) {
                $orderData['certificate'] = $form->getCertificateCardnumber();
                $orderData['certificate_pin'] = $form->getCertificatePin();
            }


            // товары
            foreach ($orderPart->getProductIds() as $productId) {
                $cartProduct = $user->getCart()->getProductById($productId);
                if (!$cartProduct) {
                    \App::logger()->error(sprintf('Товар #%s не найден в корзине', $productId), ['order']);
                    continue;
                }

                $productData = [
                    'id'       => $cartProduct->getId(),
                    'quantity' => $isPickpoint ? 1 : $cartProduct->getQuantity(),

                ];

                $orderData['product'][] = $productData;


                // Проверим наличие товаров либо услуг, чтобы не было создания заказа с пустой корзиной, SITE-2859
                if ( empty($orderData['product'])  ) {
                    unset($orderData);
                    continue;
                }

                // скидки
                $orderData['action'] = (array)$user->getCart()->getActionData();

                // мета-теги
                if (\App::config()->order['enableMetaTag'] && !$bMeta) {
                    try {
                        /** @var $products \Model\Product\Entity[] */
                        $products = [];
                        \RepositoryManager::product()->prepareCollectionById($orderPart->getProductIds(), $user->getRegion(), function($data) use(&$products) {
                            foreach ($data as $item) {
                                $products[] = new \Model\Product\Entity($item);
                            }
                        }, function(\Exception $e) { \App::exception()->remove($e); });
                        \App::coreClientV2()->execute();

                        foreach ($products as $product) {
                            $partners = [];
                            if ($partnerName = \App::partner()->getName()) {
                                $partners[] = \App::partner()->getName();
                            }
                            // FIXME: похоже, не работает
                            /*
                            foreach (\Controller\Product\BasicRecommendedAction::$recomendedPartners as $recomPartnerName) {
                                if ($viewedAt = \App::user()->getRecommendedProductByParams($product->getId(), $recomPartnerName, 'viewed_at')) {
                                    if ((time() - $viewedAt) <= 30 * 24 * 60 * 60) { // 30days
                                        $partners[] = $recomPartnerName;
                                    } else {
                                        \App::user()->deleteRecommendedProductByParams($product->getId(), $recomPartnerName, 'viewed_at');
                                    }
                                }
                            }
                            */

                            // рекомендации от retail rocket
                            try {
                                $recommendedProductIds = (array)\App::session()->get(\App::config()->product['recommendationSessionKey']);
                                if ((bool)$recommendedProductIds) {
                                    foreach ($recommendedProductIds as $recommendedIndex => $recommendedProductId) {
                                        if ($product->getId() == $recommendedProductId) {
                                            $orderData['meta_data']['product.'. $product->getUi() . '.' . 'sender'] = 'retailrocket'; // FIXME: поправить в будущем - не все рекомендации от rr
                                            unset($recommendedProductIds[$recommendedIndex]);
                                        }
                                    }

                                    \App::session()->set(\App::config()->product['recommendationSessionKey'], $recommendedProductIds);
                                }

                                // добавляем информацию о блоке рекомендаций, откуда был добавлен товар (используется корзина, которая очищается только на /order/complete)
                                $cart = $user->getCart()->getProductsNC();
                                if (isset($cart[$product->getId()]['sender'])) {
                                    $senderData = $cart[$product->getId()]['sender'];
                                    if (isset($senderData['name']))     $orderData['meta_data'][sprintf('product.%s.sender', $product->getUi())] = $senderData['name'];       // система рекомендаций
                                    if (isset($senderData['position'])) $orderData['meta_data'][sprintf('product.%s.position', $product->getUi())] = $senderData['position']; // позиция блока на сайте
                                    if (isset($senderData['method']))   $orderData['meta_data'][sprintf('product.%s.method', $product->getUi())] = $senderData['method'];     // метод рекомендаций
                                    if (isset($senderData['from']) && !empty($senderData['from']))     $orderData['meta_data'][sprintf('product.%s.from', $product->getUi())] = $senderData['from'];         // откуда перешели на карточку товара
                                    unset($senderData);
                                }

                            } catch (\Exception $e) {
                                \App::logger()->error(['error' => $e], ['order', 'partner']);
                            }

                            $orderData['meta_data'] = \App::partner()->fabricateCompleteMeta(
                                isset($orderData['meta_data']) ? $orderData['meta_data'] : [],
                                \App::partner()->fabricateMetaByPartners($partners, $product)
                            );
                            $orderData['meta_data']['user_agent'] = $request->server->get('HTTP_USER_AGENT');
                            $orderData['meta_data']['last_partner'] = $request->cookies->get('last_partner');
                        }
                        \App::logger()->info(['message' => 'Создается заказ от партнеров', 'meta_data' => $orderData['meta_data']['partner']], ['order', 'partner']);
                    } catch (\Exception $e) {
                        \App::logger()->error($e, ['order', 'partner']);
                    }
                    $bMeta = true;
                }
            } // end of foreach ($productId)

            if ( !empty($orderData) ) {
                $data[] = $orderData;
            }
        }

        if (!(bool)$data) {
            throw new \Exception('Корзина пустая');
        }

        $params = [];
        if ($userEntity && $userEntity->getToken()) {
            $params['token'] = $userEntity->getToken();
        }

        $params += ['request_id' => \App::$id]; // SITE-4445

        try {
            $result = \App::coreClientV2()->query(
                (\App::config()->newDeliveryCalc ? 'order/create-packet2' : 'order/create-packet'),
                $params,
                $data,
                \App::config()->coreV2['hugeTimeout']
            );
        } catch(\Exception $e) {
            if (!in_array($e->getCode(), \App::config()->order['excludedError'])) {
                \App::logger('order')->error([
                    'error'   => ['code' => $e->getCode(), 'message' => $e->getMessage(), 'detail' => $e instanceof \Curl\Exception ? $e->getContent() : null, 'trace' => $e->getTraceAsString()],
                    'url'     => (\App::config()->newDeliveryCalc ? 'order/create-packet2' : 'order/create-packet') . ((bool)$params ? ('?' . http_build_query($params)) : ''),
                    'data'    => $data,
                    'server'  => array_map(function($name) use (&$request) { return $request->server->get($name); }, [
                        'HTTP_USER_AGENT',
                        'HTTP_X_REQUESTED_WITH',
                        'HTTP_REFERER',
                        'HTTP_COOKIE',
                        'REQUEST_METHOD',
                        'QUERY_STRING',
                        'REQUEST_TIME_FLOAT',
                    ]),
                ]);
            }

            throw $e;
        }

        \App::logger()->info(['action' => __METHOD__, 'core.response' => $result], ['order']);
        if (!is_array($result)) {
            throw new \Exception('Заказ не подтвержден');
        }

        /** @var $createdOrders \Model\Order\CreatedEntity[] */
        $createdOrders = [];
        foreach ($result as $orderData) {
            if (!is_array($orderData)) {
                \App::logger()->error(['message' => 'Получены неверные данные для созданного заказа', 'orderData' => $orderData], ['order']);
                continue;
            }
            $createdOrder = new \Model\Order\CreatedEntity($orderData);

            // если не получен номер заказа
            if (!$createdOrder->getNumber()) {
                \App::logger()->error(['message' => 'Не получен номер заказа', 'orderData' => $orderData], ['order']);
                continue;
            }
            // если заказ не подтвержден
            if (!$createdOrder->getConfirmed()) {
                \App::logger()->error(['message' => 'Заказ не подтвержден', 'orderData' => $orderData], ['order']);
            }

            $createdOrders[] = $createdOrder;
            \App::logger()->info(['message' => 'Заказ успешно создан', 'orderData' => $orderData], ['order']);
        }

        return $createdOrders;
    }

    /**
     * @param Form $form
     */
    private function subscribeUser(Form $form) {
        $user = \App::user();

        // подписка
        $isSubscribe = $form->getSubscribe();
        $email = $form->getEmail();
        if(!empty($isSubscribe) && !empty($email)) {
            $params = [
                'email'      => $email,
                'geo_id'     => $user->getRegion()->getId(),
                'channel_id' => 1,
            ];
            if ($userEntity = $user->getEntity()) {
                $params['token'] = $userEntity->getToken();
            }

            $result = null;
            \App::coreClientV2()->addQuery('subscribe/create', $params, [], function($data) use (&$result) {
                $result = $data;
                \App::logger()->info(['action' => __METHOD__, 'core.response' => $result], ['order', 'subscribe']);
            }, function(\Exception $e) {
                \App::exception()->remove($e);
            });
            \App::coreClientV2()->execute();
        }
    }
}