<?php

namespace Controller\Order\Paypal;

use View\Order\NewForm\Form as Form;

class CreateAction {
    use \Controller\Order\ResponseDataTrait;
    use \Controller\Order\FormTrait;
    use \Controller\Order\PaypalTrait;

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

        $paypalToken = trim((string)$request->get('token'));
        if (!$paypalToken) {
            throw new \Exception\NotFoundException('Не передан параметр token');
        }

        $paypalPayerId = trim((string)$request->get('PayerID'));
        if (!$paypalToken) {
            throw new \Exception\NotFoundException('Не передан параметр PayerID');
        }

        // форма
        $form = $this->getForm();
        // данные для тела JsonResponse
        $responseData = [
            'time'      => strtotime(date('Y-m-d'), 0) * 1000,
            'action'    => [],
            'paypalECS' => true,
        ];
        // массив кукисов
        $cookies = [];

        $cartProduct = $cart->getPaypalProduct();
        $cartProducts = [$cartProduct];

        try {
            // проверка на пустую корзину
            if (!(bool)$cartProducts) {
                throw new \Exception('Корзина пустая');
            }
            // проверка на обязательный параметр
            if (!is_array($request->get('order'))) {
                throw new \Exception(sprintf('Запрос не содержит параметра %s %s', 'order', json_encode($request->request->all(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)));
            }

            $product = \RepositoryManager::product()->getEntityById($cartProduct->getId());
            if (!$product) {
                throw new \Exception(sprintf('Товар %s не найден', $cartProduct->getId()));
            }
            $cartProduct->setPrice($product->getPrice());

            \App::logger()->info(['request.order' => $request->get('order')], ['order']);

            // обновление формы из параметров запроса
            $form->fromArray($request->get('order'));
            // валидация формы
            $this->validateForm($form);
            if (!$form->isValid()) {
                throw new \Exception('Форма заполнена неверно');
            }

            // проверка на изменение цены
            /** @var $cartProduct \Model\Cart\Product\Entity */
            $cartProduct = reset($cartProducts);

            $parts = $form->getPart();
            /** @var $part \View\Order\NewForm\PartField|null */
            $part = reset($parts);
            if (!$part instanceof \View\Order\NewForm\PartField) {
                throw new \Exception('Подзаказ не получен');
            }

            // расчет доставки для paypal
            $deliveryData = (new \Controller\Order\DeliveryAction())->getResponseData(true);

            $productData = isset($deliveryData['products'][$product->getId()]) ? $deliveryData['products'][$product->getId()] : null;
            if (!$productData) {
                throw new \Exception('не получено ни одного товара');
            }

            $deliveryMethodToken = $part->getDeliveryMethodToken();
            $pointId = $part->getPointId();
            \App::logger()->info(sprintf('Проверка стоимости %s', $part->getDeliveryMethodToken()), ['order', 'paypal']);
            $deliveryPrice = isset($productData['deliveries'][$deliveryMethodToken][$pointId]['price']) ? (int)$productData['deliveries'][$deliveryMethodToken][$pointId]['price'] : 0;
            \App::logger()->info(sprintf('Стоимость доставки %s', $deliveryPrice));

            // проверка paypal
            $result = $this->getPaypalCheckout($paypalToken, $paypalPayerId);
            $paymentAmount = isset($result['payment_amount']) ? (int)$result['payment_amount'] : 0;
            \App::logger()->info(['paypal.payment_amount' => $paymentAmount], ['order', 'paypal']);

            // обновляем стоимость товара
            $cartProduct->setSum($cartProduct->getPrice() * $cartProduct->getQuantity());
            $cartProduct->setDeliverySum($deliveryPrice);

            $cart->setPaypalProduct($cartProduct);
            \App::logger()->info(['cart.paypalProduct' => ['id' => $cartProduct->getId(), 'price' => $cartProduct->getPrice(), 'quantity' => $cartProduct->getQuantity(), 'sum' => $cartProduct->getSum()]], ['order', 'paypal']);

            if ($paymentAmount != ($cartProduct->getSum() + $cartProduct->getDeliverySum())) {
                $result = \App::coreClientV2()->query(
                    'payment/paypal-set-checkout',
                    [
                        'geo_id' => \App::user()->getRegion()->getId(),
                    ],
                    [
                        'amount'          => $cartProduct->getSum(),
                        'delivery_amount' => $deliveryPrice,
                        //'currency'        => 'USD',
                        'return_url'      => \App::router()->generate('order.paypal.new', [], true),
                        'product'         => [
                            [
                                'id'       => $cartProduct->getId(),
                                'quantity' => $cartProduct->getQuantity(),
                            ],
                        ],
                        'service'         => [],
                    ],
                    \App::config()->coreV2['hugeTimeout']
                );
                \App::logger()->info(['core.response' => $result], ['order', 'paypal']);

                if (empty($result['payment_url'])) {
                    throw new \Exception('Не получен урл для редиректа');
                }

                $createdOrder = new \Model\Order\CreatedEntity($result);
                \App::logger()->info(['paymentUrl' => $createdOrder->getPaymentUrl()], ['order', 'paypal']);

                /*
                $responseData['redirect'] = $createdOrder->getPaymentUrl();

                try {
                    // сохранение формы в кукисах
                    $this->saveForm($form, $cookies);
                } catch (\Exception $e) {
                    \App::logger()->error($e, ['order']);
                }
                */
            }


            // создание заказов в ядре
            $createdOrders = $this->saveOrders($form, $paypalToken, $paypalPayerId);

            // сохранение заказов в сессии
            \App::session()->set(\App::config()->order['sessionName'] ?: 'lastOrder', array_map(function(\Model\Order\CreatedEntity $createdOrder) use ($form) {
                return ['number' => $createdOrder->getNumber(), 'phone' => $form->getMobilePhone()];
            }, $createdOrders));

            // подписка пользователя
            $this->subscribeUser($form);

            $responseData['redirect'] = \App::router()->generate('order.complete');

            try {
                // сохранение формы в кукисах
                $this->saveForm($form, $cookies);

                // удаление флага "Купи в кредит"
                $cookies[] = new \Http\Cookie('credit_on', '', time() - 3600);

                // очистка корзины
                $user->getCart()->clearPaypal();
            } catch (\Exception $e) {
                \App::logger()->error($e, ['order']);
            }

            $responseData['success'] = true;
        } catch(\Exception $e) {
            $responseData['form'] = [
                'error' => $this->updateErrors($e, $form),
            ];

            $this->failResponseData($e, $responseData);

            $this->logErrors($request, $e, $form, __METHOD__);
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

        \App::logger()->info(['site.response' => $responseData], ['order', 'paypal']);

        return $response;
    }

    /**
     * @param Form $form
     * @param $paypalToken
     * @param $paypalPayerId
     * @throws \Exception
     * @return \Model\Order\CreatedEntity[]
     */
    private function saveOrders(Form $form, $paypalToken, $paypalPayerId) {
        $request = \App::request();
        $user = \App::user();
        $userEntity = $user->getEntity();

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
                'delivery_period'   => $orderPart->getInterval() ? [$orderPart->getInterval()->getStartAt(), $orderPart->getInterval()->getEndAt()] : null,
                'delivery_date'     => $orderPart->getDate() instanceof \DateTime ? $orderPart->getDate()->format('Y-m-d') : null,
                'ip'                => $request->getClientIp(),
                'product'           => [],
                'service'           => [],
                'payment_params'    => [
                    'qiwi_phone' => $form->getQiwiPhone(),
                ],
            ];

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
                    \App::logger()->error(sprintf('Неизвестный магазин %s', $orderPart->getPointId()), ['order']);
                }
            }

            // TODO: pickpoint

            // подарочный сертификат
            if (1 == count($form->getPart()) && $form->getPaymentMethodId() == \Model\PaymentMethod\Entity::CERTIFICATE_ID) {
                $orderData['certificate'] = $form->getCertificateCardnumber();
                $orderData['certificate_pin'] = $form->getCertificatePin();
            }

            // товары
            foreach ($orderPart->getProductIds() as $productId) {
                $cartProduct = $user->getCart()->getPaypalProduct();
                if (!$cartProduct || ($cartProduct->getId() != $productId)) {
                    \App::logger()->error(sprintf('Товар #%s не найден в корзине', $productId), ['order']);
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
                //$orderData['action'] = (array)$user->getCart()->getActionData();
                $orderData['action'] = [];

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
                            foreach (\Controller\Product\BasicRecommendedAction::$recomendedPartners as $recomPartnerName) {
                                if ($viewedAt = \App::user()->getRecommendedProductByParams($product->getId(), $recomPartnerName, 'viewed_at')) {
                                    if ((time() - $viewedAt) <= 30 * 24 * 60 * 60) { // 30days
                                        $partners[] = $recomPartnerName;
                                    } else {
                                        \App::user()->deleteRecommendedProductByParams($product->getId(), $recomPartnerName, 'viewed_at');
                                    }
                                }
                            }
                            $orderData['meta_data'] = \App::partner()->fabricateCompleteMeta(
                                isset($orderData['meta_data']) ? $orderData['meta_data'] : [],
                                \App::partner()->fabricateMetaByPartners($partners, $product)
                            );
                            $orderData['meta_data']['user_agent'] = $request->server->get('HTTP_USER_AGENT');
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

        $params = [
            'token'   => $paypalToken,
            'PayerID' => $paypalPayerId,
        ];
        if ($userEntity && $userEntity->getToken()) {
            $params['user_token'] = $userEntity->getToken();
        }

        $result = \App::coreClientV2()->query('payment/paypal-create-order', $params, $data, \App::config()->coreV2['hugeTimeout']);
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