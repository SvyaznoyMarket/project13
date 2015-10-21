<?php

namespace Controller\OrderV3;

use Session\AbTest\ABHelperTrait;
use Http\RedirectResponse;
use Http\Response;
use Model\Order\OrderEntity;
use Model\OrderDelivery\Entity;

class CreateAction extends OrderV3 {
    use ABHelperTrait;

    /**
     * @param \Http\Request $request
     * @return \Http\Response
     * @throws \Exception
     */
    public function execute(\Http\Request $request) {

        //\App::logger()->debug('Exec ' . __METHOD__);

        $coreResponse = null;   // ответ о ядра
        $ordersData = [];       // данные для отправки на ядро
        $params = [];           // параметры запроса на ядро

        $splitResult = $this->session->get($this->splitSessionKey, []);
        $orderDelivery = new Entity($splitResult);

        if ($this->user->getEntity() && $this->user->getEntity()->getToken()) {
            $params['token'] = $this->user->getEntity()->getToken();
        }

        // SITE-6071
        if ('call-center' === $this->session->get(\App::config()->order['channelSessionKey'])) {
            $params['client_id'] = 'call-center'; // call center
        }

        $params += ['request_id' => \App::$id]; // SITE-4445

        // SITE-5653
        if (self::isOrderWithCart()) {
            $userInfo = ['phone' => null, 'email' => null, 'first_name' => null, 'subscribe' => null];
            $userInfo = array_merge($userInfo, (array)$request->get('user_info'));
            $splitResult['user_info']['phone'] = $userInfo['phone'];
            $splitResult['user_info']['first_name'] = $userInfo['first_name'];
            $splitResult['user_info']['email'] = $userInfo['email'];
            $splitResult['user_info']['bonus_card_number'] = !empty($userInfo['bonus_card_number']) ? $userInfo['bonus_card_number'] : null;

            $orderForm = ['comment' => null];
            $orderForm = array_merge($orderForm, (array)$request->get('order'));
            if (is_string($orderForm['comment'])) {
                $item = null;
                foreach ($splitResult['orders'] as &$item) {
                    $item['comment'] = $orderForm['comment'];
                }
                unset($item);
            }

            $this->session->set($this->splitSessionKey, $splitResult);

            if ($userInfo['subscribe'] && $userInfo['email']) {
                $this->addSubscribeRequest($subscribeResult, $userInfo['email']);
            }
        }

        try {
            if (self::isOrderWithCart()) {
                $errors = (new \Controller\OrderV3\NewAction())->validateInput($request);
                if ($errors['errors']) {
                    \App::session()->flash($errors['errors']);

                    return new RedirectResponse(\App::router()->generate('orderV3.delivery'));
                }
            }

            if (!isset($splitResult['orders']) || empty($splitResult['orders'])) {
                throw new \Exception('Ошибка создания заказа: невозможно получить предыдущее разбиение');
            }

            // Минимальная сумма заказа для Воронежа
            if (\App::abTest()->isOrderMinSumRestriction() && \App::config()->minOrderSum > $orderDelivery->getProductsSum()) {
                return new RedirectResponse(\App::router()->generate('cart'));
            }

            foreach ($splitResult['orders'] as &$splitOrder) {
                $ordersData[] = (new OrderEntity(array_merge($splitResult, ['order' => $splitOrder]), null, '', $this->cart->getProductsById()))->getOrderData();
            }
            if (isset($splitOrder)) unset($splitOrder);

            $coreResponse = $this->client->query('order/create-packet2', $params, $ordersData, \App::config()->coreV2['hugeTimeout']);

            $this->client->execute();

        } catch (\Curl\Exception $e) {
            \App::logger()->error($e->getMessage(), ['curl', 'order/create']);
            \App::exception()->remove($e);

            if (732 === $e->getCode()) {
                \App::session()->flash([
                    ['code' => $e->getCode(), 'message' => 'Выберите точку самовывоза'],
                ]);

                return new RedirectResponse(\App::router()->generate('orderV3.delivery'));
            }

            $page = new \View\OrderV3\ErrorPage();
            $page->setParam('error', 708 === $e->getCode() ? 'Товара нет в наличии' : $e->getMessage());
            $page->setParam('step', 3);

            return new Response($page->show());
        } catch (\Exception $e) {
            if (!in_array($e->getCode(), \App::config()->order['excludedError'])) {
                \App::logger('order')->error([
                    'error'   => ['code' => $e->getCode(), 'message' => $e->getMessage(), 'detail' => $e instanceof \Curl\Exception ? $e->getContent() : null, 'trace' => $e->getTraceAsString()],
                    'url'     => 'order/create-packet2' . ((bool)$params ? ('?' . http_build_query($params)) : ''),
                    'data'    => $ordersData,
                    'server'  => array_map(function($name) use (&$request) { return $request->server->get($name); }, [
                        'HTTP_USER_AGENT', 'HTTP_X_REQUESTED_WITH', 'HTTP_REFERER', 'HTTP_COOKIE', 'REQUEST_METHOD', 'QUERY_STRING', 'REQUEST_TIME_FLOAT',
                    ]),
                ]);
            }

            throw $e;
        }

        \App::logger()->info(['action' => __METHOD__, 'core.response' => $coreResponse], ['order']);

        $this->logOrderResults($coreResponse);

        $sessionData = [];

        foreach ($coreResponse as $orderData) {
            $sessionData[$orderData['number']] = $orderData;
        }

        $this->session->set(\App::config()->order['sessionName'] ?: 'lastOrder', $sessionData);

        // удаляем предыдущее разбиение
        $this->session->remove($this->splitSessionKey);

        // устанавливаем флаг первичного просмотра страницы
        $this->session->set(self::SESSION_IS_READED_KEY, false);

        $response = new \Http\RedirectResponse(\App::router()->generate('orderV3.complete'));

        return $response;
    }

    /** Логируем ответ от ядра в случае успешного запроса
     * @param $coreResponse
     */
    private function logOrderResults($coreResponse) {
        if ((bool)$coreResponse) {
            foreach ($coreResponse as $orderData) {
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
        }
    }

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