<?php

namespace Controller\OrderV3;

use \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity;

class DeliveryAction extends OrderV3 {

    /** Main function
     * @param \Http\Request $request
     * @return \Http\Response
     * @throws \Exception
     */
    public function execute(\Http\Request $request) {
//        $controller = parent::execute($request);
//        if ($controller) {
//            return $controller;
//        }

        \App::logger()->debug('Exec ' . __METHOD__);

        if ($request->isXmlHttpRequest()) {

            $splitData = [];

            try {

                $previousSplit = $this->session->get($this->splitSessionKey);

                if ($previousSplit === null) throw new \Exception('Истекла сессия');

                $splitData = [
                    'previous_split' => $previousSplit,
                    'changes'        => $this->formatChanges($request->request->all(), $previousSplit)
                ];

                $orderDeliveryModel = $this->getSplit($request->request->all());

                if (\App::debug()) {
                    $result['OrderDeliveryRequest'] = json_encode($splitData, JSON_UNESCAPED_UNICODE);
                    $result['OrderDeliveryModel'] = $orderDeliveryModel;
                }


                $page = new \View\OrderV3\DeliveryPage();
                $page->setParam('orderDelivery', $orderDeliveryModel);
                $result['page'] = $page->show();

            } catch (\Curl\Exception $e) {
                \App::exception()->remove($e);
                $result['error']    = ['message' => $e->getMessage()];
                $result['data']     = ['data' => $splitData];
                if ($e->getCode() == 600) {
                    $this->cart->clear();
                    $result['redirect'] = \App::router()->generate('cart');
                }
            } catch (\Exception $e) {
                \App::exception()->remove($e);
                $result['error'] = ['message' => $e->getMessage()];
            }

            return new \Http\JsonResponse(['result' => $result], isset($result['error']) ? 500 : 200);
        }

        try {

            $this->logger(['action' => 'view-page-delivery']);

            if (!$this->session->get($this->splitSessionKey)) return new \Http\RedirectResponse(\App::router()->generate('cart'));

            // сохраняем данные пользователя
            $data['action'] = 'changeUserInfo';
            $data['user_info'] = $this->session->get($this->splitSessionKey)['user_info'];

            //$orderDelivery =  new \Model\OrderDelivery\Entity($this->session->get($this->splitSessionKey));
            $orderDelivery = $this->getSplit($data);

            foreach($orderDelivery->orders as $order) {
                $this->logger(['delivery-self-price' => $order->delivery->price]);
            }

            $subscribeResult = false;  // ответ на подписку
            try {
                // Если стоит галка "подписаться на рассылку"
                $email = !empty($data['user_info']['email']) ? $data['user_info']['email'] : null;
                if (!empty($email)) {
                    $this->addSubscribeRequest($subscribeResult, $email);
                }
            } catch (\Exception $e) {
                \App::logger()->error($e->getMessage(), ['cart/split']);
            }

            // вытаскиваем старые ошибки из предыдущих разбиений
            $oldErrors = $this->session->flash();
            if ($oldErrors && is_array($oldErrors)) {
                foreach ($oldErrors as $error) {
                    // распихиваем их по заказам
                    if ($error instanceof \Model\OrderDelivery\Error && isset($error->details['block_name']) && isset($orderDelivery->orders[$error->details['block_name']])) {
                        $orderDelivery->orders[$error->details['block_name']]->errors[] = $error;
                    }
                }
            }

            $page = new \View\OrderV3\DeliveryPage();
            $page->setParam('orderDelivery', $orderDelivery);

            // http-ответ
            $response = new \Http\Response($page->show());

            // сохраняем результаты подписки в куку
            if ($subscribeResult === true) {
                $response->headers->setCookie(new \Http\Cookie(
                    \App::config()->subscribe['cookieName2'],
                    json_encode(['1' => true]), strtotime('+30 days' ), '/',
                    \App::config()->session['cookie_domain'], false, false
                ));
            }

            return $response;

        } catch (\Curl\Exception $e) {
            \App::exception()->remove($e);
            \App::logger()->error($e->getMessage(), ['curl', 'cart/split']);
            $page = new \View\OrderV3\ErrorPage();
            $page->setParam('error', 'CORE: '.$e->getMessage());
            $page->setParam('step', 2);
            return new \Http\Response($page->show(), 500);
        } catch (\Exception $e) {
            \App::logger()->error($e->getMessage(), ['cart/split']);
            $page = new \View\OrderV3\ErrorPage();
            $page->setParam('error', $e->getMessage());
            $page->setParam('step', 2);
            return new \Http\Response($page->show(), 500);
        }

    }

    public function getSplit(array $data = null, $shopId = null) {

        if (!$this->cart->count()) throw new \Exception('Пустая корзина');

        if ($data) {
            $splitData = [
                'previous_split' => $this->session->get($this->splitSessionKey),
                'changes'        => $this->formatChanges($data, $this->session->get($this->splitSessionKey))
            ];
        } else {
            $product_list = [];
            foreach ($this->cart->getProductData() as $product) $product_list[$product['id']] = $product;

            $splitData = [
                'cart' => [
                    'product_list' => $product_list
                ]
            ];

            if ($shopId) $splitData['shop_id'] = (int)$shopId;
            // Проверка метода getCreditProductIds необходима, т.к. Cart/OneClickCart не имеет этого метода
            if (method_exists($this->cart, 'getCreditProductIds') && !empty($this->cart->getCreditProductIds())) $splitData['payment_method_id'] = \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity::PAYMENT_CREDIT;
        }


        $orderDeliveryData = null;
        foreach ([1, 8] as $i) { // две попытки на расчет доставки: 1*4 и 8*4 секунды
            try {
                $orderDeliveryData = $this->client->query(
                    'cart/split',
                    [
                        'geo_id'     => $this->user->getRegion()->getId(),
                        'request_id' => \App::$id, // SITE-4445
                    ],
                    $splitData,
                    $i * \App::config()->coreV2['timeout']
                );
            } catch (\Exception $e) {
                if ($e->getCode() == 600) throw $e; // когда удалили последний товар
            }

            if ($orderDeliveryData) break; // если получен ответ прекращаем попытки
        }
        if (!$orderDeliveryData) {
            throw new \Exception('Не удалось расчитать доставку. Повторите попытку позже.');
        }

        $orderDelivery = new \Model\OrderDelivery\Entity($orderDeliveryData);
        if (!(bool)$orderDelivery->orders) {
            foreach ($orderDelivery->errors as $error) {
                if (708 == $error->code) {
                    throw new \Exception('Товара нет в наличии');
                }
            }

            throw new \Exception('Отстуствуют данные по заказам');
        }

        // обновляем корзину пользователя
        if (isset($data['action']) && isset($data['params']['id']) && $data['action'] == 'changeProductQuantity') {
            $product = (new \Model\Product\Repository($this->client))->getEntityById($data['params']['id']);
            if ($product !== null) $this->cart->setProduct($product, $data['params']['quantity']);
        }

        // сохраняем в сессию расчет доставки
        $this->session->set($this->splitSessionKey, $orderDeliveryData);

        return $orderDelivery;
    }

    private function formatChanges($data, $previousSplit) {

        $changes = [];

        switch ($data['action']) {

            case 'changeUserInfo':
                $changes['user_info'] = array_merge($previousSplit['user_info'], $data['user_info']);
                break;

            case 'changeDelivery':
                $changes['orders'] = [
                    $data['params']['block_name'] => array_merge(
                        isset($previousSplit['orders'][$data['params']['block_name']]) ? $previousSplit['orders'][$data['params']['block_name']] : [],
                        [
                            'delivery' => ['delivery_method_token' => $data['params']['delivery_method_token']]
                        ]
                    )
                ];
                break;

            case 'changePoint':
                $changes['orders'] = array(
                    $data['params']['block_name'] => $previousSplit['orders'][$data['params']['block_name']]
                );
                $changes['orders'][$data['params']['block_name']]['delivery']['point'] = ['id' => $data['params']['id'], 'token' => $data['params']['token']];
                break;

            case 'changeDate':
                $this->logger(['action' => 'change-date']);
                $changes['orders'] = array(
                    $data['params']['block_name'] => $previousSplit['orders'][$data['params']['block_name']]
                );
                $changes['orders'][$data['params']['block_name']]['delivery']['date'] = $data['params']['date'];
                break;

            case 'changeInterval':
                $changes['orders'] = array(
                    $data['params']['block_name'] => $previousSplit['orders'][$data['params']['block_name']]
                );
                $changes['orders'][$data['params']['block_name']]['delivery']['interval'] = $data['params']['interval'];
                break;

            case 'changePaymentMethod':
                $changes['orders'] = array(
                    $data['params']['block_name'] => $previousSplit['orders'][$data['params']['block_name']]
                );
                if (@$data['params']['by_credit_card'] == 'true') $paymentTypeId = PaymentMethodEntity::PAYMENT_CARD_ON_DELIVERY;
                    else if (@$data['params']['by_online_credit']== 'true') $paymentTypeId = PaymentMethodEntity::PAYMENT_CREDIT;
                    else if (@$data['params']['by_online'] == 'true') $paymentTypeId = PaymentMethodEntity::PAYMENT_CARD_ONLINE;
                    else $paymentTypeId = PaymentMethodEntity::PAYMENT_CASH;
                $changes['orders'][$data['params']['block_name']]['payment_method_id'] = $paymentTypeId;
                break;

            case 'changeProductQuantity':
                $changes['orders'] = array(
                    $data['params']['block_name'] => $previousSplit['orders'][$data['params']['block_name']]
                );

                $id = $data['params']['id'];
                $quantity = $data['params']['quantity'];
                $productsArray = &$changes['orders'][$data['params']['block_name']]['products'];

                array_walk($productsArray, function(&$product) use ($id, $quantity) {
                        if ($product['id'] == $id) $product['quantity'] = (int)$quantity;
                });
                break;
            case 'changeAddress':
                $changes['user_info'] = $previousSplit['user_info'];
                $changes['user_info']['address'] = array_merge($changes['user_info']['address'], $data['params']);
                break;
            case 'changeOrderComment':
                $changes['orders'] = $previousSplit['orders'];
                foreach ($changes['orders'] as &$order) {
                    $order['comment'] = $data['params']['comment'];
                }
                break;
            case 'applyDiscount':
                $changes['orders'] = array(
                    $data['params']['block_name'] => $previousSplit['orders'][$data['params']['block_name']]
                );
                $changes['orders'][$data['params']['block_name']]['discounts'][] = ['number' => $data['params']['number'], 'name' => null, 'type' => null, 'discount' => null];
                break;
            case 'deleteDiscount':
                $changes['orders'] = array(
                    $data['params']['block_name'] => $previousSplit['orders'][$data['params']['block_name']]
                );
                $changes['orders'][$data['params']['block_name']]['discounts'] = array_filter($changes['orders'][$data['params']['block_name']]['discounts'], function($discount) use ($data) {
                    return $discount['number'] != $data['params']['number'];
                });
                break;
            case 'applyCertificate':
                $changes['orders'] = [
                    $data['params']['block_name'] => $previousSplit['orders'][$data['params']['block_name']]
                ];
                $changes['orders'][$data['params']['block_name']]['certificate'] = ['code' => $data['params']['code'], 'pin' => $data['params']['pin']];
                break;
            case 'deleteCertificate':
                $changes['orders'] = [
                    $data['params']['block_name'] => $previousSplit['orders'][$data['params']['block_name']]
                ];
                $changes['orders'][$data['params']['block_name']]['certificate'] = null;
                break;
        }

        return $changes;
    }

    /** Добавление запроса на подписку
     * @param $subscribeResult
     * @param $email
     */
    private function addSubscribeRequest(&$subscribeResult, $email) {

        $subscribeParams = [
            'email'      => $email,
            'geo_id'     => $this->user->getRegion()->getId(),
            'channel_id' => 1,
        ];

        if ($userEntity = $this->user->getEntity()) {
            $subscribeParams['token'] = $userEntity->getToken();
        }

        $this->client->addQuery('subscribe/create', $subscribeParams, [], function($data) use (&$subscribeResult) {
            if (isset($data['subscribe_id']) && isset($data['subscribe_id'])) $subscribeResult = true;
        }, function(\Exception $e) use (&$subscribeResult) {
            \App::exception()->remove($e);
            // "code":910,"message":"Не удается добавить подписку, указанный email уже подписан на этот канал рассылок"
            if ($e->getCode() == 910) $subscribeResult = true;
        });
    }
}