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
        $controller = parent::execute($request);
        if ($controller) {
            return $controller;
        }

        \App::logger()->debug('Exec ' . __METHOD__);

        $coreResponse = null;   // ответ о ядра
        $ordersData = [];       // данные для отправки на ядро
        $createdOrders = [];    // созданные заказы
        $params = [];           // параметры запроса на ядро

        $splitResult = $this->session->get($this->splitSessionKey);

        if ($this->user->getEntity() && $this->user->getEntity()->getToken()) {
            $params['token'] = $this->user->getEntity()->getToken();
        }

        try {

            foreach ($splitResult['orders'] as &$splitOrder) {
                $ordersData[] = (new OrderEntity(array_merge($splitResult, array('order' => $splitOrder))))->getOrderData();
            }

            $coreResponse = $this->client->query((\App::config()->newDeliveryCalc ? 'order/create-packet2' : 'order/create-packet'), $params, $ordersData, \App::config()->coreV2['hugeTimeout']);

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
                    'url'     => (\App::config()->newDeliveryCalc ? 'order/create-packet2' : 'order/create-packet') . ((bool)$params ? ('?' . http_build_query($params)) : ''),
                    'data'    => $ordersData,
                    'server'  => array_map(function($name) use (&$request) { return $request->server->get($name); }, [
                        'HTTP_USER_AGENT',
                        'HTTP_X_REQUESTED_WITH',
                        'HTTP_REFERER',
                        'HTTP_COOKIE',
                        'REQUEST_METHOD',
                        'QUERY_STRING',
                        'REQUEST_TIME_FLOAT',
                    ]),
                ]);
            }

            throw $e;
        }

        \App::logger()->info(['action' => __METHOD__, 'core.response' => $coreResponse], ['order']);

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

        if ((bool)$createdOrders) {
            $this->session->set(\App::config()->order['sessionName'] ?: 'lastOrder', array_map(function(\Model\Order\CreatedEntity $createdOrder) use ($splitResult) {
                return [
                    'number'        => $createdOrder->getNumber(),
                    'number_erp'    => $createdOrder->numberErp,
                    'id'            => $createdOrder->getId(),
                    'phone'         => (string)$splitResult['user_info']['phone']
                ];
            }, $createdOrders));
        }

        // удаляем предыдущее разбиение
        $this->session->remove($this->splitSessionKey);

        // устанавливаем флаг первичного просмотра страницы
        $this->session->set(self::SESSION_IS_READED_KEY, false);

        return new \Http\RedirectResponse(\App::router()->generate('orderV3.complete'));
    }
}