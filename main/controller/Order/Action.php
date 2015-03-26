<?php

namespace Controller\Order;

class Action {
    const ORDER_COOKIE_NAME = 'last_order';
    const ORDER_SESSION_NAME = 'lastOrder';

    private $sessionIsReaded = false;

    /**
     * @param \Http\Request $request
     * @return \Http\RedirectResponse|\Http\Response
     * @throws \Exception
     */
    public function complete(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__, ['order']);

        $user = \App::user();
        $userEntity = $user->getEntity();
        $client = \App::coreClientPrivate();
        $helper = new \Helper\TemplateHelper();

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
        $paymentMethod = \RepositoryManager::paymentMethod()->getEntityById($order->getPaymentId(), new \Model\Region\Entity(['id' => \App::config()->region['defaultId']]));
        if (!$paymentMethod) {
            throw new \Exception(sprintf('Не найден метод оплаты для заказа #%s', $order->getId()));
        }

        $cookie = null;
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
                    \App::logger()->error(['message' => 'Не найден кредитный банк для заказа', 'order.id' => $order->getId()], ['order']);
                } else {
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
                            'partnerID' => \App::config()->creditProvider['directcredit']['partnerId'],
                            'number' => $order->getNumber(),
                            'region' => $shop ? $shop->getId() : ( 'r_' . $user->getRegion()->getParentId() ?: $user->getRegion()->getId() ),
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
                                'quantity' => $orderProduct->getQuantity(),
                                'price'    => $orderProduct->getPrice(),
                                'articul'  => $product->getArticle(),
                                'type'     => \RepositoryManager::creditBank()->getCreditTypeByCategoryToken($product->getMainCategory() ? $product->getMainCategory()->getToken() : null)
                            ];
                        }
                    }
                }
            }

            // PaymentConfig
            $paymentForm = null;
            if (in_array($paymentMethod->getId(), [5, 8, 14])) {
                try {
                    // Получение номера sclub
                    $sclub_card_number = null;
                    $sclubId = \Model\Order\BonusCard\Entity::SVYAZNOY_ID;
                    $sclubNumberFromCookies = $request->cookies->get(\App::config()->svyaznoyClub['cardNumber']['cookieName']);

                    // пытаемся получить sclub_card_number с данных формы
                    if ((bool)$form->getBonusCardnumber() && $sclubId == $form->getBonusCardId()) {
                        $sclub_card_number = $form->getBonusCardnumber();

                        // пытаемся подставить номер с куки
                    } elseif ($sclubNumberFromCookies) {
                        $sclub_card_number = $sclubNumberFromCookies;

                        // если авторизован, пытаемся получить sclub_card_number с пользовательских данных
                    } elseif ($userEntity) {
                        $bonusCards = $userEntity->getBonusCard();
                        if ((bool)$bonusCards && is_array($bonusCards)) {
                            foreach ($bonusCards as $card) {
                                if (
                                    !isset($card['bonus_card_id']) || !isset($card['number']) || empty($card['number']) ||
                                    $sclubId != $card['bonus_card_id']
                                ) {
                                    continue;
                                }

                                $sclub_card_number = $card['number'];
                            }
                        }
                    }

                    $result = [];
                    $client->addQuery('site-integration/payment-config',
                        [
                            'method_id' => $paymentMethod->getId(),
                            'order_id'  => $order->getId(),
                        ],
                        [
                            'back_ref'    => $helper->url('order.paymentComplete', array('orderNumber' => $order->getNumber()), true),// обратная ссылка
                            'email'       => $form->getEmail(),
                            'card_number' => $sclub_card_number,
                            'user_token'  => $request->cookies->get('UserTicket'),// токен кросс-авторизации. может быть передан для Связного-Клуба (UserTicket)
                        ],
                        function($data) use (&$result) {
                            if ((bool)$data) {
                                $result = $data;
                            }
                        },
                        function(\Exception $e) {
                            \App::exception()->remove($e);
                        }
                    );
                    $client->execute(\App::config()->corePrivate['retryTimeout']['default'], \App::config()->corePrivate['retryCount']);

                    if (!$result || empty($result) || !is_array($result) || !isset($result['detail'])) {
                        throw new \Exception(sprintf('Не получены payment конфигурация для метода method_id=%s, заказ id=%s', $paymentMethod->getId(), $order->getId()));
                    }

                    // онлайн оплата через psb
                    if (5 == $paymentMethod->getId()) {
                        $paymentForm = new \Payment\Psb\Form();
                        $paymentForm->fromArray($result['detail']);

                    // онлайн оплата через psb invoice
                    } else if (8 == $paymentMethod->getId()) {
                        $paymentForm = new \Payment\PsbInvoice\Form();
                        $paymentForm->fromArray($result['detail']);

                    // оплаты баллами Связного-Клуба
                    } else if (14 == $paymentMethod->getId()) {
                        $paymentForm = new \Payment\SvyaznoyClub\Form();
                        $paymentForm->fromArray($result['detail']);
                        $paymentProvider = new \Payment\SvyaznoyClub\Provider($paymentForm);

                        // если пришел UserTicket, то пишем в куку
                        if ($paymentForm->getUserTicket()) {
                            $cookie = new \Http\Cookie(
                                \App::config()->svyaznoyClub['userTicket']['cookieName'],
                                $paymentForm->getUserTicket(), time() + \App::config()->svyaznoyClub['cookieLifetime'], '/', null, false, true
                            );
                        }
                    }

                    // перезаписываем PayUrl значением пришедшим с ядра
                    if ($paymentProvider instanceof \Payment\ProviderInterface && !empty($result['url'])) {
                        $paymentProvider->setPayUrl($result['url']);
                    }

                } catch (\Exception $e) {
                    \App::logger()->error($e);
                    \App::exception()->remove($e);
                }
            }
        } else $paymentForm = null;

        $paymentUrl = $order->getPaymentUrl(); // раньше было: $paymentUrl = \App::session()->get('paymentUrl');

        $page = new \View\Order\CompletePage();
        $page->setParam('form', $form);
        $page->setParam('orders', $orders);
        $page->setParam('shopsById', $shopsById);
        $page->setParam('productsById', $productsById);
        $page->setParam('servicesById', $servicesById);
        $page->setParam('paymentMethod', $paymentMethod);
        $page->setParam('paymentProvider', $paymentProvider);
        $page->setParam('paymentForm', $paymentForm);
        $page->setParam('creditData', $creditData);
        $page->setParam('paymentUrl', $paymentUrl);
        $page->setParam('paymentPageType', 'complete');
        $page->setParam('sessionIsReaded', $this->sessionIsReaded);
        if ($this->sessionIsReaded) {
            $page->setParam('isOrderAnalytics', false);
        }

        $response = new \Http\Response($page->show());

        if ($cookie) {
            $response->headers->setCookie($cookie);
        }

        if ($form->getEmail() != '') {
            \App::retailrocket()->setUserEmail($response, $form->getEmail());
        }

        return $response;
    }

    /**
     * @param number        $orderNumber
     * @param \Http\Request $request
     * @return \Http\Response
     * @throws \Exception\NotFoundException
     */
    public function paymentComplete($orderNumber, \Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__, ['order']);

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
        $page->setParam('sessionIsReaded', $this->sessionIsReaded);

        return new \Http\Response($page->show());
    }

    /**
     * @param \Http\Request $request
     * @return \Http\Response
     */
    public function paymentSuccess(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__, ['order']);

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
        //\App::logger()->debug('Exec ' . __METHOD__, ['order']);

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
        //\App::logger()->debug('Exec ' . __METHOD__, ['order']);
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
     * @return \View\Order\Form
     */
    private function getForm() {
        $region = \App::user()->getRegion();
        $request = \App::request();
        $form = new \View\Order\Form();

        $cookieValue = $request->cookies->get(self::ORDER_COOKIE_NAME);
        if (!empty($cookieValue)) {
            try {
                $cookieValue = (array)unserialize(base64_decode(strtr($cookieValue, '-_', '+/')));
            } catch (\Exception $e) {
                \App::logger()->error($e, ['order']);
                $cookieValue = [];
            }
        }

        /**
         * @param array $fields
         */
        $fillForm = function (array $fields = []) use (&$form, $cookieValue, $region) {
            $data = [];
            foreach ($fields as $k) {
                if (!array_key_exists($k, (array)$cookieValue) || (('subway_id' == $k) && !$region->getHasSubway())) continue;

                if (('recipient_phonenumbers' == $k) && (strlen($cookieValue[$k])) > 10) {
                    $cookieValue[$k] = substr($cookieValue[$k], -10);
                }
                $data[$k] = $cookieValue[$k];
            }
            $form->fromArray($data);
        };

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
        } elseif (!empty($cookieValue)) {
            $fields = [
                'recipient_first_name',
                'recipient_last_name',
                'recipient_phonenumbers',
                'recipient_email',
                'subscribe',
                'address_street',
                'address_number',
                'address_building',
                'address_apartment',
                'address_floor',
                'subway_id',
            ];
            $fillForm($fields);
        }

        $fields = [
            'bonus_card_number',
            'bonus_card_id',
        ];
        $fillForm($fields);

        return $form;
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
