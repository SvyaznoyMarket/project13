<?php

namespace Controller\OrderV3;

use Curl\TimeoutException;
use EnterApplication\CurlTrait;
use Model\OrderDelivery\Entity;
use Model\OrderDelivery\Error;
use Model\PaymentMethod\PaymentMethod\PaymentMethodEntity;
use Session\AbTest\ABHelperTrait;

class DeliveryAction extends OrderV3 {
    use ABHelperTrait, CurlTrait;

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

        $bonusCards = [];
        try {
            $bonusCards = (new \Model\Order\BonusCard\Repository($this->client))->getCollection(['product_list' => array_map(function(\Model\Cart\Product\Entity $cartProduct) { return ['id' => $cartProduct->id, 'quantity' => $cartProduct->quantity]; }, $this->cart->getProductsById())]);
        } catch (\Exception $e) {
            \App::logger()->error($e->getMessage(), ['cart/split']);
        }

        $userEntity = \App::user()->getEntity();

        /** @var \Model\User\Address\Entity[] $userAddresses */
        $userAddresses = [];
        /** @var \Model\EnterprizeCoupon\Entity[] $userEnterprizeCoupons */
        $userEnterprizeCoupons = [];
        call_user_func(function() use(&$userAddresses, &$userEnterprizeCoupons, $userEntity) {
            if (!$userEntity) {
                return;
            }

            $curl = $this->getCurl();

            $userAddressQuery = new \EnterQuery\User\Address\Get();
            $userAddressQuery->userUi = $userEntity->getUi();
            $userAddressQuery->prepare();

            /** @var \EnterQuery\Coupon\GetByUserToken $discountQuery */
            $discountQuery = null;
            /** @var \EnterQuery\Coupon\Series\Get $couponQuery */
            $couponQuery = null;
            if ($userEntity->isEnterprizeMember()) {
                $discountQuery = new \EnterQuery\Coupon\GetByUserToken();
                $discountQuery->userToken = $userEntity->getToken();
                $discountQuery->prepare();

                $couponQuery = new \EnterQuery\Coupon\Series\Get();
                $couponQuery->memberType = '1';
                $couponQuery->prepare();
            }

            $curl->execute();

            foreach ($userAddressQuery->response->addresses as $item) {
                $userAddress = new \Model\User\Address\Entity($item);
                if ($userAddress->regionId && $userAddress->regionId === (string)$this->user->getRegion()->getId()) {
                    $userAddresses[] = $userAddress;
                }
            }

            if ($discountQuery && $couponQuery) {
                $discountsGroupedByCouponSeries = [];
                foreach ($discountQuery->response->coupons as $item) {
                    $discount = new \Model\EnterprizeCoupon\DiscountCoupon\Entity($item);
                    $discountsGroupedByCouponSeries[$discount->getSeries()][] = $discount;
                }

                foreach ($couponQuery->response->couponSeries as $item) {
                    $token = isset($item['uid']) ? (string)$item['uid'] : null;
                    if (!$token || !isset($discountsGroupedByCouponSeries[$token])) {
                        continue;
                    }

                    foreach ($discountsGroupedByCouponSeries[$token] as $discount) {
                        $coupon = new \Model\EnterprizeCoupon\Entity($item);
                        $coupon->setDiscount($discount);
                        $userEnterprizeCoupons[] = $coupon;
                    }
                }
            }
        });

        $userInfoAddressAddition = new \Model\OrderDelivery\UserInfoAddressAddition($this->session->get(\App::config()->order['splitAddressAdditionSessionKey']));

        if ($request->isXmlHttpRequest()) {
            $previousSplit = null;

            try {
                $previousSplit = $this->session->get($this->splitSessionKey);

                if ($previousSplit === null) throw new \Exception('Истекла сессия');

                $orderDeliveryModel = $this->getSplit($request->request->all());
                $this->bindErrors($orderDeliveryModel->errors, $orderDeliveryModel);

                if (\App::debug()) {
                    $result['OrderDeliveryModel'] = $orderDeliveryModel;
                }

                $result['page'] = \App::closureTemplating()->render('order-v3-new/page-delivery-with-user', [
                    'ajax'                       => true,
                    'orderDelivery'              => $orderDeliveryModel,
                    'bonusCards'                 => $bonusCards,
                    'hasProductsOnlyFromPartner' => $this->hasProductsOnlyFromPartner(),
                    'userAddresses'              => $userAddresses,
                    'userInfoAddressAddition'    => $userInfoAddressAddition,
                    'userEnterprizeCoupons'      => $userEnterprizeCoupons,
                ]);

            } catch (\Curl\Exception $e) {
                \App::exception()->remove($e);
                $result['error']    = ['message' => $e->getMessage()];
                if (in_array($e->getCode(), [600, 302])) {
                    $result['redirect'] = \App::router()->generate('cart');
                }
            } catch (\Exception $e) {
                \App::exception()->remove($e);
                $result['error'] = ['message' => $e->getMessage()];

                if (302 === $e->getCode()) {
                    $result['redirect'] = \App::router()->generate('cart');
                } else if (!$previousSplit) {
                    $result['redirect'] = \App::router()->generate('cart');
                }
            }

            return new \Http\JsonResponse(['result' => $result], isset($result['error']) ? 500 : 200);
        } else {
            $this->pushEvent(['step' => 2]);
        }

        try {
            $this->logger(['action' => 'view-page-delivery']);

            $data = null;
            $previousSplit = $this->session->get($this->splitSessionKey);
            $userData = $this->session->get('user_info_split');

            if (!$userData) {
                return new \Http\RedirectResponse(\App::router()->generate('cart'));
            }
            $data['action'] = null;
            if ($previousSplit) {
                $data['user_info'] = $previousSplit['user_info'];
            }

            if (!$previousSplit || @$previousSplit['user_info']['phone'] === '') {
                $data['user_info'] = $userData;
            }

            $useNodeMQ = \App::config()->useNodeMQ;

            if ($useNodeMQ) {
                $orderDelivery = new \Model\OrderDelivery\Entity(['user_info' => $data['user_info']], !$useNodeMQ);
            } else {
                $orderDelivery = $this->getSplit(null, $data['user_info']);
                if ($orderDelivery->errors) {
                    $this->session->flash($orderDelivery->errors);
                }
            }

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
            $page->setParam('step', 2);
            $page->setParam('orderDelivery', $orderDelivery);
            $page->setParam('bonusCards', $bonusCards);
            $page->setParam('hasProductsOnlyFromPartner', $this->hasProductsOnlyFromPartner());
            $page->setParam('userAddresses', $userAddresses);
            $page->setParam('userInfoAddressAddition', $userInfoAddressAddition);
            $page->setParam('userEnterprizeCoupons', $userEnterprizeCoupons);

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
            if (302 === $e->getCode()) {
                return new \Http\RedirectResponse(\App::router()->generate('cart'));
            }

            \App::logger()->error($e->getMessage(), ['cart/split']);
            $page = new \View\OrderV3\ErrorPage();
            $page->setParam('error', $e->getMessage());
            $page->setParam('step', 2);

            return new \Http\Response($page->show(), 500);
        }
    }

    /** Разбиение заказа ядром
     * @param array|null $data
     * @param null $userData
     *
     * @return \Model\OrderDelivery\Entity
     * @throws \Exception
     */
    private function getSplit(array $data = null, $userData = null) {
        $cartRepository = new \Model\Cart\Repository();

        $previousSplit = $this->session->get($this->splitSessionKey);

        if (!$this->cart->count()) throw new \Exception('Пустая корзина', 302);

        if ($data) {
            // если изменение только в информации о пользователе, то валидируем, сохраняем и не переразбиваем
            if (isset($data['action']) && $data['action'] === 'changeAddress') {
                if (!isset($data['params']) || !is_array($data['params'])) {
                    throw new \Exception('Не передан параметр "params"');
                }

                $dataToValidate = array_replace_recursive($previousSplit['user_info'], ['address' => array_intersect_key($data['params'], [
                    'street' => null,
                    'building' => null,
                    'apartment' => null,
                    'kladr_id' => null,
                ])]);

                $userInfo = $this->validateUserInfo($dataToValidate);

                if (!isset($userInfo['error'])) {
                    $newSplit = array_replace_recursive($previousSplit, ['user_info' => $userInfo]);
                    $this->session->set($this->splitSessionKey, $newSplit);
                    $this->session->set(\App::config()->order['splitAddressAdditionSessionKey'], array_intersect_key($data['params'], [
                        'kladrZipCode' => null,
                        'kladrStreet' => null,
                        'kladrStreetType' => null,
                        'kladrBuilding' => null,
                        'isSaveAddressChecked' => null,
                        'isSaveAddressDisabled' => null,
                    ]));
                } else {
                    throw new \Exception('Ошибка валидации данных пользователя');
                }

                $orderDelivery = new Entity($newSplit);
                \RepositoryManager::order()->prepareOrderDeliveryProducts($orderDelivery);
                \App::coreClientV2()->execute();

                return $orderDelivery;
            }

            // иначе подготовим данные для разбиения
            $splitData = [
                'previous_split' => $previousSplit,
                'changes'        => $this->formatChanges($data, $previousSplit)
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
            } elseif ($this->session->get('user_info_split')) {
                $splitData += ['user_info' => $this->session->get('user_info_split')];
            }

            if (!empty($this->cart->getCreditProductIds())) $splitData['payment_method_id'] = \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity::PAYMENT_CREDIT;

            try {
                switch (\App::abTest()->getOrderDeliveryType()) {
                    case 'self':
                        $splitData += ['delivery_type' => 'self'];
                        break;
                    case 'delivery':
                        $splitData += ['delivery_type' => 'standart'];
                        break;
                }
            } catch (\Exception $e) {
                \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' .  __LINE__], ['cart.split']);
            }

            // SITE-6513
            try {
                if (\App::abTest()->checkForFreeDelivery()) {
                    $splitData += ['check_for_free_delivery_discount' => true];
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
            } catch (TimeoutException $e) {
                \App::exception()->remove($e);

                // когда удалили последний товар
                if ($e->getCode() == 600) {
                    if (isset($data['action']) && isset($data['params']['ui']) && $data['action'] == 'changeProductQuantity') {
                        try {
                            $updateResultProducts = $this->cart->update([['ui' => $data['params']['ui'], 'quantity' => 0]]);
                            $cartRepository->updateCrmCart($updateResultProducts);
                        } catch(\Exception $e) {}
                    }

                    throw $e;
                }

                // некорректный email
                if ($e->getCode() == 759) {
                    throw $e;
                }
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
                    throw new \Exception('Товара нет в наличии');
                }
            }

            throw new \Exception('Отстуствуют данные по заказам');
        }

        \RepositoryManager::order()->prepareOrderDeliveryProducts($orderDelivery);

        \App::coreClientV2()->execute();

        // обновляем корзину пользователя
        if (isset($data['action']) && isset($data['params']['id']) && isset($data['params']['ui']) && $data['action'] == 'changeProductQuantity') {
            try {
                // SITE-5442
                if (isset($orderDelivery->getProductsById()[$data['params']['id']])) {
                    $updateResultProducts = $this->cart->update([['ui' => $data['params']['ui'], 'quantity' => $orderDelivery->getProductsById()[$data['params']['id']]->quantity]]);
                } else {
                    $updateResultProducts = $this->cart->update([['ui' => $data['params']['ui'], 'quantity' => 0]]);
                }

                $cartRepository->updateCrmCart($updateResultProducts);
            } catch(\Exception $e) {}
        }

        // сохраняем в сессию расчет доставки
        $this->session->set($this->splitSessionKey, $splitResponse);

        return $orderDelivery;
    }

    private function formatChanges($data, $previousSplit) {
        $data += ['action' => null];
        $changes = [];

        if (isset($data['user_info']['phone'])) {
            $data['user_info']['phone'] = preg_replace('/^\+7/', '8', $data['user_info']['phone']);
        }

        switch ($data['action']) {
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
                $changes['orders'] = [
                    $data['params']['block_name'] => $previousSplit['orders'][$data['params']['block_name']]
                ];
                // SITE-5703 TODO remove
                $true_token = strpos($data['params']['token'], '_postamat') !== false ? str_replace('_postamat', '', $data['params']['token']) : $data['params']['token'];
                $changes['orders'][$data['params']['block_name']]['delivery']['point'] = ['id' => $data['params']['id'], 'token' => $true_token];
                break;

            case 'changeDate':
                $this->logger(['action' => 'change-date']);
                $changes['orders'] = [
                    $data['params']['block_name'] => $previousSplit['orders'][$data['params']['block_name']]
                ];
                $changes['orders'][$data['params']['block_name']]['delivery']['date'] = $data['params']['date'];
                break;

            case 'changeInterval':
                $changes['orders'] = [
                    $data['params']['block_name'] => $previousSplit['orders'][$data['params']['block_name']]
                ];
                $changes['orders'][$data['params']['block_name']]['delivery']['interval'] = $data['params']['interval'];
                break;

            case 'changePaymentMethod':
                $changes['orders'] = [
                    $data['params']['block_name'] => $previousSplit['orders'][$data['params']['block_name']]
                ];
                if (!empty($data['params']['payment_method_id'])) {
                    $paymentTypeId = $data['params']['payment_method_id'];
                } else {
                    if (@$data['params']['by_credit_card'] == 'true') {
                        $paymentTypeId = PaymentMethodEntity::PAYMENT_CARD_ON_DELIVERY;
                    } else if (@$data['params']['by_online_credit']== 'true') {
                        $paymentTypeId = PaymentMethodEntity::PAYMENT_CREDIT;
                    } else if (@$data['params']['by_online'] == 'true') {
                        $paymentTypeId = PaymentMethodEntity::PAYMENT_CARD_ONLINE;
                    } else {
                        $paymentTypeId = PaymentMethodEntity::PAYMENT_CASH;
                    }
                }

                $changes['orders'][$data['params']['block_name']]['payment_method_id'] = $paymentTypeId;
                break;

            case 'changeProductQuantity':
                $changes['orders'] = [
                    $data['params']['block_name'] => $previousSplit['orders'][$data['params']['block_name']]
                ];

                $id = $data['params']['id'];
                $quantity = $data['params']['quantity'];
                $productsArray = &$changes['orders'][$data['params']['block_name']]['products'];

                array_walk($productsArray, function(&$product) use ($id, $quantity) {
                    if ($product['id'] == $id) $product['quantity'] = (int)$quantity;
                });

                break;
            case 'changeOrderComment':
                $changes['orders'] = $previousSplit['orders'];
                foreach ($changes['orders'] as &$order) {
                    $order['comment'] = $data['params']['comment'];
                }
                break;
            case 'applyDiscount':
                $changes['orders'] = [
                    $data['params']['block_name'] => $previousSplit['orders'][$data['params']['block_name']]
                ];
                $changes['orders'][$data['params']['block_name']]['discounts'][] = ['number' => $data['params']['number'], 'name' => null, 'type' => null, 'discount' => null];
                break;
            case 'deleteDiscount':
                $changes['orders'] = [
                    $data['params']['block_name'] => $previousSplit['orders'][$data['params']['block_name']]
                ];
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
            if (is_array($error) && isset($error['message'])) {
                $error = new \Model\OrderDelivery\Error($error, $orderDelivery);
            }

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
            } else if (in_array($error->code, [732])) {
                $orderDelivery->errors[] = $error;
            }
        }
    }
}