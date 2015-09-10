<?php

namespace Controller\OrderV3;

use Model\OrderDelivery\Error;
use Model\PaymentMethod\PaymentMethod\PaymentMethodEntity;
use Session\AbTest\ABHelperTrait;

class DeliveryAction extends OrderV3 {
    use ABHelperTrait;

    /** Main function
     * @param \Http\Request $request
     * @return \Http\Response
     * @throws \Exception
     */
    public function execute(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $response = parent::execute($request);
        if ($response) {
            return $response;
        }

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
                $this->bindErrors($orderDeliveryModel->errors, $orderDeliveryModel);

                if (\App::debug()) {
                    $result['OrderDeliveryRequest'] = json_encode($splitData, JSON_UNESCAPED_UNICODE);
                    $result['OrderDeliveryModel'] = $orderDeliveryModel;
                }


                $page = new \View\OrderV3\DeliveryPage();
                $page->setParam('orderDelivery', $orderDeliveryModel);
                $result['page'] = $page->slotContent();

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
        } else {
            $this->pushEvent(['step' => 2]);
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
            $this->bindErrors($this->session->flash(), $orderDelivery);

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
            $page->setParam('error', $e->getMessage());
            $page->setParam('step', 2);

            return new \Http\Response($page->show());
        } catch (\Exception $e) {
            \App::logger()->error($e->getMessage(), ['cart/split']);
            $page = new \View\OrderV3\ErrorPage();
            $page->setParam('error', $e->getMessage());
            $page->setParam('step', 2);

            return new \Http\Response($page->show(), 500);
        }
    }

    public function getSplit(array $data = null, $userData = null) {

        if (!$this->cart->count()) throw new \Exception('Пустая корзина');

        if ($data) {
            $splitData = [
                'previous_split' => $this->session->get($this->splitSessionKey),
                'changes'        => $this->formatChanges($data, $this->session->get($this->splitSessionKey))
            ];
        } else {
            $splitData = [
                'cart' => [
                    'product_list' => array_map(function(\Model\Cart\Product\Entity $cartProduct) {
                        return [
                            'id' => $cartProduct->id,
                            'quantity' => $cartProduct->quantity,
                        ];
                    }, $this->cart->getProductsById()),
                ]
            ];

            if ($userData) {
                $splitData += ['user_info' => $userData];
            }

            if (!empty($this->cart->getCreditProductIds())) $splitData['payment_method_id'] = \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity::PAYMENT_CREDIT;

            try {
                // SITE-6016
                if (in_array(\App::user()->getRegion()->parentId, [76, 90])) { // Воронеж, Ярославль
                    switch ( \App::abTest()->getTest('order_delivery_type')->getChosenCase()->getKey()) {
                        case 'self':
                            $splitData += ['delivery_type' => 'self'];
                            break;
                        case 'delivery':
                            $splitData += ['delivery_type' => 'standart'];
                            break;
                    }
                }
            } catch (\Exception $e) {
                \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' .  __LINE__], ['cart.split']);
            }
        }


        $splitResponse = null;
        foreach ([2, 8] as $i) { // две попытки на расчет доставки: 2*4 и 8*4 секунды
            try {
                $splitResponse = $this->client->query(
                    'cart/split',
                    [
                        'geo_id'     => $this->user->getRegion()->getId(),
                        'request_id' => \App::$id, // SITE-4445
                    ],
                    $splitData,
                    $i * \App::config()->coreV2['timeout']
                );
            } catch (\Exception $e) {
                if ($e->getCode() == \Curl\Client::CODE_TIMEOUT) \App::exception()->remove($e);
                if (in_array($e->getCode(), [600, 759])) throw $e; // когда удалили последний товар, некорректный email
            }

            if ($splitResponse) break; // если получен ответ прекращаем попытки
        }
        if (!$splitResponse) {
            throw new \Exception('Не удалось расчитать доставку. Повторите попытку позже.');
        }

        $orderDelivery = new \Model\OrderDelivery\Entity($splitResponse);
        if (!$orderDelivery->orders) {
            foreach ($orderDelivery->errors as $error) {
                if (708 == $error->code) {
                    throw new \Exception(count($splitData['cart']['product_list']) == 1 ? 'Товара нет в наличии' : 'Товаров нет в наличии');
                }
            }

            throw new \Exception('Отстуствуют данные по заказам');
        }

        $medias = [];
        $pointUiToObject = [];
        foreach($orderDelivery->orders as $order) {
            $point = $order->delivery->point ? $orderDelivery->points[$order->delivery->point->token]->list[$order->delivery->point->id] : null;
            if (!empty($point->ui)) {
                $pointUiToObject[$point->ui] = $point;
            }

            \RepositoryManager::product()->prepareProductsMediasByIds(array_map(function(\Model\OrderDelivery\Entity\Order\Product $product) { return $product->id; }, $order->products), $medias);
        }

        if ($pointUiToObject) {
            \App::scmsClient()->addQuery('api/point/get', [
                'uids' => array_keys($pointUiToObject),
            ], [],
                function ($data) use($pointUiToObject) {
                    $partners = [];
                    if (isset($data['partners'])) {
                        foreach ($data['partners'] as $partner) {
                            $partners[$partner['slug']] = $partner;
                        }
                    }

                    if (isset($data['points']) && is_array($data['points'])) {
                        foreach ($data['points'] as $pointData) {
                            if (!empty($pointUiToObject[$pointData['uid']])) {
                                /** @var \Model\OrderDelivery\Entity\Point\DefaultPoint $point */
                                $point = $pointUiToObject[$pointData['uid']];
                                if (!empty($pointData['partner']) && !empty($partners[$pointData['partner']])) {
                                    $partner = $partners[$pointData['partner']];

                                    if (strpos($point->name, 'Постамат') === 0 && strpos($partner['slug'], 'pickpoint') !== false) {
                                        $partner['name'] = 'Постамат PickPoint';
                                    }

                                    $point->group = new \Model\Point\Group($partner);
                                }
                            }
                        }
                    }
                }
            );
        }

        \App::coreClientV2()->execute();

        foreach($orderDelivery->orders as $order) {
            foreach ($order->products as $product) {
                if (isset($medias[$product->id])) {
                    $product->medias = $medias[$product->id];
                }
            }
        }

        // обновляем корзину пользователя
        if (isset($data['action']) && isset($data['params']['id']) && $data['action'] == 'changeProductQuantity') {
            // SITE-5442
            if (isset($orderDelivery->getProductsById()[$data['params']['id']])) {
                $this->cart->update([['ui' => $data['params']['ui'], 'quantity' => $orderDelivery->getProductsById()[$data['params']['id']]->quantity]]);
            } else {
                $this->cart->update([['ui' => $data['params']['ui'], 'quantity' => 0]]);
            }
        }

        // сохраняем в сессию расчет доставки
        $this->session->set($this->splitSessionKey, $splitResponse);

        return $orderDelivery;
    }

    private function formatChanges($data, $previousSplit) {

        $changes = [];

        if (isset($data['user_info']['phone'])) {
            $data['user_info']['phone'] = preg_replace('/^\+7/', '8', $data['user_info']['phone']);
        }

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
                // SITE-5703 TODO remove
                $true_token = strpos($data['params']['token'], '_postamat') !== false ? str_replace('_postamat', '', $data['params']['token']) : $data['params']['token'];
                $changes['orders'][$data['params']['block_name']]['delivery']['point'] = ['id' => $data['params']['id'], 'token' => $true_token];
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

                // SITE-5958
                try {
                    // FIXME: осторожно, мбыть неверный результат при использовании скидки
                    $totalSum = array_reduce($productsArray, function($carry, $item){ return $carry + $item['price'] * $item['quantity']; }, 0.0);
                    if (
                        \App::config()->order['prepayment']['priceLimit']
                        && ($totalSum > \App::config()->order['prepayment']['priceLimit'])
                        && in_array(PaymentMethodEntity::PAYMENT_CARD_ONLINE, $changes['orders'][$data['params']['block_name']]['possible_payment_methods'])
                    ) {
                        $changes['orders'][$data['params']['block_name']]['payment_method_id'] = PaymentMethodEntity::PAYMENT_CARD_ONLINE;
                    }
                } catch (\Exception $e) {
                    \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' .  __LINE__], ['cart.split']);
                }

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

    /** Распихиваем ошибки из общего блока ошибок по заказам
     * @param $errors
     * @param \Model\OrderDelivery\Entity $orderDelivery
     */
    private function bindErrors($errors, \Model\OrderDelivery\Entity &$orderDelivery) {

        if (!is_array($errors)) return;

        foreach ($errors as $error) {

            if (!$error instanceof \Model\OrderDelivery\Error) continue;

            // распихиваем их по заказам
            if (isset($error->details['block_name']) && isset($orderDelivery->orders[$error->details['block_name']])) {
                // Если кода этой ошибки нет в уже существующих ошибках заказа
                if (!in_array($error->code, array_map(function(Error $err){ return $err->code; }, $orderDelivery->orders[$error->details['block_name']]->errors))) {
                    $orderDelivery->orders[$error->details['block_name']]->errors[] = $error;
                }
            } else if ($error->isMaxQuantityError() && count($orderDelivery->orders) == 1) {
                $ord = reset($orderDelivery->orders);
                $orderDelivery->orders[$ord->block_name]->errors[] = $error;
            } else if ($error->isMaxQuantityError()) {
                $orderDelivery->errors[] = $error;
            }
        }
    }
}