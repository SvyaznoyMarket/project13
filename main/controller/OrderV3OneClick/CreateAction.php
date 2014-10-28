<?php

namespace Controller\OrderV3OneClick;

use Http\Response;
use Model\Order\OrderEntity;

class CreateAction {
    public function __construct() {
        $this->session = \App::session();
        $this->splitSessionKey = \App::config()->order['splitSessionKey'] . '-1click';
        $this->client = \App::coreClientV2();
        $this->user = \App::user();
    }

    /**
     * @param \Http\Request $request
     * @return \Http\Response
     * @throws \Exception
     */
    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $coreResponse = null;   // ответ о ядра
        $ordersData = [];       // данные для отправки на ядро
        /** @var \Model\Order\CreatedEntity[] $createdOrders */
        $createdOrders = [];    // созданные заказы
        $params = [];           // параметры запроса на ядро

        $splitResult = $this->session->get($this->splitSessionKey);

        if ($this->user->getEntity() && $this->user->getEntity()->getToken()) {
            $params['token'] = $this->user->getEntity()->getToken();
        }

        $params += ['request_id' => \App::$id]; // SITE-4445

        try {
            if (empty($splitResult['orders'])) {
                throw new \Exception('Ошибка оформления');
            }

            foreach ($splitResult['orders'] as &$splitOrder) {
                $ordersData[] = (new OrderEntity(array_merge($splitResult, ['order' => $splitOrder])))->getOrderData();
            }
            if (isset($splitOrder)) unset($splitOrder);

            /*
            $coreResponse = $this->client->query(
                (\App::config()->newDeliveryCalc ? 'order/create-packet2' : 'order/create-packet'),
                $params,
                $ordersData,
                \App::config()->coreV2['hugeTimeout']
            );
            */
            $coreResponse = json_decode('[{"confirmed":"true","id":"8473373","access_token":"300F0632-7253-4472-8C68-364A6FC61A2A","is_partner":0,"partner_ui":null,"number":"TE411112","number_erp":"COTE-411112","user_id":null,"price":10280,"pay_sum":10280,"payment_invoice_id":null,"payment_url":null}]', true);

        } catch (\Curl\Exception $e) {
            \App::logger()->error($e->getMessage(), ['curl', 'order/create']);
            \App::exception()->remove($e);

            $message = (708 == $e->getCode()) ? 'Товара нет в наличии' : $e->getMessage();

            $result['error'] = ['message' => $message];
            $result['errorContent'] = \App::closureTemplating()->render('order-v3/__error', ['error' => $message]);

            return new \Http\JsonResponse(['result' => $result], 500);
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

            $message = $e->getMessage();

            $result['error'] = ['message' => $message];
            $result['errorContent'] = \App::closureTemplating()->render('order-v3/__error', ['error' => $message]);

            return new \Http\JsonResponse(['result' => $result], 500);
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
        //$this->session->set(self::SESSION_IS_READED_KEY, false);

        $result = [
            'page' => \App::closureTemplating()->render('order-v3-1click/__complete', [
                'orders' => $createdOrders,
            ]),
        ];

        return new \Http\JsonResponse(['result' => $result]);
    }
}