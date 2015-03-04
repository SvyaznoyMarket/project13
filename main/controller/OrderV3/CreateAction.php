<?php

namespace Controller\OrderV3;

use Http\Response;
use Model\Order\OrderEntity;

class CreateAction extends OrderV3 {

    /**
     * @param \Http\Request $request
     * @return \Http\Response
     * @throws \Exception
     */
    public function execute(\Http\Request $request) {

        \App::logger()->debug('Exec ' . __METHOD__);

        $coreResponse = null;   // ответ о ядра
        $ordersData = [];       // данные для отправки на ядро
        $params = [];           // параметры запроса на ядро
        $subscribeResult = false;  // ответ на подписку

        $splitResult = $this->session->get($this->splitSessionKey);

        if ($this->user->getEntity() && $this->user->getEntity()->getToken()) {
            $params['token'] = $this->user->getEntity()->getToken();
        }

        $params += ['request_id' => \App::$id]; // SITE-4445

        try {

            if (!isset($splitResult['orders']) || empty($splitResult['orders'])) {
                throw new \Exception('Ошибка создания заказа: невозможно получить предыдущее разбиение');
            }

            foreach ($splitResult['orders'] as &$splitOrder) {
                $ordersData[] = (new OrderEntity(array_merge($splitResult, ['order' => $splitOrder])))->getOrderData();
            }
            if (isset($splitOrder)) unset($splitOrder);

            $coreResponse = $this->client->query('order/create-packet2', $params, $ordersData, \App::config()->coreV2['hugeTimeout']);

            // Если стоит галка "подписаться на рассылку"
            if ((bool)$request->cookies->get(\App::config()->subscribe['cookieName3']) && !empty($ordersData[0]['email'])) {
                $this->addSubscribeRequest($subscribeResult, $ordersData[0]['email']);
            }

            $this->client->execute();

        } catch (\Curl\Exception $e) {
            \App::logger()->error($e->getMessage(), ['curl', 'order/create']);
            \App::exception()->remove($e);

            $page = new \View\OrderV3\ErrorPage();
            $page->setParam('error', (708 == $e->getCode()) ? 'Товара нет в наличии' : ('CORE: ' . $e->getMessage()));
            $page->setParam('step', 3);
            return new Response($page->show(), 500);
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
            $sessionData[$orderData['number']] = $orderData += ['confirmed' => null, 'phone' => (string)$splitResult['user_info']['phone']];
        }

        $this->session->set(\App::config()->order['sessionName'] ?: 'lastOrder', $sessionData);

        // удаляем предыдущее разбиение
        $this->session->remove($this->splitSessionKey);

        // устанавливаем флаг первичного просмотра страницы
        $this->session->set(self::SESSION_IS_READED_KEY, false);

        $response = new \Http\RedirectResponse(\App::router()->generate('orderV3.complete'));

        // сохраняем результаты подписки в куку
        if ($subscribeResult === true) {
            $response->headers->setCookie(new \Http\Cookie(
                \App::config()->subscribe['cookieName2'],
                json_encode(['1' => true]), strtotime('+30 days' ), '/',
                \App::config()->session['cookie_domain'], false, false
            ));
        }

        return $response;
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
}