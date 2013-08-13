<?php

namespace Controller\Order;

use View\Order\NewForm\Form as Form;

class CreateAction {
    use ResponseDataTrait;
    use FormTrait;

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

        $user = \App::user();
        $cart = $user->getCart();

        // форма
        $form = $this->getForm();
        // данные для тела JsonResponse
        $responseData = [
            'time'   => strtotime(date('Y-m-d'), 0) * 1000,
            'action' => [],
        ];
        // массив кукисов
        $cookies = [];

        try {
            // проверка на пустую корзину
            if ($cart->isEmpty()) {
                throw new \Exception('Корзина пустая');
            }
            // проверка на обязательный параметр
            if (!is_array($request->get('order'))) {
                throw new \Exception(sprintf('Запрос не содержит параметра %s %s', 'order', json_encode($request->request->all(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)));
            }

            // обновление формы из параметров запроса
            $form->fromArray($request->request->get('order'));
            // валидация формы
            $this->validateForm($form);
            if (!$form->isValid()) {
                throw new \Exception('Форма заполнена неверно');
            }

            // если заказ разбился более чем на один подзаказ, то ...
            if (\App::config()->coupon['enabled'] && (bool)$cart->getCoupons() && (count($form->getPart()) > 1)) {
                $cart->clearCoupons();
                $cart->fill();

                $responseData['action']['alert'] = [
                    'message' => 'Не удалось применить скидку. Свяжитесь с оператором Контакт-cENTER ' . \App::config()->company['phone'],
                    'cancel'  => false,
                ];
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
            \App::session()->set(\App::config()->order['sessionName'] ?: 'lastOrder', array_map(function(\Model\Order\CreatedEntity $createdOrder) use ($form) {
                return ['number' => $createdOrder->getNumber(), 'phone' => $form->getMobilePhone()];
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
                // сохранение заказа в куках
                $cookieValue = [
                    'recipient_first_name'   => $form->getFirstName(),
                    'recipient_last_name'    => $form->getLastName(),
                    'recipient_phonenumbers' => $form->getMobilePhone(),
                    'recipient_email'        => $form->getEmail(),
                    'address_street'         => $form->getAddressStreet(),
                    'address_number'         => $form->getAddressNumber(),
                    'address_building'       => $form->getAddressBuilding(),
                    'address_apartment'      => $form->getAddressApartment(),
                    'address_floor'          => $form->getAddressFloor(),
                    'subway_id'              => $form->getSubwayId(),
                ];
                $cookies[] = new \Http\Cookie(self::ORDER_COOKIE_NAME, strtr(base64_encode(serialize($cookieValue)), '+/', '-_'), strtotime('+1 year' ));

                // удаление флага "Беру в кредит"
                $cookies[] = new \Http\Cookie('credit_on', '', time() - 3600);

                // очистка корзины
                $user->getCart()->clear();
            } catch (\Exception $e) {
                \App::logger()->error($e, ['order']);
            }
        } catch(\Exception $e) {
            $formErrors = [];
            foreach ($form->getErrors() as $fieldName => $errorMessage) {
                $formErrors[] = ['code' => 'invalid', 'message' => $errorMessage, 'field' => $fieldName];
            }

            switch ($e->getCode()) {
                case 735:
                    \App::exception()->remove($e);
                    $formErrors[] = ['code' => 'invalid', 'message' => 'Неверный код карты Связной-Клуб', 'field' => 'sclub_card_number'];
                    break;
                case 742:
                    \App::exception()->remove($e);
                    $formErrors[] = ['code' => 'invalid', 'message' => 'Неверный пин-код подарочного сертификата', 'field' => 'cardpin'];
                    break;
                case 743:
                    \App::exception()->remove($e);
                    $formErrors[] = ['code' => 'invalid', 'message' => 'Подарочный сертификат не найден', 'field' => 'cardnumber'];
                    break;
            }

            $responseData['form'] = [
                'error' => $formErrors,
            ];

            $this->failResponseData($e, $responseData);

            \App::logger()->error(['action' => __METHOD__, 'request.data' => $request->request->all(), 'error' => $e, 'formError' => $formErrors], ['order']);
        }

        // JsonResponse
        $response = new \Http\JsonResponse($responseData);
        foreach ($cookies as $cookie) {
            $response->headers->setCookie($cookie);
        }

        // очистка кеша
        if ($responseData['success']) {
            $user->setCacheCookie($response);
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

        if (!$form->isValid()) {
            throw new \Exception('Невалидная форма заказа %s');
        }

        /** @var $deliveryTypesById \Model\DeliveryType\Entity[] */
        $deliveryTypesById = [];
        foreach (\RepositoryManager::deliveryType()->getCollection() as $deliveryType) {
            $deliveryTypesById[$deliveryType->getId()] = $deliveryType;
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
            $deliveryType = isset($deliveryTypesById[$orderPart->getDeliveryTypeId()]) ? $deliveryTypesById[$orderPart->getDeliveryTypeId()] : null;
            if (!$deliveryType) {
                \App::logger()->error(sprintf('Неизвестный тип доставки {#%s @%s}', json_encode($orderPart->getDeliveryTypeId(), $orderPart->getDeliveryTypeToken(), JSON_UNESCAPED_UNICODE)), ['order']);
                continue;
            }

            // общие данные заказа
            $orderData = [
                'type_id'                   => \Model\Order\Entity::TYPE_ORDER,
                'geo_id'                    => $user->getRegion()->getId(),
                'user_id'                   => $userEntity ? $userEntity->getId() : null,
                'is_legal'                  => $userEntity ? $userEntity->getIsCorporative() : false,
                'payment_id'                => $form->getPaymentMethodId(),
                'credit_bank_id'            => $form->getCreditBankId(),
                'last_name'                 => $form->getLastName(),
                'first_name'                => $form->getFirstName(),
                'email'                     => $form->getEmail(),
                'mobile'                    => $form->getMobilePhone(),
                'address_street'            => $form->getAddressStreet(),
                'address_number'            => $form->getAddressNumber(),
                'address_building'          => $form->getAddressBuilding(),
                'address_apartment'         => $form->getAddressApartment(),
                'address_floor'             => $form->getAddressFloor(),
                'extra'                     => $form->getComment(),
                'svyaznoy_club_card_number' => $form->getSclubCardnumber(),
                'delivery_type_id'          => $deliveryType->getId(),
                'delivery_period'           => !empty($deliveryItem['interval']) ? explode(',', $deliveryItem['interval']) : null,
                'delivery_date'             => !empty($deliveryItem['date']) ? $deliveryItem['date'] : null,
                'ip'                        => $request->getClientIp(),
                'product'                   => [],
                'service'                   => [],
                'payment_params'            => [
                    'qiwi_phone' => $form->getQiwiPhone(),
                ],
            ];

            // станция метро
            if ($user->getRegion()->getHasSubway()) {
                $orderData['subway_id'] = $form->getSubwayId();
            }

            // данные для самовывоза [self, now]
            if (in_array($deliveryType->getToken(), [\Model\DeliveryType\Entity::TYPE_SELF, \Model\DeliveryType\Entity::TYPE_NOW])) {
                if ($orderPart->getShopId() && array_key_exists($orderPart->getShopId(), $shopsById)) {
                    $orderData['shop_id'] = $orderPart->getShopId();
                    $orderData['subway_id'] = null;
                } else {
                    \App::logger()->error(sprintf('Неизвестный магазин %s', $orderPart->getShopId()), ['order']);
                }

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
                    \App::logger()->error(sprintf('Товар #%s не найден в корзине', json_encode($productId, JSON_UNESCAPED_UNICODE)), ['order']);
                    continue;
                }

                $productData = [
                    'id'       => $cartProduct->getId(),
                    'quantity' => $cartProduct->getQuantity(),

                ];

                // расширенная гарантия
                foreach ($cartProduct->getWarranty() as $cartWarranty) {
                    $productData['additional_warranty'][] = [
                        'id'         => $cartWarranty->getId(),
                        'quantity'   => $cartProduct->getQuantity(),
                    ];
                }

                $orderData['product'][] = $productData;

                // связанные услуги
                foreach ($cartProduct->getService() as $cartService) {
                    $orderData['service'][] = [
                        'id'         => $cartService->getId(),
                        'quantity'   => $cartService->getQuantity(),
                        'product_id' => $cartProduct->getId(),
                    ];
                }

                // скидки
                $actionData = $user->getCart()->getActionData();
                if ((bool)$actionData) {
                    $orderData['action'] = $actionData;
                }

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
                            if (\Partner\Counter\MyThings::isTracking()) {
                                $partners[] = \Partner\Counter\MyThings::NAME;
                            }
                            if ($viewedAt = \App::user()->getRecommendedProductByParams($product->getId(), \Smartengine\Client::NAME, 'viewed_at')) {
                                if ((time() - $viewedAt) <= 30 * 24 * 60 * 60) { // 30days
                                    $partners[] = \Smartengine\Client::NAME;
                                } else {
                                    \App::user()->deleteRecommendedProductByParams($product->getId(), \Smartengine\Client::NAME, 'viewed_at');
                                }
                            }
                            $orderData['meta_data'] = \App::partner()->fabricateCompleteMeta(
                                isset($orderData['meta_data']) ? $orderData['meta_data'] : [],
                                \App::partner()->fabricateMetaByPartners($partners, $product)
                            );
                        }
                        \App::logger()->info(sprintf('Создается заказ от партнеров %s', json_encode($orderData['meta_data']['partner'])), ['order', 'partner']);
                    } catch (\Exception $e) {
                        \App::logger()->error($e, ['order', 'partner']);
                    }
                    $bMeta = true;
                }
            }

            $data[] = $orderData;
        }

        $params = [];
        if ($userEntity && $userEntity->getToken()) {
            $params['token'] = $userEntity->getToken();
        }

        $result = \App::coreClientV2()->query('order/create-packet', $params, $data);
        if (!is_array($result)) {
            throw new \Exception(sprintf('Заказ не подтвержден. Ответ ядра: %s', json_encode($result, JSON_UNESCAPED_UNICODE)));
        }

        \App::logger()->info(['action' => __METHOD__, 'core.response' => $result], ['order']);

        /** @var $createdOrders \Model\Order\CreatedEntity[] */
        $createdOrders = [];
        foreach ($result as $orderData) {
            if (!is_array($orderData)) {
                \App::logger()->error(sprintf('Получены неверные данные для созданного заказа %s', json_encode($orderData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)), ['order']);
                continue;
            }
            $createdOrder = new \Model\Order\CreatedEntity($orderData);

            // если не получен номер заказа
            if (!$createdOrder->getNumber()) {
                \App::logger()->error(sprintf('Не получен номер заказа %s', json_encode($orderData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)), ['order']);
                continue;
            }
            // если заказ не подтвержден
            if (!$createdOrder->getConfirmed()) {
                \App::logger()->error(sprintf('Заказ не подтвержден %s', json_encode($orderData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)), ['order']);
            }

            $createdOrders[] = $createdOrder;
            \App::logger()->info(sprintf('Заказ успешно создан %s', json_encode($orderData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)), ['order']);
        }

        return $createdOrders;
    }

    /**
     * @param Form $form
     */
    private function subscribeUser(Form $form) {
        $user = \App::user();
        $request = \App::request();

        // подписка
        $isSubscribe = $request->request->get('subscribe');
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