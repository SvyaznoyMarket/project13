<?php

namespace Controller\Order;

class Action {
    const ORDER_COOKIE_NAME = 'last_order';
    const ORDER_SESSION_NAME = 'lastOrder';

    private $sessionIsReaded = false;

    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse|\Http\RedirectResponse|\Http\Response
     * @throws \Exception\NotFoundException
     * @throws \Exception
     */
    public function create(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (\App::config()->order['newCreate']) {
            return (new \Controller\Order\NewAction())->execute($request);
        }

        $client = \App::coreClientV2();
        $user = \App::user();
        $region = $user->getRegion();
        $cart = $user->getCart();
        $userEntity = $user->getEntity();
        $form = $this->getForm();

        try {
            // проверка на пустую корзину
            if ($cart->isEmpty()) {
                \App::logger()->warn('Невозможно начать оформление заказа: в корзине нет товаров и услуг', ['order']);

                return $request->isXmlHttpRequest()
                    ? new \Http\JsonResponse([
                        'success' => true,
                        'data'    => ['redirect' => \App::router()->generate('order.complete')],
                    ])
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
                $productsInCart[] = ['id' => $cartProduct->getId(), 'quantity' => $cartProduct->getQuantity()];
            }
            $servicesInCart = [];
            // несвязанные услуги
            foreach ($cartServicesById as $cartService) {
                $servicesInCart[] = ['id' => $cartService->getId(), 'quantity' => $cartService->getQuantity()];
            }
            // связанные услуги
            foreach ($cartProductsById as $cartProduct) {
                foreach ($cartProduct->getService() as $cartService) {
                    $servicesInCart[] = ['id' => $cartService->getId(), 'quantity' => $cartProduct->getQuantity(), 'product_id' => $cartProduct->getId()];
                }
            }

            // подготовка пакета запросов

            // магазины
            /** @var $shops \Model\Shop\Entity[] */
            $shops = [];
            // карта доставки
            $deliveryCalcResult = null;

            $calcException = null;
            \App::coreClientV2()->addQuery('order/calc-tmp',
                [
                    'geo_id'  => $user->getRegion()->getId(),
                ],
                [
                    'product' => $productsInCart,
                    'service' => $servicesInCart,
                ], function($data) use (&$deliveryCalcResult, &$shops) {
                    $deliveryCalcResult = $data;
                    $shops = array_map(function($data) { return new \Model\Shop\Entity($data); }, $deliveryCalcResult['shops']);
                }, function (\Exception $e) use (&$calcException) {
                    $calcException = $e;
                },
                \App::config()->coreV2['timeout'] * 4
            );

            // товары и услуги индексированные по ид
            /** @var $productsById \Model\Product\CartEntity[] */
            $productsById = [];
            /** @var $servicesById \Model\Product\Service\Entity[] */
            $servicesById = [];
            /** @var $productsForRetargeting \Model\Product\Entity */
            $productsForRetargeting = [];

            // запрашиваем список товаров
            if ((bool)$productIds) {
                \RepositoryManager::product()->prepareCollectionById($productIds, $region, function($data) use(&$productsById, $cartProductsById, &$productsForRetargeting) {
                    foreach ($data as $item) {
                        $productsById[$item['id']] = new \Model\Product\CartEntity($item);
                        $productsForRetargeting[] = new \Model\Product\Entity($item);
                    }
                });
            }

            // запрашиваем список услуг
            if ((bool)$serviceIds) {
                \RepositoryManager::service()->prepareCollectionById(
                    $serviceIds,
                    $region,
                    function($data) use(&$servicesById, $cartServicesById) {
                        foreach ($data as $item) {
                            $servicesById[$item['id']] = new \Model\Product\Service\Entity($item);
                        }
                    },
                    function(\Exception $e) {
                        \App::exception()->remove($e);
                    }
                );
            }

            // выполнение пакета запросов
            $client->execute();

            // проверка на ошибки калькуляции
            if ($calcException instanceof \Curl\Exception) {
                \App::exception()->remove($calcException);

                $errorData = (array)$calcException->getContent();
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
                            \App::logger()->error(sprintf('Товар #%s из данных об ошибке %s не найден', $errorItem['id'], json_encode($errorItem, JSON_UNESCAPED_UNICODE)), ['order']);
                            continue;
                        }
                        $cartProduct = \App::user()->getCart()->getProductById($product->getId());
                        if (!$cartProduct) {
                            \App::logger()->error(sprintf('Товар #%s не найден в корзине', $errorItem['id']), ['order']);
                            continue;
                        }

                        $errorItem['product'] = [
                            'id'       => $product->getId(),
                            'token'    => $product->getToken(),
                            'name'     => $product->getName(),
                            'image'    => $product->getImageUrl(0),
                            'quantity' => $cartProduct->getQuantity(),
                            'price'    => $product->getPrice(),
                        ];

                        if (!empty($errorItem['quantity_available'])) {
                            $errorItem['product']['addUrl'] = \App::router()->generate('cart.product.set', array('productId' => $product->getId(), 'quantity' => $errorItem['quantity_available']));
                        }
                        $errorItem['product']['deleteUrl'] = \App::router()->generate('cart.product.delete', array('productId' => $product->getId()));

                        if (708 == $errorItem['code']) {
                            $errorItem['message'] = !empty($errorItem['quantity_available']) ? sprintf('Доступно только %s шт.', $errorItem['quantity_available']) : $errorItem['message'];
                        }

                    } if (isset($errorItem)) unset($errorItem);

                    $page = new \View\Order\WarnPage();
                    $page->setParam('errorData', $errorData);

                    return new \Http\Response($page->show());
                }
            } else if ($calcException instanceof \Exception) {
                throw $calcException;
            }

            if (!$deliveryCalcResult) {
                $e = new \Exception('Калькулятор доставки вернул пустой результат');
                \App::logger()->error($e->getMessage(), ['order']);

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
                return new \Http\JsonResponse([
                    'success' => false,
                    'error'   => ['code' => 'invalid', 'message' => 'Форма заполнена неверно'],
                    'errors'  => $errors,
                ]);
            }

            try {
                $deliveryData = array_merge([
                    'deliveryTypes' => [],
                ], json_decode($request->get('delivery_map'), true));

                $actions = [];
                if (\App::config()->coupon['enabled'] && (bool)$cart->getCoupons() && (count($deliveryData['deliveryTypes']) > 1)) {
                    $cart->clearCoupons();
                    $cart->fill();

                    $actions['alert'] = [
                        'message' => 'Не удалось применить скидку. Свяжитесь с оператором Контакт-cENTER ' . \App::config()->company['phone'],
                        'cancel'  => false,
                    ];
                }

                // сохранение заказов в ядре
                $saveOrderResult = $this->saveOrder($form, $deliveryMap, $productsForRetargeting);
                $orderNumbers = $saveOrderResult['orderNumbers'];
                $paymentUrl = $saveOrderResult['paymentUrl'];

                // подписка
                $isSubscribe = $request->request->get('subscribe');
                $email = $form->getEmail();
                if(!empty($isSubscribe) && !empty($email)) {
                    $subscriptionParams = [
                        'email'      => $email,
                        'geo_id'     => $region->getId(),
                        'channel_id' => 1,
                    ];
                    if ($userEntity = \App::user()->getEntity()) {
                        $subscriptionParams['token'] = $userEntity->getToken();
                    }

                    $exception = null;
                    $subscriptionResponse = null;
                    $client->addQuery('subscribe/create', $subscriptionParams, [], function($data) use (&$subscriptionResponse) {
                        $subscriptionResponse = $data;
                    }, function(\Exception $e) use (&$exception) {
                        $exception = $e;
                        \App::exception()->remove($e);
                    });
                    $client->execute(\App::config()->coreV2['retryTimeout']['default'], \App::config()->coreV2['retryCount']);
                }

                // TODO: прибавить к totalSum стоимость доставки
                $totalSum = $user->getCart()->getSum();
                if ($totalSum > \App::config()->order['maxSumOnline'] && in_array($form->getPaymentMethodId(), [\Model\PaymentMethod\Entity::QIWI_ID, \Model\PaymentMethod\Entity::WEBMONEY_ID])) {
                    throw new \Exception(sprintf('Невозможно оформить заказ на %d рублей с выбранным способом оплаты (%d)', $totalSum, $form->getPaymentMethodId()));
                }

                // сохранение заказов в сессии
                \App::session()->set(self::ORDER_SESSION_NAME, array_map(function($orderNumber) use ($form) {
                    return ['number' => $orderNumber, 'phone' => $form->getMobilePhone()];
                }, $orderNumbers));
                // сохранение урла для редиректа в сессии
                \App::session()->set('paymentUrl', $paymentUrl);

                $response = new \Http\JsonResponse([
                    'success'         => true,
                    'data'            => ['redirect' => \App::router()->generate('order.complete')],
                    'orderNumber'     => $orderNumbers,
                    'paymentMethodId' => $form->getPaymentMethodId(),
                    'action'          => $actions,
                ]);

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

                $errcode = $e->getCode();
                if (735 == $errcode) {
                    \App::exception()->remove($e);
                    $errors['order[sclub_card_number]'] = 'Неверный код карты &laquo;Связной-Клуб&raquo;';
                }else if (742 == $errcode) {
                    \App::exception()->remove($e);
                    $errors['order[cardpin]'] = 'Неверный пин-код подарочного сертификата';
                }else if (743 == $errcode) {
                    \App::exception()->remove($e);
                    $errors['order[cardnumber]'] = 'Подарочный сертификат не найден';
                }

                $response = new \Http\JsonResponse([
                    'success' => false,
                    'error'   => ['code' => 'invalid', 'message' => 'Не удалось создать заказ' . (\App::config()->debug ? (': ' . $e) : '')],
                    'errors'  => $errors,
                ]);
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
                    $subwayData[] = ['val' => $item['id'], 'label' => $item['name']];
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
                $blockedIds = (array)\App::config()->payment['blockedIds'];

                foreach ($data as $i => $item) {
                    $paymentMethod = new \Model\PaymentMethod\Entity($item);
                    if (in_array($paymentMethod->getId(), $blockedIds)) continue;

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
                \App::logger()->error(sprintf('Товар #%s не найден', $cartProduct->getId()), ['order']);
                continue;
            }

            $creditData[] = [
                'id'       => $product->getId(),
                'quantity' => $cartProduct->getQuantity(),
                'price'    => $product->getPrice(),
                'type'     => \RepositoryManager::creditBank()->getCreditTypeByCategoryToken($product->getMainCategory() ? $product->getMainCategory()->getToken() : null),
            ];
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
        $page->setParam('productsForRetargeting', $productsForRetargeting);

        return new \Http\Response($page->show());
    }

    /**
     * @param \Http\Request $request
     * @return \Http\RedirectResponse|\Http\Response
     * @throws \Exception
     */
    public function complete(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__, ['order']);

        $user = \App::user();

        $form = $this->getForm(); // подключаем данные формы, чтобы знать данные покупателя

        // последние заказы в сессии
        $orders = $this->getLastOrders();

        /** @var $order \Model\Order\Entity */
        $order = reset($orders);
        if (!$order) {
            \App::logger()->error(sprintf('В сессии нет созданных заказов. Запрос: %s, сессия: %s', json_encode($request->query->all(), JSON_UNESCAPED_UNICODE), json_encode((array)\App::session()->get(self::ORDER_SESSION_NAME), JSON_UNESCAPED_UNICODE)), ['order']);
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
                    $creditData = [
                        'widget' => 'kupivkredit',
                        'vars'   => [
                            'sum'   => $order->getProductSum(), // брокеру отпрвляем стоимость только продуктов!
                            'order' => (string)$data,
                            'sig'   => $data->getSig(),
                        ],
                    ];
                } else if ($creditProviderId == \Model\CreditBank\Entity::PROVIDER_DIRECT_CREDIT) {

                    $creditData['widget'] = 'direct-credit';

                    $shop = $order->getShopId()
                        ? \RepositoryManager::shop()->getEntityById($order->getShopId())
                        : null;
                    if (!$shop) {
                        $shops = \RepositoryManager::shop()->getCollectionByRegion($user->getRegion());
                        $shop = reset($shops);
                    }

                    $creditData['vars'] = [
                        'number' => $order->getNumber(),
                        'region' => $shop ? $shop->getId() : null,
                        'items'  => [],

                    ];

                    foreach ($order->getProduct() as $orderProduct) {
                        /** @var $product \Model\Product\Entity|null */
                        $product = isset($productsById[$orderProduct->getId()]) ? $productsById[$orderProduct->getId()] : null;
                        if (!$product) {
                            throw new \Exception(sprintf('Не найден товар #%s, который есть в заказе', $orderProduct->getId()));
                        }

                        $creditData['vars']['items'][] = [
                            'name'     => sprintf('%s шт %s', $orderProduct->getQuantity(), $product->getName()), // SITE-2662
                            'quantity' => "1", // SITE-2662
                            'price'    => (int)$orderProduct->getSum(), // SITE-2662
                            'articul'  => $product->getArticle(),
                            'type'     => \RepositoryManager::creditBank()->getCreditTypeByCategoryToken($product->getMainCategory() ? $product->getMainCategory()->getToken() : null)
                        ];
                    }
                }

            }
        }

        $paymentUrl = $order->getPaymentUrl(); // раньше было: $paymentUrl = \App::session()->get('paymentUrl');

        $page = new \View\Order\CompletePage();
        $page->setParam('form', $form);
        $page->setParam('orders', $orders);
        $page->setParam('shopsById', $shopsById);
        $page->setParam('productsById', $productsById);
        $page->setParam('servicesById', $servicesById);
        $page->setParam('paymentMethod', $paymentMethod);
        $page->setParam('paymentProvider', $paymentProvider);
        $page->setParam('creditData', $creditData);
        $page->setParam('paymentUrl', $paymentUrl);
        $page->setParam('paymentPageType', 'complete');
        $page->setParam('sessionIsReaded', $this->sessionIsReaded);
        if ($this->sessionIsReaded) {
            $page->setParam('isOrderAnalytics', false);
        }

        return new \Http\Response($page->show());
    }

    /**
     * @param number        $orderNumber
     * @param \Http\Request $request
     * @return \Http\Response
     * @throws \Exception\NotFoundException
     */
    public function paymentComplete($orderNumber, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__, ['order']);

        $orderNumber = trim((string)$orderNumber);
        if (!$orderNumber) {
            throw new \Exception\NotFoundException('Не передан номер заказа');
        }

        $orders = array_filter($this->getLastOrders(), function($order) use ($orderNumber) {
            /** @var $order \Model\Order\Entity */
            if ($order->getNumber() === $orderNumber) return true;
        });
        /** @var $order \Model\Order\Entity */
        $order = reset($orders);
        if (!$order) {
            throw new \Exception\NotFoundException(sprintf('Заказ с номером %s не найден в сессии', $orderNumber));
        }

        $paymentMethod = \RepositoryManager::paymentMethod()->getEntityById($order->getPaymentId());

        $page = new \View\Order\CompletePage();
        $page->setParam('orders', $orders);
        $page->setParam('paymentMethod', $paymentMethod);
        $page->setParam('paymentProvider', null);
        $page->setParam('creditData', []);
        $page->setParam('isOrderAnalytics', false);
        $page->setParam('productsById', []);

        return new \Http\Response($page->show());
    }

    /**
     * @param \Http\Request $request
     * @return \Http\Response
     */
    public function paymentSuccess(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__, ['order']);

        $user = \App::user();

        $form = $this->getForm(); // подключаем данные формы, чтобы знать данные покупателя

        // последние заказы в сессии
        $orders = $this->getLastOrders();

        $page = new \View\Order\PaymentSuccessPage();
        $page->setParam('orders', $orders);
        $page->setParam('form', $form);

        return new \Http\Response($page->show());
    }

    /**
     * @param \Http\Request $request
     * @return \Http\Response
     */
    public function paymentFail(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__, ['order']);

        $user = \App::user();

        $form = $this->getForm(); // подключаем данные формы, чтобы знать данные покупателя

        // последние заказы в сессии
        $orders = $this->getLastOrders();

        $page = new \View\Order\PaymentFailPage();
        $page->setParam('orders', $orders);
        $page->setParam('form', $form);

        return new \Http\Response($page->show());
    }

    /**
     * @param \Http\Request $request
     * @return \Http\Response
     */
    public function clearPaymentUrl(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__, ['order']);
        if ($request->isMethod('post') && $request->isXmlHttpRequest()) {
            \App::session()->remove('paymentUrl');
            return new \Http\JsonResponse([
                'success' => true,
            ]);
        } else {
            throw new \Exception\NotFoundException('Request is not xml http request');
        }
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
                \App::logger()->error(['message' => 'Неизвестный тип доставки', 'item' => $deliveryItem], ['order']);
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
                    \App::logger()->error(sprintf('Неизвестный магазин %s', $shopId), ['order']);
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
                    \App::logger()->error(['message' => 'Неправильный элемент заказа', 'itemToken' => $itemToken], ['order']);
                    continue;
                }

                list($itemType, $itemId) = explode('-', $itemToken);

                // товары
                if ('product' == $itemType) {
                    $cartProduct = $user->getCart()->getProductById($itemId);
                    if (!$cartProduct) {
                        \App::logger()->error(['message' => 'Элемент заказа не найден в корзине', 'itemToken' => $itemToken], ['order']);
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
                        \App::logger()->error(['message' => 'Элемент заказа не найден в корзине', 'itemToken' => $itemToken], ['order']);
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

                            foreach ( \Controller\Product\BasicRecommendedAction::$recomendedPartners as $recomPartnerName) {
                                if ($viewedAt = \App::user()->getRecommendedProductByParams($product->getId(), $recomPartnerName, 'viewed_at')) {
                                    if ((time() - $viewedAt) <= 30 * 24 * 60 * 60) { //30days
                                        $partners[] = $recomPartnerName;
                                    } else {
                                        \App::user()->deleteRecommendedProductByParams($product->getId(), $recomPartnerName, 'viewed_at');
                                    }
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

        $result = \App::coreClientV2()->query('order/create-packet', $params, $data, \App::config()->coreV2['hugeTimeout']);
        if (!is_array($result)) {
            throw new \Exception(sprintf('Заказ не подтвержден. Ответ ядра: %s', json_encode($result, JSON_UNESCAPED_UNICODE)));
        }

        \App::logger()->info(['action' => __METHOD__, 'core.response' => $result], ['order']);

        $orderNumbers = [];
        $paymentUrls = [];
        foreach ($result as $orderData) {
            if (empty($orderData['number'])) {
                \App::logger()->error(['message' => 'Не передан номер заказа', 'orderData' => $orderData], ['order']);
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

        // абтест для обязательно поля email
        if (\App::abTest()->getCase()->getKey() == 'emails' && !$form->getOneClick()) {
            $email = $form->getEmail();
            $emailValidator = new \Validator\Email();
            if (!$emailValidator->isValid($email)) {
                $form->setError('recipient_email', 'Укажите ваш e-mail');
            }
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
        } else if ($form->getPaymentMethodId() && (\Model\PaymentMethod\Entity::QIWI_ID == $form->getPaymentMethodId())) {
            // номер телефона qiwi
            $qiwiValue = $form->getQiwiPhone();
            $qiwiValue = trim((string)$qiwiValue);
            $qiwiValue = preg_replace('/^\+7/', '8', $qiwiValue);
            $qiwiValue = preg_replace('/[^\d]/', '', $qiwiValue);
            if (10 == strlen($qiwiValue)) {
                $qiwiValue = '8' . $qiwiValue;
            }
            $form->setQiwiPhone($qiwiValue);
            if (!$form->getQiwiPhone()) {
                $form->setError('qiwi_phone', 'Не указан мобильный телефон');
            } else if (11 != strlen($form->getQiwiPhone())) {
                $form->setError('qiwi_phone', 'Номер мобильного телефона должен содержать 11 цифр');
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

        // если пользователь авторизован
        if ($userEntity = \App::user()->getEntity()) {
            $form->setFirstName($userEntity->getFirstName());
            $form->setLastName($userEntity->getLastName());
            $form->setMobilePhone((strlen($userEntity->getMobilePhone()) > 10)
                    ? substr($userEntity->getMobilePhone(), -10)
                    : $userEntity->getMobilePhone()
            );
            $form->setEmail($userEntity->getEmail());
        // иначе, если пользователь неавторизован, то вытащить из куки значения для формы
        } else {
            $cookieValue = $request->cookies->get(self::ORDER_COOKIE_NAME);
            if (!empty($cookieValue)) {
                try {
                    $cookieValue = (array)unserialize(base64_decode(strtr($cookieValue, '-_', '+/')));
                } catch (\Exception $e) {
                    \App::logger()->error($e, ['order']);
                    $cookieValue = [];
                }
                $data = [];
                foreach ([
                     'recipient_first_name',
                     'recipient_last_name',
                     'recipient_phonenumbers',
                     'recipient_email',
                     'address_street',
                     'address_number',
                     'address_building',
                     'address_apartment',
                     'address_floor',
                     'subway_id',
                ] as $k) {
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

        /** @var $productsById \Model\Product\BasicEntity[] */
        $productsEntityById = [];
        /** @var $productsById \Model\Product\Service\Entity[] */
        $servicesEntityById = [];

        foreach (['products', 'services'] as $itemType) {
            foreach ($deliveryCalcResult[$itemType] as $itemData) {
                if ($itemType == 'products') $productsEntityById[(int)$itemData['id']] = null;
                if ($itemType == 'services') $servicesEntityById[(int)$itemData['id']] = null;
            }
        }


        if ((bool)$productsEntityById) {
            \RepositoryManager::product()->prepareCollectionById(array_keys($productsById), $region, function($data) use (&$productsEntityById) {
                foreach ($data as $item) {
                    $productsEntityById[(int)$item['id']] = new \Model\Product\BasicEntity($item);
                }
            }, function(\Exception $e) {
                \App::exception()->remove($e);
                \App::logger()->error('Не удалось получить товары для баннеров');
            });
        }
        // запрашиваем услуги
        if ((bool)$servicesEntityById) {
            \RepositoryManager::service()->prepareCollectionById(array_keys($servicesById), $region, function($data) use (&$servicesEntityById) {
                foreach ($data as $item) {
                    $servicesEntityById[(int)$item['id']] = new \Model\Product\Service\Entity($item);
                }
            }, function(\Exception $e) {
                \App::exception()->remove($e);
                \App::logger()->error('Не удалось получить услуги для баннеров');
            });
        }

        if ((bool)$productsEntityById || (bool)$servicesEntityById) {
            $client->execute();
        }

        // сборка товаров и услуг
        foreach (['products', 'services'] as $itemType) {
            foreach ($deliveryCalcResult[$itemType] as $itemData) {
                $itemData['id'] = (int)$itemData['id'];

                /** @var $cartItem \Model\Cart\Product\Entity|\Model\Cart\Service\Entity|null */
                $cartItem = null;
                if ('products' == $itemType) {
                    if (!isset($cartProductsById[$itemData['id']])) {
                        \App::logger()->error(sprintf('В корзине отсутсвует товар #%s', $itemData['id']), ['order']);
                        continue;
                    }

                    $cartItem = $cartProductsById[$itemData['id']];
                } else if ('services' == $itemType) {
                    if (!isset($cartServicesById[$itemData['id']])) {
                        //\App::logger()->error(sprintf('В корзине отсутсвует услуга #%s', $itemData['id']), ['order']);
                        continue;
                    }

                    $cartItem = $cartServicesById[$itemData['id']];
                }
                if (!$cartItem) {
                    \App::logger()->error(sprintf('Не найден элемент корзины %s-%s', $itemType, $itemData['id']), ['order']);
                    continue;
                }

                $serviceTotal = 0; $serviceName = ''; $serviceQuan = 0;
                if ($cartItem instanceof \Model\Cart\Product\Entity) {

                    foreach ($cartItem->getService() as $cartService) {
                        if (!isset($servicesById[$cartService->getId()])) {
                            \App::logger()->error(sprintf('В индексном массиве услуга #%s отсутсвует', $cartService->getId()), ['order']);
                            continue;
                        }

                        /** @var $service \Model\Product\Service\Entity */
                        $service = $servicesById[$cartService->getId()];
                        $serviceName .= sprintf(' + <span class="motton">%s (%s шт.)</span>', $service->getName(), $cartService->getQuantity());
                        $serviceQuan += $cartService->getQuantity();
                        $serviceTotal += $cartService->getSum();
                    }
                }

                // дополнительные гарантии для товара
                $warrantyTotal = 0; $warrantyQuan = 0; 
                if ($cartItem instanceof \Model\Cart\Product\Entity) {
                    /** @var $product \Model\Product\CartEntity */
                    $product = $productsById[$cartItem->getId()];
                    if (!$product) {
                        \App::logger()->error(sprintf('Товар в корзине #%s не найден', $cartItem->getId()), ['order']);
                        continue;
                    }

                    $warrantiesById = [];
                    foreach ($product->getWarranty() as $warranty) {
                        $warrantiesById[$warranty->getId()] = $warranty;
                    }
                    foreach ($cartItem->getWarranty() as $cartWarranty) {
                        /** @var $warranty \Model\Product\Warranty\Entity */
                        $warranty = isset($warrantiesById[$cartWarranty->getId()]) ? $warrantiesById[$cartWarranty->getId()] : null;
                        if (!$warranty) {
                            \App::logger()->error(sprintf('Не найдена гарантия #%s для товара #%s', $cartWarranty->getId(), $product->getId()), ['order']);
                            continue;
                        }

                        $serviceName .= sprintf(' + <span class="motton">%s (%s шт.)</span>', $warranty->getName(), $cartWarranty->getQuantity());
                        $warrantyTotal += $cartWarranty->getSum();
                        $warrantyQuan += $cartWarranty->getQuantity();
                    }
                }

                $itemView = new \View\Order\DeliveryCalc\Item();
                $itemView->url = $itemData['link'];
                if ($cartItem instanceof \Model\Cart\Product\Entity) {
                    $itemView->deleteUrl = $router->generate('cart.product.delete', ['productId' => $itemData['id']]);
                } else if ($cartItem instanceof \Model\Cart\Service\Entity) {
                    $itemView->deleteUrl = $router->generate('cart.service.delete', ['serviceId' => $itemData['id'], 'productId' => 0]);
                }
                if ($cartItem instanceof \Model\Cart\Product\Entity) {
                    $itemView->addUrl = $router->generate('cart.product.set', ['productId' => $itemData['id'], 'quantity' => $itemData['stock']]);
                } else if ($cartItem instanceof \Model\Cart\Service\Entity) {
                    $itemView->addUrl = $router->generate('cart.service.set', ['serviceId' => $itemData['id'], 'quantity' => 1, 'productId' => 0]);
                }

                $itemView->id = $itemData['id'];
                if ('products' == $itemType && $productsEntityById[$itemData['id']]) {
                    /** @var $productEntity \Model\Product\Entity */
                    $productEntity = $productsEntityById[$itemData['id']];
                    $itemView->article = $productEntity->getArticle();
                    $itemView->parent_category = $productEntity->getMainCategory() ? $productEntity->getMainCategory()->getName() : null;
                    $itemView->category = $productEntity->getParentCategory() ? $productEntity->getParentCategory()->getName() : null;
                } else if ('services' == $itemType && $servicesEntityById[$itemData['id']]) {
                    /** @var $serviceEntity \Model\Product\Service\Entity */
                    $serviceEntity = $servicesEntityById[$itemData['id']];
                    $itemView->article = $serviceEntity->getToken();
                    $category = $serviceEntity->getCategory();
                    if ($category) {
                        $itemView->parent_category = isset($category[0]) ? $category[0]->getName() : null;
                        $itemView->category = $category[count($category)-1]->getName();
                    }
                }
                $itemView->name = $itemData['name'] . $serviceName;
                $itemView->image = $itemData['media_image'];
                $itemView->price = $itemData['price'];
                $itemView->quantity = $cartItem->getQuantity();
                $itemView->total = $cartItem->getSum() + $serviceTotal +$warrantyTotal;
                $itemView->serviceQ = $serviceQuan;
                $itemView->serviceTotal = $serviceTotal;
                $itemView->warrantyTotal = $warrantyTotal;
                $itemView->warrantyQ = $warrantyQuan;

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
                    $deliveryView->name = preg_match("/self\_|now\_/i",$deliveryToken) ? 'В самовывоз' : 'В доставку';

                    if ('products' == $itemType && $productsEntityById[$itemData['id']] instanceof \Model\Product\BasicEntity) {
                        $deliveryView->isSupplied = $productsEntityById[$itemData['id']]->getState() ? $productsEntityById[$itemData['id']]->getState()->getIsSupplier() : false;
                    } else $deliveryView->isSupplied = false;

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
                \App::logger()->error(sprintf('Не найден тип доставки #%s', $itemData['mode_id']), ['order']);
                continue;
            }

            $deliveryTypeView = new \View\Order\DeliveryCalc\Type();
            $deliveryTypeView->description = $deliveryType->getDescription();
            $deliveryTypeView->id = $itemData['mode_id'];
            $deliveryTypeView->name = $deliveryType->getName();
            $deliveryTypeView->type = $deliveryType->getToken() == 'now' ? 'self' : $deliveryType->getToken();
            $deliveryTypeView->token = $deliveryTypeToken;
            $deliveryTypeView->shortName =  in_array($deliveryTypeView->type, ['self', 'now']) ? 'Самовывоз' : 'Доставим';

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

        if (\App::config()->product['allowBuyOnlyInshop']) {
            if ((bool)$deliveryMapView->deliveryTypes) {
                foreach ($deliveryMapView->deliveryTypes as $key => $delivery) {
                    if (false !== strpos($key, 'now_')) {
                        $delivery->token = str_replace('now_', 'self_', $delivery->token);
                        $delivery->name = 'самовывоз';
                        $delivery->id = 3;
                        unset($deliveryMapView->deliveryTypes[$key]);
                        $deliveryMapView->deliveryTypes[$delivery->token] = $delivery;
                    }
                }
            }

            if ((bool)$deliveryMapView->items) {
                foreach ($deliveryMapView->items as $product => $deliveries) {
                    if ((bool)$deliveries->deliveries) {
                        foreach ($deliveries->deliveries as $token => $delivery) {
                            if (strpos($token, 'now_') !== false) {
                                if (isset($delivery->dates[0])) {
                                    $delivery->dates[0]->isNow = true;
                                }
                                unset($deliveryMapView->items[$product]->deliveries[$token]);
                                $delivery->token = str_replace('now_', 'self_', $delivery->token);
                                $deliveryMapView->items[$product]->deliveries[str_replace('now_', 'self_', $token)] = $delivery;
                            }
                        }
                    }
                }
            }
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
        $orderSession = (array)\App::session()->getWithChecking(self::ORDER_SESSION_NAME);
        if ( isset($orderSession['_is_readed']) ) {
            $this->sessionIsReaded = $orderSession['_is_readed'];
            unset( $orderSession['_is_readed'] );
        }

        $orderData = array_map(function ($orderItem) {
            //if ( !is_array($orderItem) ) return;
            return array_merge([
                'number' => null,
                'phone' => null,
                'coupon_number' => null,
            ], $orderItem);
        }, $orderSession);

        /** @var $orders \Model\Order\Entity[] */
        $orders = [];
        foreach ($orderData as $orderItem) {
            if (!$orderItem['number']) {
                \App::logger()->error(['message' => 'Номер заказа не найден в сессии', 'orderItem' => $orderItem], ['order']);
                continue;
            }
            if (!$orderItem['phone']) {
                \App::logger()->error(['message' => 'Телефонный номер заказа не найден в сессии', 'orderItem' => $orderItem], ['order']);
                // продолжаем работу
                $orderItem['phone'] = '81111111111';
                \App::logger()->error(['message' => 'Используется тестовый номер 81111111111'], ['order']);
            }

            // TODO: запрашивать несколько заказов асинхронно
            $order = \RepositoryManager::order()->getEntityByNumberAndPhone($orderItem['number'], $orderItem['phone']);
            if (!$order) {
                \App::logger()->error(['message' => 'Заказ из сессии не найден', 'orderItem' => $orderItem], ['order']);
                continue;
            }
            // TODO: осторожно, хак
            $order->setMobilePhone($orderItem['phone']);

            $order->setCouponNumber($orderItem['coupon_number']);

            $orders[] = $order;
        }

        return $orders;
    }
}
