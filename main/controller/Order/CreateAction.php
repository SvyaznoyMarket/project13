<?php

namespace Controller\Order;

class CreateAction {
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

        try {
            // проверка на пустую корзину
            if ($cart->isEmpty()) {
                throw new \Exception('Корзина пустая');
            }

            // типы доставок
            /** @var $deliveryTypesById \Model\DeliveryType\Entity[] */
            $deliveryTypesById = [];
            foreach (\RepositoryManager::deliveryType()->getCollection() as $deliveryType) {
                $deliveryTypesById[$deliveryType->getId()] = $deliveryType;
            }

            $params = [];
            $data = [];
            $result = \App::coreClientV2()->query('order/create-packet', $params, $data);

            $responseData = [];

            $responseData['success'] = true;
        } catch(\Exception $e) {
            $this->failResponseData($e, $responseData);
        }

        return new \Http\JsonResponse($responseData);
    }

    /**
     * @param \View\Order\Form $form        Валидная форма заказа
     * @param \View\Order\DeliveryCalc\Map $deliveryMap Ката доставки заказов
     * @param $products
     * @throws \Exception
     * @return array Номера созданных заказов
     */
    private function saveOrder(\View\Order\Form $form, \View\Order\DeliveryCalc\Map $deliveryMap, $products) {
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

        $deliveryData = json_decode($request->get('delivery_map'), true);
        if (empty($deliveryData['deliveryTypes']) ) {
            $e = new \Exception(sprintf('Пустая карта доставки %s', json_encode($request->request->all(), JSON_UNESCAPED_UNICODE)));
            \App::logger()->error($e->getMessage(), ['order']);

            throw $e;
        }

        $data = [];
        $bMeta = false;
        foreach ($deliveryData['deliveryTypes'] as $deliveryItem) {
            if (!isset($deliveryTypesById[$deliveryItem['id']])) {
                \App::logger()->error(sprintf('Неизвестный тип доставки %s', json_encode($deliveryItem, JSON_UNESCAPED_UNICODE)), ['order']);
                continue;
            }

            $deliveryType = $deliveryTypesById[$deliveryItem['id']];

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

            // данные для самовывоза
            if (in_array($deliveryType->getToken(), ['self', 'now'])) {
                $shopId = (int)$deliveryItem['shop']['id'];
                if (!array_key_exists($shopId, $shopsById)) {
                    \App::logger()->error(sprintf('Неизвестный магазин %s', json_encode($deliveryItem['shop'], JSON_UNESCAPED_UNICODE)), ['order']);
                }
                $orderData['shop_id'] = $shopId;
                $orderData['subway_id'] = null;
            }

            // подарочный сертификат
            if (1 == count($deliveryData['deliveryTypes']) && $form->getPaymentMethodId() == \Model\PaymentMethod\Entity::CERTIFICATE_ID) {
                $orderData['certificate'] = $form->getCertificateCardnumber();
                $orderData['certificate_pin'] = $form->getCertificatePin();
            }

            // товары и услуги

            foreach ($deliveryItem['items'] as $itemToken) {
                if (false === strpos($itemToken, '-')) {
                    \App::logger()->error(sprintf('Неправильный элемент заказа %s', json_encode($itemToken, JSON_UNESCAPED_UNICODE)), ['order']);
                    continue;
                }

                list($itemType, $itemId) = explode('-', $itemToken);

                // товары
                if ('product' == $itemType) {
                    $cartProduct = $user->getCart()->getProductById($itemId);
                    if (!$cartProduct) {
                        \App::logger()->error(sprintf('Элемент заказа %s не найден в корзине', json_encode($itemToken, JSON_UNESCAPED_UNICODE)), ['order']);
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

                    // несвязанные услуги
                } else if ('service' == $itemType) {
                    $cartService = $user->getCart()->getServiceById($itemId);
                    if (!$cartService) {
                        \App::logger()->error(sprintf('Элемент заказа %s не найден в корзине', json_encode($itemToken, JSON_UNESCAPED_UNICODE)), ['order']);
                        continue;
                    }
                    $orderData['service'][] = [
                        'id'       => $cartService->getId(),
                        'quantity' => $cartService->getQuantity(),
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
                        foreach ($products as $product) {
                            $partners = [];
                            if ($partnerName = \App::partner()->getName()) {
                                $partners[] = \App::partner()->getName();
                            }
                            if (\Partner\Counter\MyThings::isTracking()) {
                                $partners[] = \Partner\Counter\MyThings::NAME;
                            }
                            if ($viewedAt = \App::user()->getRecommendedProductByParams($product->getId(), \Smartengine\Client::NAME, 'viewed_at')) {
                                if ((time() - $viewedAt) <= 30 * 24 * 60 * 60) { //30days
                                    $partners[] = \Smartengine\Client::NAME;
                                } else {
                                    \App::user()->deleteRecommendedProductByParams($product->getId(), \Smartengine\Client::NAME, 'viewed_at');
                                }
                            }
                            $orderData['meta_data'] =  \App::partner()->fabricateCompleteMeta(
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

        $orderNumbers = [];
        $paymentUrls = [];
        foreach ($result as $orderData) {
            if (empty($orderData['number'])) {
                \App::logger()->error(sprintf('Ошибка при создании заказа %s', json_encode($orderData, JSON_UNESCAPED_UNICODE)), ['order']);
                continue;
            }
            \App::logger()->debug(sprintf('Заказ %s успешно создан %s', $orderData['number'], json_encode($orderData, JSON_UNESCAPED_UNICODE)));

            $orderNumbers[] = $orderData['number'];

            if (!empty($orderData['payment_url'])) {
                $paymentUrls[] = $orderData['payment_url'];
            }
        }

        $paymentUrl = empty($paymentUrls[0]) ? null : base64_decode($paymentUrls[0]);

        return ['orderNumbers' => $orderNumbers, 'paymentUrl' => $paymentUrl];
    }
}