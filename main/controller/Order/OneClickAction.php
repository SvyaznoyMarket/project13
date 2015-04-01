<?php

namespace Controller\Order;

class OneClickAction {
    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     * @throws \Exception\NotFoundException
     * @throws \Exception
     */
    public function execute(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__, ['order']);

        $client = \App::coreClientV2();
        $user = \App::user();
        $region = $user->getRegion();

        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http request');
        }

        $userEntity = \App::user()->getEntity();

        try {
            $productToken = $request->get('product');
            if (!$productToken) {
                $e = new \Exception\NotFoundException(sprintf('В GET запросе %s не содержится токена товара для заказа в один клик', json_encode($request->query->all(), JSON_UNESCAPED_UNICODE)));
                \App::logger()->error($e, ['order']);
                throw $e;
            }

            $formData = (array)$request->get('order');
            if (!(bool)$formData) {
                $e = new \Exception\NotFoundException(sprintf('В POST запросе %s не содержится данных о заказе в один клик', json_encode($request->request->all(), JSON_UNESCAPED_UNICODE)));
                \App::logger()->error($e, ['order']);
                throw $e;
            }

            $formData = array_merge([
                'product_quantity'       => 0,
                'delivered_at'           => null,
                'recipient_first_name'   => null,
                'recipient_email'        => null,
                'recipient_phonenumbers' => null,
                'recipient_scCard'       => null,
                'shop_id'                => null,
            ], $formData);

            $productQuantity = (int)$formData['product_quantity'];
            $product = \RepositoryManager::product()->getEntityByToken($productToken);
            if (!$product) {
                $e = new \Exception\NotFoundException(sprintf('Товар @%s не найден в ядре', $productToken));
                \App::logger()->error($e, ['order']);
                throw $e;
            }

            $productsInCart = [
                ['id' => $product->getId(), 'quantity' => $productQuantity]
            ];

            $deliveryTypeId = (int)$formData['delivery_type_id'];
            if (!$deliveryTypeId) {
                throw new \Exception('Не определен метод доставки для заказа в один клик');
            }

            // чистим номер мобильного телефона
            $phone = trim((string)$formData['recipient_phonenumbers']);
            $phone = trim((string)$phone);
            $phone = preg_replace('/^\+7/', '8', $phone);
            $phone = preg_replace('/[^\d]/', '', $phone);
            if (10 == strlen($phone)) {
                $phone = '8' . $phone;
            }
            $formData['recipient_phonenumbers'] = $phone;

            if (empty($formData['recipient_phonenumbers'])) {
                throw new \Exception('Не указан телефонный номер', 400);
            }

            $data = [
                'geo_id'            => \App::user()->getRegion()->getId(),
                'type_id'           => \Model\Order\Entity::TYPE_1CLICK,
                'delivery_type_id'  => $deliveryTypeId,
                'payment_id'        => \Model\PaymentMethod\Entity::CASH_ID, // оплата наличными
                'delivery_date'     => (string)$formData['delivered_at'],
                'first_name'        => (string)$formData['recipient_first_name'],
                'mobile'            => trim((string)$formData['recipient_phonenumbers']),
                'bonus_card_number' => str_replace(' ','', (string)$formData['recipient_scCard']),
                'product'           => $productsInCart,
                'extra'             => 'Это быстрый заказ за 1 клик. Уточните параметры заказа у клиента.',
                'ip'                => $request->getClientIp(),
            ];
            if ($formData['shop_id']) {
                $data['shop_id'] = (int)$formData['shop_id'];
            }
            $data['meta_data'] = [];
            $data['meta_data']['user_agent'] = $request->server->get('HTTP_USER_AGENT');

            // мета-теги
            try {
                $params = [];
                if ($userEntity && $userEntity->getToken()) {
                    $params['token'] = $userEntity->getToken();
                }

                try {
                    $result = \App::coreClientV2()->query('order/create', $params, $data, \App::config()->coreV2['hugeTimeout']);
                } catch (\Exception $e) {
                    if (!in_array($e->getCode(), \App::config()->order['excludedError'])) {
                        try {
                            // пробуем создать заказ без мета-данных
                            $data['meta_data'] = [];
                            $result = \App::coreClientV2()->query('order/create', $params, $data, \App::config()->coreV2['hugeTimeout']);
                        } catch (\Exception $e) {
                            \App::logger('order')->error([
                                'error'   => ['code' => $e->getCode(), 'message' => $e->getMessage(), 'detail' => $e instanceof \Curl\Exception ? $e->getContent() : null, 'trace' => $e->getTraceAsString()],
                                'url'     => 'order/create' . ((bool)$params ? ('?' . http_build_query($params)) : ''),
                                'data'    => $data,
                                'server'  => array_map(function($name) use (&$request) { return $request->server->get($name); }, [
                                    'HTTP_USER_AGENT',
                                    'HTTP_ACCEPT',
                                    'HTTP_ACCEPT_LANGUAGE',
                                    'HTTP_ACCEPT_ENCODING',
                                    'HTTP_X_REQUESTED_WITH',
                                    'HTTP_REFERER',
                                    'HTTP_COOKIE',
                                    'REQUEST_METHOD',
                                    'QUERY_STRING',
                                    'REQUEST_TIME_FLOAT',
                                ]),
                            ]);

                            throw $e;
                        }
                        // все ок, создали заказ без мета-данных
                        \App::exception()->remove($e);
                    } else {
                        \App::exception()->remove($e);

                        throw $e;
                    }
                }

                $orderNumber = !empty($result['number']) ? (string)$result['number'] : null;
                if (!$orderNumber) {
                    throw new \Exception(sprintf('Не получен номер заказа. Ответ ядра: %s', json_encode($result, JSON_UNESCAPED_UNICODE)));
                }

                $order = \RepositoryManager::order()->getEntityByNumberAndPhone($orderNumber, $formData['recipient_phonenumbers']);
                if (!$order) {
                    \App::logger()->error(sprintf('Заказ №%s не найден в ядре', $result['number']), ['order']);
                    $order = new \Model\Order\Entity([
                        'number'  => $result['number'],
                        'product' => [
                            [
                                'id'       => $product->getId(),
                                'price'    => $product->getPrice(),
                                'quantity' => $productQuantity,
                            ],
                        ]
                    ]);
                }
            } catch (\Exception $e) {
                \App::logger()->error($e, ['order']);
                \App::exception()->remove($e);

                throw $e;
            }

            $orderData = [
                'order_article'    => implode(',', array_map(function($orderProduct) { /** @var $orderProduct \Model\Order\Product\Entity */ return $orderProduct->getId(); }, $order->getProduct())),
                'order_id'         => $order->getNumber(),
                'order_total'      => $order->getPaySum(),
                'product_quantity' => implode(',', array_map(function($orderProduct) { /** @var $orderProduct \Model\Order\Product\Entity */ return $orderProduct->getQuantity(); }, $order->getProduct())),
            ];

            $shop = null;
            if ($order->getShopId()) {
                try {
                    $shop = \RepositoryManager::shop()->getEntityById($order->getShopId());
                } catch(\Exception $e) {
                    \App::logger()->error($e, ['order']);
                }
            }

            $orderProducts = $order->getProduct();
            /** @var $orderProduct \Model\Order\Product\Entity */
            $orderProduct = reset($orderProducts);

            $categoryData = [];
            foreach ($product->getCategory() as $category) {
                $categoryData[] = [
                    'id'   => $category->getId(),
                    'name' => $category->getName(),
                ];
            }

            // подписка
            $isSubscribe = isset($formData['subscribe']) ? $formData['subscribe'] : false;
            $email = $formData['recipient_email'];
            if(!empty($isSubscribe) && !empty($email) && preg_match('/@/', $email)) {
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

            return new \Http\JsonResponse([
                'success' => true,
                'message' => 'Заказ успешно создан',
                'data'    => [
                    'title'   => 'Ваш заказ принят, спасибо!',
                    'content' => \App::templating()->render('order/_oneClick', [
                        'page'              => new \View\Layout(),
                        'order'             => $order,
                        'orderData'         => $orderData,
                        'shop'              => $shop,
                        'orderProduct'      => $orderProduct,
                        'product'           => $product,
                    ]),
                    'shop'    => $shop,
                    'orderNumber' => $order->getNumber(),
                    'productArticle' => $product->getArticle(),
                    'productCategory' => $categoryData,

                ],
            ]);
        } catch(\Exception $e){
            switch ($e->getCode()) {
                case 705:
                    $message = 'Запрошенного количества товара нет в наличии';
                    if ($e instanceof \Curl\Exception) {
                        $errorData = $e->getContent();
                        $availableQuantity = isset($errorData['product_error_list'][0]['quantity_available']) ? (int)$errorData['product_error_list'][0]['quantity_available'] : null;
                        $message .=
                            $availableQuantity
                            ? (': доступно только <strong>' . $availableQuantity . ' шт</strong>.')
                            : '';
                    }

                    break;
                case 400:
                    $message = $e->getMessage();
                    break;
                case 735:
                    $message = 'Невалидный номер карты &laquo;Связной-Клуб&raquo;';
                    break;
                default:
                    $message = 'Не удалось создать заказ';
                    break;
            }

            return new \Http\JsonResponse([
                'success' => false,
                'message' => $message,
                'error'   => ['code' => $e->getCode(), 'message' => $message],
                'debug'   => \App::config()->debug ? $e : [],
            ]);
        }
    }
}
