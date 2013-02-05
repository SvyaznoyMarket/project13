<?php

namespace Controller\Order;

class Action {
    const ORDER_COOKIE_NAME = 'last_order';
    const ORDER_SESSION_NAME = 'lastOrder';

    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse|\Http\RedirectResponse|\Http\Response
     * @throws \Exception\NotFoundException
     * @throws \Exception
     */
    public function create(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $user = \App::user();
        $region = $user->getRegion();
        $cart = $user->getCart();
        $userEntity = $user->getEntity();
        $form = $this->getForm();

        try {
            // проверка на пустую корзину
            if ($cart->isEmpty()) {
                \App::logger()->warn('Невозможно начать оформление заказа: в корзине нет товаров и услуг');

                return $request->isXmlHttpRequest()
                    ? new \Http\JsonResponse(array(
                        'success' => true,
                        'data'    => array('redirect' => \App::router()->generate('order.complete')),
                    ))
                    : new \Http\RedirectResponse(\App::router()->generate('cart'));
            }

            // товары и услуги в корзине индексированные по ид
            $cartProductsById = $cart->getProducts();
            $cartServicesById = $cart->getServices();

            $productIds = array_keys($cartProductsById);
            $serviceIds = array_keys($cartServicesById);
            foreach ($cartProductsById as $cartProduct) {
                foreach ($cartProduct->getService() as $serviceCart) {
                    $serviceIds[] = $serviceCart->getId();
                }
            }

            // данные по товарам и услугам для запроса в ядро
            $productsInCart = [];
            foreach ($cartProductsById as $cartProduct) {
                $productsInCart[] = array('id' => $cartProduct->getId(), 'quantity' => $cartProduct->getQuantity());
            }
            $servicesInCart = [];
            // несвязанные услуги
            foreach ($cartServicesById as $cartService) {
                $servicesInCart[] = array('id' => $cartService->getId(), 'quantity' => $cartService->getQuantity());
            }
            // связанные услуги
            foreach ($cartProductsById as $cartProduct) {
                foreach ($cartProduct->getService() as $cartService) {
                    $servicesInCart[] = array('id' => $cartService->getId(), 'quantity' => $cartProduct->getQuantity(), 'product_id' => $cartProduct->getId());
                }
            }

            // подготовка пакета запросов
            // страница с уточнением количества товаров или с ошибкой
            $page = null;

            // магазины
            /** @var $shops \Model\Shop\Entity[] */
            $shops = [];
            // карта доставки
            $deliveryCalcResult = null;
            \App::coreClientV2()->addQuery('order/calc-tmp', array(
                'geo_id'  => $user->getRegion()->getId(),
            ), array(
                'product' => $productsInCart,
                'service' => $servicesInCart,
            ), function($data) use (&$deliveryCalcResult, &$shops) {
                $deliveryCalcResult = $data;
                $shops = array_map(function($data) { return new \Model\Shop\Entity($data); }, $deliveryCalcResult['shops']);
            }, function (\Exception $e) use (&$page) {
                if (!$e instanceof \Core\Exception) {
                    throw $e;
                }

                \App::exception()->remove($e);

                $errorData = (array)$e->getContent();
                $errorData = isset($errorData['product_error_list']) ? (array)$errorData['product_error_list'] : [];

                // товары
                $productIds = array_map(function ($item) { return $item['id']; }, $errorData);
                /** @var $productsById \Model\Product\Entity[] */
                $productsById = [];
                foreach (\RepositoryManager::product()->getCollectionById($productIds) as $product) {
                    $productsById[$product->getId()] = $product;
                }

                if ((bool)$errorData && (bool)$productsById) {
                    foreach ($errorData as &$errorItem) {
                        /** @var $product \Model\Product\Entity */
                        $product = isset($productsById[$errorItem['id']]) ? $productsById[$errorItem['id']] : null;
                        if (!$product) {
                            \App::logger()->error(sprintf('Товар #%s из данных об ошибке %s не найден', $errorItem['id'], json_encode($errorItem, JSON_UNESCAPED_UNICODE)));
                            continue;
                        }
                        $cartProduct = \App::user()->getCart()->getProductById($product->getId());
                        if (!$cartProduct) {
                            \App::logger()->error(sprintf('Товар #%s не найден в корзине', $errorItem['id']));
                            continue;
                        }

                        $errorItem['product'] = [];
                        $errorItem['product']['id'] = $product->getId();
                        $errorItem['product']['token'] = $product->getToken();
                        $errorItem['product']['name'] = $product->getName();
                        $errorItem['product']['image'] = $product->getImageUrl(0);

                        $errorItem['product']['quantity'] = $cartProduct->getQuantity();
                        $errorItem['product']['price'] = $product->getPrice();

                        if (!empty($errorItem['quantity_available'])) {
                            $errorItem['product']['addUrl'] = \App::router()->generate('cart.product.add', array('productId' => $product->getId(), 'quantity' => $errorItem['quantity_available']));
                        }
                        $errorItem['product']['deleteUrl'] = \App::router()->generate('cart.product.delete', array('productId' => $product->getId()));

                        if (708 == $errorItem['code']) {
                            $errorItem['message'] = !empty($errorItem['quantity_available']) ? sprintf('Доступно только %s шт.', $errorItem['quantity_available']) : $errorItem['message'];
                        }

                    } if (isset($errorItem)) unset($errorItem);

                    $page = new \View\Order\WarnPage();
                    $page->setParam('errorData', $errorData);

                    return;
                }
            });
            //$result = json_decode(file_get_contents(\App::config()->dataDir . '/core/v2-order-calc.json'), true);

            // товары и услуги индексированные по ид
            /** @var $productsById \Model\Product\CartEntity[] */
            $productsById = [];
            /** @var $servicesById \Model\Product\Service\Entity[] */
            $servicesById = [];

            // запрашиваем список товаров
            if ((bool)$productIds) {
                \RepositoryManager::product()->prepareCollectionById($productIds, $region, function($data) use(&$productsById, $cartProductsById) {
                    foreach ($data as $item) {
                        $productsById[$item['id']] = new \Model\Product\CartEntity($item);
                    }
                });
            }

            // запрашиваем список услуг
            if ((bool)$serviceIds) {
                \RepositoryManager::service()->prepareCollectionById($serviceIds, $region, function($data) use(&$servicesById, $cartServicesById) {
                    foreach ($data as $item) {
                        $servicesById[$item['id']] = new \Model\Product\Service\Entity($item);
                    }
                });
            }

            // выполнение пакета запросов
            $client->execute();

            // если кто-то из обратных функции асинхронных запросов в ядро определил $page, то выдать результат
            if ($page instanceof \View\Layout) {
                return new \Http\Response($page->show());
            }
            if (!$deliveryCalcResult) {
                $e = new \Exception('Калькулятор доставки вернул пустой результат');
                \App::logger()->error($e->getMessage());

                throw $e;
            }

            $deliveryMap = $this->getDeliveryMap($deliveryCalcResult, $productsById, $servicesById, $shops);
        } catch (\Exception $e) {
            $page = new \View\Order\ErrorPage();
            $page->setParam('exception', $e);

            return new \Http\Response($page->show());
        }

        // сохранение заказа
        if ($request->isMethod('post')) {
            if (!$request->isXmlHttpRequest()) {
                throw new \Exception\NotFoundException('Request is not xml http request');
            }

            if (!is_array($request->request->get('order'))) {
                throw new \Exception(sprintf('Запрос не содержит параметра %s %s', 'order', json_encode($request->request->all(), JSON_UNESCAPED_UNICODE)));
            }

            // обновление формы
            $form->fromArray($request->request->get('order'));

            // валидация формы
            $this->validateForm($form);
            if (!$form->isValid()) {
                $errors = array_filter($form->getErrors(), function($error) { if ($error) return true; }) ;
                return new \Http\JsonResponse(array(
                    'success' => false,
                    'error'   => array('code' => 'invalid', 'message' => 'Форма заполнена неверно'),
                    'errors'  => $errors,
                ));
            }

            try {
                // сохранение заказов в ядре
                $orderNumbers = $this->saveOrder($form, $deliveryMap);

                // сохранение заказов в сессии
                \App::session()->set(self::ORDER_SESSION_NAME, array_map(function($orderNumber) use ($form) {
                    return array('number' => $orderNumber, 'phone' => $form->getMobilePhone());
                }, $orderNumbers));

                $response = new \Http\JsonResponse(array(
                    'success' => true,
                    'data'    => array('redirect' => \App::router()->generate('order.complete')),
                ));

                try {
                    // сохранение заказа в куках
                    $cookieValue = array(
                        'recipient_first_name'   => $form->getFirstName(),
                        'recipient_last_name'    => $form->getLastName(),
                        'recipient_phonenumbers' => $form->getMobilePhone(),
                        'address_street'         => $form->getAddressStreet(),
                        'address_number'         => $form->getAddressNumber(),
                        'address_building'       => $form->getAddressBuilding(),
                        'address_apartment'      => $form->getAddressApartment(),
                        'address_floor'          => $form->getAddressFloor(),
                        'subway_id'              => $form->getSubwayId(),
                    );
                    $cookie = new \Http\Cookie(self::ORDER_COOKIE_NAME, strtr(base64_encode(serialize($cookieValue)), '+/', '-_'), strtotime('+1 year' ));
                    $response->headers->setCookie($cookie);

                    $cookie = new \Http\Cookie('credit_on', '', time() - 3600);
                    $response->headers->setCookie($cookie);

                    // очистка корзины
                    $user->getCart()->clear();
                    // очистка кеша
                    $user->setCacheCookie($response);
                } catch (\Exception $e) {
                    \App::logger()->error($e);
                }
            } catch (\Exception $e) {
                $errors = [];

                if (735 == $e->getCode()) {
                    \App::exception()->remove($e);
                    $errors['order[sclub_card_number]'] = 'Неверный код карты Связной-Клуб';
                }

                $response = new \Http\JsonResponse(array(
                    'success' => false,
                    'error'   => array('code' => 'invalid', 'message' => 'Не удалось создать заказ' . (\App::config()->debug ? (': ' . $e) : '')),
                    'errors'  => $errors,
                ));
            }

            return $response;
        }

        // подготовка пакета запросов

        // запрашиваем список станций метро
        /** @var $subwayData array */
        $subwayData = [];
        if ($user->getRegion()->getHasSubway()) {
            \RepositoryManager::subway()->prepareCollectionByRegion($user->getRegion(), function($data) use (&$subwayData) {
                foreach ($data as $item) {
                    $subwayData[] = array('val' => $item['id'], 'label' => $item['name']);
                }
            }, function(\Exception $e) {
                \App::exception()->remove($e);
            });
        }

        // запрашиваем список способов оплаты
        /** @var $paymentMethods \Model\PaymentMethod\Entity[] */
        $paymentMethods = [];
        $selectedPaymentMethodId = null;
        $creditAllowed = \App::config()->payment['creditEnabled'] && ($user->getCart()->getTotalProductPrice()) >= \App::config()->product['minCreditPrice'];
        \RepositoryManager::paymentMethod()->prepareCollection(null, $userEntity ? $userEntity->getIsCorporative() : false, function($data)
            use (
                &$paymentMethods,
                &$selectedPaymentMethodId,
                $creditAllowed,
                $user,
                $request
            ) {
                foreach ($data as $i => $item) {
                    $paymentMethod = new \Model\PaymentMethod\Entity($item);

                    // кредит
                    if ($paymentMethod->getIsCredit() && !$creditAllowed) {
                        continue;
                    }

                    // подарочный сертификат
                    if ($user->getRegion()->getHasTransportCompany() && $paymentMethod->isCertificate()) {
                        continue;
                    }

                    // кредит
                    if ($creditAllowed && $request->cookies->get('credit_on')) {
                        if ($paymentMethod->getIsCredit()) {
                            $selectedPaymentMethodId = $paymentMethod->getId();
                        }
                    } elseif (null == $selectedPaymentMethodId) {
                        $selectedPaymentMethodId = $paymentMethod->getId();
                    }

                    $paymentMethods[] = $paymentMethod;
                }
        });

        // запрашиваем список кредитных банков
        /** @var $banks \Model\CreditBank\Entity[] */
        $banks = [];
        \RepositoryManager::creditBank()->prepareCollection(function($data) use (&$banks) {
            foreach ($data as $item) {
                $banks[] = new \Model\CreditBank\Entity($item);
            }
        });

        // выполнение пакета запросов
        $client->execute();

        // json для кредитных банков
        rsort($banks);
        $bankData = [];
        foreach ($banks as $bank) {
            $bankData[$bank->getId()]['name'] = $bank->getName();
            $bankData[$bank->getId()]['href'] = $bank->getLink();
        }

        // json для кредита
        $creditData = [];
        foreach ($cartProductsById as $cartProduct) {
            /** @var $product \Model\Product\CartEntity|null */
            $product = isset($productsById[$cartProduct->getId()]) ? $productsById[$cartProduct->getId()] : null;
            if (!$product) {
                \App::logger()->error(sprintf('Товар #%s не найден', $cartProduct->getId()));
                continue;
            }

            $creditData[] = array(
                'id'       => $product->getId(),
                'quantity' => $cartProduct->getQuantity(),
                'price'    => $cartProduct->getPrice(),
                'type'     => \RepositoryManager::creditBank()->getCreditTypeByCategoryToken($product->getMainCategory() ? $product->getMainCategory()->getToken() : null),
            );
        }

        // страница
        $page = new \View\Order\CreatePage();

        // ссылка "вернуться к покупкам"
        $backLink = $page->url('cart');
        // TODO: доделать расчет ссылки

        $page->setParam('form', $form);
        $page->setParam('deliveryMap', $deliveryMap);
        $page->setParam('subwayData', $subwayData);
        $page->setParam('banks', $banks);
        $page->setParam('bankData', $bankData);
        $page->setParam('creditData', $creditData);
        $page->setParam('backLink', $backLink);
        $page->setParam('paymentMethods', $paymentMethods);
        $page->setParam('selectedPaymentMethodId', $selectedPaymentMethodId);

        return new \Http\Response($page->show());
    }

    /**
     * @param \Http\Request $request
     * @return \Http\RedirectResponse|\Http\Response
     * @throws \Exception
     */
    public function complete(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $user = \App::user();

        // последние заказы в сессии
        $orders = $this->getLastOrders();

        /** @var $order \Model\Order\Entity */
        $order = reset($orders);
        if (!$order) {
            \App::logger()->error(sprintf('В сессии нет созданных заказов. Запрос: %s, сессия: %s', json_encode($request->query->all(), JSON_UNESCAPED_UNICODE), json_encode((array)\App::session()->get(self::ORDER_SESSION_NAME), JSON_UNESCAPED_UNICODE)));
            return new \Http\RedirectResponse(\App::router()->generate('cart'));

        }

        // TODO: асинхронные запросы в ядро

        // собираем магазины
        /** @var $shopsById \Model\Shop\Entity[] */
        $shopsById = [];
        foreach ($orders as $order) {
            if (!$order->getShopId()) continue;

            $shopsById[$order->getShopId()] = null;
        }
        if ((bool)$shopsById) {
            foreach (\RepositoryManager::shop()->getCollectionById(array_keys($shopsById)) as $shop) {
                $shopsById[$shop->getId()] = $shop;
            }
        }

        // товары индексированные по ид
        /** @var $productsById \Model\Product\Entity[] */
        $productsById = [];
        foreach ($orders as $order) {
            foreach ($order->getProduct() as $orderProduct) {
                $productsById[$orderProduct->getId()] = null;
            }
        }
        \RepositoryManager::product()->setEntityClass('\Model\Product\Entity');
        foreach (\RepositoryManager::product()->getCollectionById(array_keys($productsById)) as $product) {
            $productsById[$product->getId()] = $product;
        }

        // услуги индексированные по ид
        /** @var $servicesById \Model\Product\Service\Entity[] */
        $servicesById = [];
        foreach ($orders as $order) {
            foreach ($order->getService() as $orderService) {
                $servicesById[$orderService->getId()] = null;
            }
        }
        foreach (\RepositoryManager::service()->getCollectionById(array_map(function ($orderService) { /** @var $orderService \Model\Order\Service\Entity */ return $orderService->getId(); }, $order->getService()), $user->getRegion()) as $service) {
            $servicesById[$service->getId()] = $service;
        }

        // метод оплаты
        $paymentMethod = \RepositoryManager::paymentMethod()->getEntityById($order->getPaymentId());
        if (!$paymentMethod) {
            throw new \Exception(sprintf('Не найден метод оплаты для заказа #%s', $order->getId()));
        }

        $paymentProvider = null;
        $creditData = [];
        if (1 == count($orders)) {
            // онлайн оплата через psb
            if (5 == $paymentMethod->getId()) {
                $paymentProvider = new \Payment\Psb\Provider(\App::config()->paymentPsb);
            // онлайн оплата через psb invoice
            } else if (8 == $paymentMethod->getId()) {
                $paymentProvider = new \Payment\PsbInvoice\Provider(\App::config()->paymentPsbInvoice);
            // если покупка в кредит
            } else if ($paymentMethod->getIsCredit()) {
                if (!$order->getCredit() || !$order->getCredit()->getBankProviderId()) {
                    throw new \Exception(sprintf('Не найден кредитный банк для заказа #%s', $order->getId()));
                }

                $creditProviderId = $order->getCredit()->getBankProviderId();
                if ($creditProviderId == \Model\CreditBank\Entity::PROVIDER_KUPIVKREDIT) {
                    $data = new \View\Order\Credit\Kupivkredit($order, $productsById);
                    $creditData = array(
                        'widget' => 'kupivkredit',
                        'vars'   => array(
                            'sum'   => $order->getProductSum(), // брокеру отпрвляем стоимость только продуктов!
                            'order' => (string)$data,
                            'sig'   => $data->getSig(),
                        ),
                    );
                } else if ($creditProviderId == \Model\CreditBank\Entity::PROVIDER_DIRECT_CREDIT) {

                    $creditData['widget'] = 'direct-credit';
                    $creditData['vars'] = array(
                        'number' => $order->getNumber(),
                        'items' => []
                    );
                    foreach ($order->getProduct() as $orderProduct) {
                        /** @var $product \Model\Product\Entity|null */
                        $product = isset($productsById[$orderProduct->getId()]) ? $productsById[$orderProduct->getId()] : null;
                        if (!$product) {
                            throw new \Exception(sprintf('Не найден товар #%s, который есть в заказе', $orderProduct->getId()));
                        }

                        $creditData['vars']['items'][] = array(
                            'name'     => $product->getName(),
                            'quantity' => (string)$orderProduct->getQuantity(),
                            'price'    => $orderProduct->getPrice(),
                            'articul'  => $product->getArticle(),
                            'type'     => \RepositoryManager::creditBank()->getCreditTypeByCategoryToken($product->getMainCategory() ? $product->getMainCategory()->getToken() : null),
                        );
                    }
                }

            }
        }

        // TODO: удалять из сессии успешный заказ, время создания которого больше 1 часа

        $page = new \View\Order\CompletePage();
        $page->setParam('orders', $orders);
        $page->setParam('shopsById', $shopsById);
        $page->setParam('productsById', $productsById);
        $page->setParam('servicesById', $servicesById);
        $page->setParam('paymentProvider', $paymentProvider);
        $page->setParam('creditData', $creditData);

        return new \Http\Response($page->show());
    }

    /**
     * @param number        $orderNumber
     * @param \Http\Request $request
     * @return \Http\Response
     * @throws \Exception\NotFoundException
     */
    public function paymentComplete($orderNumber, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $orderNumber = trim((string)$orderNumber);
        if (!$orderNumber) {
            throw new \Exception\NotFoundException('Не передан номер заказа');
        }

        $orders = array_filter($this->getLastOrders(), function($order) use ($orderNumber) {
            /** @var $order \Model\Order\Entity */
            if ($order->getNumber() === $orderNumber) return true;
        });
        $order = reset($orders);
        if (!$order) {
            throw new \Exception\NotFoundException(sprintf('Заказ с номером %s не найден в сессии', $orderNumber));
        }


        $page = new \View\Order\CompletePage();
        $page->setParam('orders', $orders);
        $page->setParam('paymentProvider', null);
        $page->setParam('creditData', []);
        $page->setParam('isOrderAnalytics', false);

        return new \Http\Response($page->show());
    }


    /**
     * @param \View\Order\Form             $form        Валидная форма заказа
     * @param \View\Order\DeliveryCalc\Map $deliveryMap Ката доставки заказов
     * @throws \Exception
     * @return array Номера созданных заказов
     */
    private function saveOrder(\View\Order\Form $form, \View\Order\DeliveryCalc\Map $deliveryMap) {
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
            \App::logger()->error($e->getMessage());

            throw $e;
        }

        $data = [];
        foreach ($deliveryData['deliveryTypes'] as $deliveryItem) {
            if (!isset($deliveryTypesById[$deliveryItem['id']])) {
                \App::logger()->error(sprintf('Неизвестный тип доставки %s', json_encode($deliveryItem, JSON_UNESCAPED_UNICODE)));
                continue;
            }

            $deliveryType = $deliveryTypesById[$deliveryItem['id']];

            // общие данные заказа
            $orderData = array(
                'type_id'                   => \Model\Order\Entity::TYPE_ORDER,
                'geo_id'                    => $user->getRegion()->getId(),
                'user_id'                   => $userEntity ? $userEntity->getId() : null,
                'is_legal'                  => $userEntity ? $userEntity->getIsCorporative() : false,
                'payment_id'                => $form->getPaymentMethodId(),
                'credit_bank_id'            => $form->getCreditBankId(),
                'last_name'                 => $form->getLastName(),
                'first_name'                => $form->getFirstName(),
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
            );

            // станция метро
            if ($user->getRegion()->getHasSubway()) {
                $orderData['subway_id'] = $form->getSubwayId();
            }

            // данные для самовывоза
            if ('self' == $deliveryType->getToken()) {
                $shopId = (int)$deliveryItem['shop']['id'];
                if (!array_key_exists($shopId, $shopsById)) {
                    \App::logger()->error(sprintf('Неизвестный магазин %s', json_encode($deliveryItem['shop'], JSON_UNESCAPED_UNICODE)));
                }
                $orderData['shop_id'] = $shopId;
                $orderData['subway_id'] = null;
            }

            // подарочный сертификат
            if (1 == count($deliveryData['deliveryTypes'])) {
                $orderData['certificate'] = $form->getCertificateCardnumber();
                $orderData['certificate_pin'] = $form->getCertificatePin();
            }

            // товары и услуги
            foreach ($deliveryItem['items'] as $itemToken) {
                if (false === strpos($itemToken, '-')) {
                    \App::logger()->error(sprintf('Неправильный элемент заказа %s', json_encode($itemToken, JSON_UNESCAPED_UNICODE)));
                    continue;
                }

                list($itemType, $itemId) = explode('-', $itemToken);

                // товары
                if ('product' == $itemType) {
                    $cartProduct = $user->getCart()->getProductById($itemId);
                    if (!$cartProduct) {
                        \App::logger()->error(sprintf('Элемент заказа %s не найден в корзине', json_encode($itemToken, JSON_UNESCAPED_UNICODE)));
                        continue;
                    }

                    $productData = array(
                        'id'       => $cartProduct->getId(),
                        'quantity' => $cartProduct->getQuantity(),

                    );

                    // расширенная гарантия
                    foreach ($cartProduct->getWarranty() as $cartWarranty) {
                        $productData['additional_warranty'][] = array(
                            'id'         => $cartWarranty->getId(),
                            'quantity'   => $cartProduct->getQuantity(),
                        );
                    }

                    $orderData['product'][] = $productData;

                    // связанные услуги
                    foreach ($cartProduct->getService() as $cartService) {
                        $orderData['service'][] = array(
                            'id'         => $cartService->getId(),
                            'quantity'   => $cartService->getQuantity(),
                            'product_id' => $cartProduct->getId(),
                        );
                    }

                    // несвязанные услуги
                } else if ('service' == $itemType) {
                    $cartService = $user->getCart()->getServiceById($itemId);
                    if (!$cartService) {
                        \App::logger()->error(sprintf('Элемент заказа %s не найден в корзине', json_encode($itemToken, JSON_UNESCAPED_UNICODE)));
                        continue;
                    }
                    $orderData['service'][] = array(
                        'id'       => $cartService->getId(),
                        'quantity' => $cartService->getQuantity(),
                    );
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
        foreach ($result as $orderData) {
            if (empty($orderData['number'])) {
                \App::logger()->error(sprintf('Ошибка при создании заказа %s', json_encode($orderData, JSON_UNESCAPED_UNICODE)));
                continue;
            }
            \App::logger()->debug(sprintf('Заказ %s успешно создан %s', $orderData['number'], json_encode($orderData, JSON_UNESCAPED_UNICODE)));

            $orderNumbers[] = $orderData['number'];
        }

        return $orderNumbers;
    }

    /**
     * @param \View\Order\Form $form
     */
    private function validateForm(\View\Order\Form $form) {
        // мобильный телефон
        $value = $form->getMobilePhone();
        $value = trim((string)$value);
        $value = preg_replace('/^\+7/', '8', $value);
        $value = preg_replace('/[^\d]/', '', $value);
        if (10 == strlen($value)) {
            $value = '8' . $value;
        }
        $form->setMobilePhone($value);
        if (!$form->getMobilePhone()) {
            $form->setError('recipient_phonenumbers', 'Не указан мобильный телефон');
        } else if (11 != strlen($form->getMobilePhone())) {
            $form->setError('recipient_phonenumbers', 'Номер мобильного телефона должен содержать 11 цифр');
        }

        // способ доставки
        if (!$form->getDeliveryTypeId()) {
            $form->setError('delivery_type_id', 'Не указан способ получения заказа');
        } else if ($form->getDeliveryTypeId()) {
            $deliveryType = \RepositoryManager::deliveryType()->getEntityById($form->getDeliveryTypeId());
            if (!$deliveryType) {
                $form->setError('delivery_type_id', 'Способ получения заказа недоступен');
            } else if ('standart' == $deliveryType->getToken()) {
                if (!$form->getAddressStreet()) {
                    $form->setError('address_street', 'Укажите улицу');
                }
                if (!$form->getAddressBuilding()) {
                    $form->setError('address_building', 'Укажите дом');
                }
            }
        }

        // метод оплаты
        if (!$form->getPaymentMethodId()) {
            $form->setError('payment_method_id', 'Не указан способ оплаты');
        } else if ($form->getPaymentMethodId() && (\Model\PaymentMethod\Entity::CERTIFICATE_ID == $form->getPaymentMethodId())) {
            if (!$form->getCertificateCardnumber()) {
                $form->setError('cardnumber', 'Укажите номер карты');
            }
            if (!$form->getCertificatePin()) {
                $form->setError('cardpin', 'Укажите пин карты');
            }
        }
    }

    /**
     * @return \View\Order\Form
     */
    private function getForm() {
        $region = \App::user()->getRegion();
        $request = \App::request();
        $form = new \View\Order\Form();

        // вытащить из куки значения для формы, если пользователь неавторизован
        if ($userEntity = \App::user()->getEntity()) {
            $form->setFirstName($userEntity->getFirstName());
            $form->setLastName($userEntity->getLastName());
            $form->setMobilePhone((strlen($userEntity->getMobilePhone()) > 10)
                    ? substr($userEntity->getMobilePhone(), -10)
                    : $userEntity->getMobilePhone()
            );
        } else {
            $cookieValue = $request->cookies->get(self::ORDER_COOKIE_NAME);
            if (!empty($cookieValue)) {
                $cookieValue = (array)unserialize(base64_decode(strtr($cookieValue, '-_', '+/')));
                $data = [];
                foreach (array(
                     'recipient_first_name',
                     'recipient_last_name',
                     'recipient_phonenumbers',
                     'address_street',
                     'address_number',
                     'address_building',
                     'address_apartment',
                     'address_floor',
                     'subway_id',
                ) as $k) {
                    if (array_key_exists($k, $cookieValue)) {
                        if (('subway_id' == $k) && !$region->getHasSubway()) {
                            continue;
                        }
                        if (('recipient_phonenumbers' == $k) && (strlen($cookieValue[$k])) > 10) {
                            $cookieValue[$k] = substr($cookieValue[$k], -10);
                        }
                        $data[$k] = $cookieValue[$k];
                    }
                }
                $form->fromArray($data);
            }
        }

        return $form;
    }

    /**
     * @param array                           $deliveryCalcResult
     * @param \Model\Product\Entity[]         $productsById
     * @param \Model\Product\Service\Entity[] $servicesById
     * @param \Model\Shop\Entity[]            $shops
     * @return \View\Order\DeliveryCalc\Map
     */
    private function getDeliveryMap(array $deliveryCalcResult, array $productsById, array $servicesById, array $shops) {
        $client = \App::coreClientV2();
        $user = \App::user();
        $region = $user->getRegion();
        $cart = $user->getCart();
        $router = \App::router();

        // товары и услуги в корзине индексированные по ид
        $cartProductsById = $cart->getProducts();
        $cartServicesById = $cart->getServices();

        // карта доставки
        $deliveryMapView = new \View\Order\DeliveryCalc\Map();

        $deliveryMapView->unavailable = [];
        if (array_key_exists('unavailable', $deliveryCalcResult)) {
            foreach ($deliveryCalcResult['unavailable'] as $itemType => $itemIds) {
                $deliveryMapView->unavailable = array_merge($deliveryMapView->unavailable, array_map(function($id) use ($itemType) {
                    return ('products' == $itemType ? 'product' : 'service') . '-' . $id;
                }, $itemIds));
            }
        }

        // сборка магазинов
        foreach ($shops as $shop) {
            $shopView = new \View\Order\DeliveryCalc\Shop();

            $shopView->id = $shop->getId();
            $shopView->address = $shop->getAddress();
            $shopView->latitude = $shop->getLatitude();
            $shopView->longitude = $shop->getLongitude();
            $shopView->name = $shop->getName();
            $shopView->regime = $shop->getRegime();

            $deliveryMapView->shops[$shopView->id] = $shopView;
        }

        // сборка товаров и услуг
        foreach (array('products', 'services') as $itemType) {
            foreach ($deliveryCalcResult[$itemType] as $itemData) {
                $itemData['id'] = (int)$itemData['id'];

                /** @var $cartItem \Model\Cart\Product\Entity|\Model\Cart\Service\Entity|null */
                $cartItem = null;
                if ('products' == $itemType) {
                    if (!isset($cartProductsById[$itemData['id']])) {
                        \App::logger()->error(sprintf('В корзине отсутсвует товар #%s', $itemData['id']));
                        continue;
                    }

                    $cartItem = $cartProductsById[$itemData['id']];
                } else if ('services' == $itemType) {
                    if (!isset($cartServicesById[$itemData['id']])) {
                        //\App::logger()->error(sprintf('В корзине отсутсвует услуга #%s', $itemData['id']));
                        continue;
                    }

                    $cartItem = $cartServicesById[$itemData['id']];
                }
                if (!$cartItem) {
                    \App::logger()->error(sprintf('Не найден элемент корзины %s-%s', $itemType, $itemData['id']));
                    continue;
                }

                $serviceTotal = 0; $serviceName = '';
                if ($cartItem instanceof \Model\Cart\Product\Entity) {

                    foreach ($cartItem->getService() as $cartService) {
                        if (!isset($servicesById[$cartService->getId()])) {
                            \App::logger()->error(sprintf('В индексном массиве услуга #%s отсутсвует', $cartService->getId()));
                            continue;
                        }

                        /** @var $service \Model\Product\Service\Entity */
                        $service = $servicesById[$cartService->getId()];
                        $serviceName .= sprintf(' + <span class="motton">%s (%s шт.)</span>', $service->getName(), $cartService->getQuantity());
                        $serviceTotal += ($cartService->getPrice() * $cartService->getQuantity());
                    }
                }

                // дополнительные гарантии для товара
                if ($cartItem instanceof \Model\Cart\Product\Entity) {
                    /** @var $product \Model\Product\CartEntity */
                    $product = $productsById[$cartItem->getId()];
                    $warrantiesById = [];
                    foreach ($product->getWarranty() as $warranty) {
                        $warrantiesById[$warranty->getId()] = $warranty;
                    }
                    foreach ($cartItem->getWarranty() as $cartWarranty) {
                        /** @var $warranty \Model\Product\Warranty\Entity */
                        $warranty = isset($warrantiesById[$cartWarranty->getId()]) ? $warrantiesById[$cartWarranty->getId()] : null;
                        if (!$warranty) {
                            \App::logger()->error(sprintf('Не найдена гарантия #%s для товара #%s', $cartWarranty->getId(), $product->getId()));
                            continue;
                        }

                        $serviceName .= sprintf(' + <span class="motton">%s (%s шт.)</span>', $warranty->getName(), $cartWarranty->getQuantity());
                        $serviceTotal += ($cartWarranty->getPrice() * $cartWarranty->getQuantity());
                    }
                }

                $itemView = new \View\Order\DeliveryCalc\Item();
                $itemView->url = $itemData['link'];
                if ($cartItem instanceof \Model\Cart\Product\Entity) {
                    $itemView->deleteUrl = $router->generate('cart.product.delete', array('productId' => $itemData['id']));
                } else if ($cartItem instanceof \Model\Cart\Service\Entity) {
                    $itemView->deleteUrl = $router->generate('cart.service.delete', array('serviceId' => $itemData['id'], 'productId' => 0));
                }
                if ($cartItem instanceof \Model\Cart\Product\Entity) {
                    $itemView->addUrl = $router->generate('cart.product.add', array('productId' => $itemData['id'], 'quantity' => $itemData['stock']));
                } else if ($cartItem instanceof \Model\Cart\Service\Entity) {
                    $itemView->addUrl = $router->generate('cart.service.add', array('serviceId' => $itemData['id'], 'quantity' => 1, 'productId' => 0));
                }

                $itemView->id = $itemData['id'];
                $itemView->name = $itemData['name'] . $serviceName;
                $itemView->image = $itemData['media_image'];
                $itemView->price = $itemData['price'];
                $itemView->quantity = $cartItem->getQuantity();
                $itemView->total = ($cartItem->getPrice() * $cartItem->getQuantity()) + $serviceTotal;
                if ($cartItem instanceof \Model\Cart\Product\Entity) {
                    $itemView->type = \View\Order\DeliveryCalc\Item::TYPE_PRODUCT;
                } else if ($cartItem instanceof \Model\Cart\Service\Entity) {
                    $itemView->type = \View\Order\DeliveryCalc\Item::TYPE_SERVICE;
                }
                $itemView->token = $itemView->type . '-' . $itemView->id;
                $itemView->stock = isset($itemData['stock']) ? $itemData['stock'] : 0;

                foreach ($itemData['deliveries'] as $deliveryToken => $deliveryData) {
                    $deliveryView = new \View\Order\DeliveryCalc\Delivery();
                    $deliveryView->price = $deliveryData['price'];
                    $deliveryView->token = $deliveryToken;
                    $deliveryView->name = 0 === strpos($deliveryToken, 'self') ? 'В самовывоз' : 'В доставку';

                    foreach ($deliveryData['dates'] as $dateData) {
                        $dateView = new \View\Order\DeliveryCalc\Date();
                        $dateView->day = date('j', strtotime($dateData['date']));
                        $dateView->dayOfWeek = trim(strftime('%a', strtotime($dateData['date'])), '.');
                        $dateView->value = date('Y-m-d', strtotime($dateData['date']));
                        $dateView->timestamp = strtotime($dateData['date'], 0) * 1000;

                        foreach ($dateData['interval'] as $intervalData) {
                            $intervalView = new \View\Order\DeliveryCalc\Interval();
                            $intervalView->start_at = $intervalData['time_begin'];
                            $intervalView->end_at = $intervalData['time_end'];

                            $dateView->intervals[] = $intervalView;
                        }

                        $deliveryView->dates[] = $dateView;
                    }

                    $itemView->deliveries[$deliveryView->token] = $deliveryView;
                }

                $deliveryMapView->items[$itemView->token] = $itemView;
            }
        }

        // сборка типов доставки
        /** @var $deliveryTypesById \Model\DeliveryType\Entity[] */
        $deliveryTypesById = [];
        foreach (\RepositoryManager::deliveryType()->getCollection() as $deliveryType) {
            $deliveryTypesById[$deliveryType->getId()] = $deliveryType;
        }
        foreach ($deliveryCalcResult['possible_deliveries'] as $deliveryTypeToken => $itemData) {
            $itemData['mode_id'] = (int)$itemData['mode_id'];

            $deliveryType = isset($deliveryTypesById[$itemData['mode_id']]) ? $deliveryTypesById[$itemData['mode_id']] : null;
            if (!$deliveryType) {
                \App::logger()->error(sprintf('Не найден тип доставки #%s', $itemData['mode_id']));
                continue;
            }

            $deliveryTypeView = new \View\Order\DeliveryCalc\Type();
            $deliveryTypeView->description = $deliveryType->getDescription();
            $deliveryTypeView->id = $itemData['mode_id'];
            $deliveryTypeView->name = $deliveryType->getName();
            $deliveryTypeView->type = $deliveryType->getToken();
            $deliveryTypeView->token = $deliveryTypeToken;
            $deliveryTypeView->shortName = 0 === strpos($deliveryTypeView->type, 'self') ? 'Самовывоз' : 'Доставим';

            $deliveryTypeView->shop =
                array_key_exists($itemData['shop_id'], $deliveryMapView->shops)
                    ? $deliveryMapView->shops[$itemData['shop_id']]
                    : null;

            foreach ($deliveryMapView->items as $itemView) {
                if (($itemView->type == \View\Order\DeliveryCalc\Item::TYPE_PRODUCT) && !in_array($itemView->id, $itemData['products'])) continue;
                if (($itemView->type == \View\Order\DeliveryCalc\Item::TYPE_SERVICE) && !in_array($itemView->id, $itemData['services'])) continue;

                $deliveryTypeView->items[] = $itemView->token;
            }

            $tmpDates = null;
            $dates = [];
            foreach ($deliveryTypeView->items as $itemToken) {
                $dates = array_map(function($i) { return $i->value; }, $deliveryMapView->items[$itemToken]->deliveries[$deliveryTypeView->token]->dates);
                $dates = is_array($tmpDates) ? array_intersect($dates, $tmpDates) : $dates;
                $tmpDates = $dates;
            }
            $deliveryTypeView->date = array_shift($dates);
            $deliveryTypeView->displayDate = \Util\Date::strftimeRu('%e %B2', strtotime($deliveryTypeView->date));

            /** @var $interval \View\Order\DeliveryCalc\Interval|null */
            $interval =
                (isset($deliveryTypeView->items[0]) && isset($deliveryMapView->items[$deliveryTypeView->items[0]]->deliveries[$deliveryTypeView->token]->dates[0]->intervals[0]))
                    ? $deliveryMapView->items[$deliveryTypeView->items[0]]->deliveries[$deliveryTypeView->token]->dates[0]->intervals[0]
                    : null
            ;
            if ($interval) {
                $deliveryTypeView->interval = $interval->start_at . ',' . $interval->end_at;
                $deliveryTypeView->displayInterval = 'с ' . $interval->start_at . ' по ' . $interval->end_at;
            }

            $deliveryMapView->deliveryTypes[$deliveryTypeView->token] = $deliveryTypeView;
        }

        foreach ($deliveryMapView->items as $itemView) {
            foreach ($itemView->deliveries as $deliveryView) {
                if (!isset($deliveryMapView->deliveryTypes[$deliveryView->token])) continue;

                $deliveryView->name .= ''
                    .(
                    $deliveryMapView->deliveryTypes[$deliveryView->token]->shop
                        ? (' '.str_replace('г. Москва,', '', $deliveryMapView->deliveryTypes[$deliveryView->token]->shop->address))
                        : ''
                    )
                ;
            }
        }

        return $deliveryMapView;
    }

    /**
     * @return \Model\Order\Entity[]
     */
    private function getLastOrders() {
        $orderData = array_map(function ($orderItem) {
            return array_merge(array('number' => null, 'phone' => null), $orderItem);
        }, (array)\App::session()->get(self::ORDER_SESSION_NAME));
        //$orderData = array(array('number' => 'XX013863', 'phone' => '80000000000'));

        /** @var $orders \Model\Order\Entity[] */
        $orders = [];
        foreach ($orderData as $orderItem) {
            if (!$orderItem['number'] || !$orderItem['phone']) {
                \App::logger()->error(sprintf('Невалидные данные о заказе в сессии %s', json_encode($orderItem, JSON_UNESCAPED_UNICODE)));
                continue;
            }

            // TODO: запрашивать несколько заказов асинхронно
            $order = \RepositoryManager::order()->getEntityByNumberAndPhone($orderItem['number'], $orderItem['phone']);
            if (!$order) {
                \App::logger()->error(sprintf('Заказ из сессии не найден %s', json_encode($orderItem, JSON_UNESCAPED_UNICODE)));
                continue;
            }

            $orders[] = $order;
        }

        return $orders;
    }
}