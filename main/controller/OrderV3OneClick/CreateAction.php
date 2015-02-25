<?php

namespace Controller\OrderV3OneClick;

use Http\Response;
use Model\Order\OrderEntity;
use Session\ProductPageSenders;
use Session\ProductPageSendersForMarketplace;

class CreateAction {
    public function __construct() {
        $this->session = \App::session();
        $this->splitSessionKey = \App::config()->order['oneClickSplitSessionKey'];
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

        $referer = $request->headers->get('referer') ?: '/';

        $coreResponse = null;   // ответ о ядра
        $ordersData = [];       // данные для отправки на ядро
        /** @var \Model\Order\CreatedEntity[] $createdOrders */
        $createdOrders = [];    // созданные заказы
        $params = [];           // параметры запроса на ядро

        $userInfo = (array)$request->get('user_info');

        $splitResult = $this->session->get($this->splitSessionKey);

        if ($this->user->getEntity() && $this->user->getEntity()->getToken()) {
            $params['token'] = $this->user->getEntity()->getToken();
        }

        $params += ['request_id' => \App::$id]; // SITE-4445

        try {
            if (empty($userInfo['mobile'])) {
                throw new \Exception('Не указан телефон');
            }
            $userInfo['mobile'] = preg_replace('/^\+7/', '8', $userInfo['mobile']);
            $userInfo['mobile'] = preg_replace('/[^\d]/', '', $userInfo['mobile']);

            if (11 != strlen($userInfo['mobile'])) {
                throw new \Exception('Неверный номер телефона');
            }

            if (empty($splitResult['orders'])) {
                throw new \Exception('Ошибка оформления');
            }

            foreach ($splitResult['orders'] as &$splitOrder) {
                $orderItem = array_merge($userInfo, (new OrderEntity(array_merge($splitResult, ['order' => $splitOrder]), json_decode($request->request->get('sender'), true), (string)$request->request->get('sender2')))->getOrderData());
                $orderItem['type_id'] = \Model\Order\Entity::TYPE_1CLICK;

                $ordersData[] = $orderItem;
            }
            if (isset($splitOrder)) unset($splitOrder);

            $coreResponse = $this->client->query(
                (\App::config()->newDeliveryCalc ? 'order/create-packet2' : 'order/create-packet'),
                $params,
                $ordersData,
                \App::config()->coreV2['hugeTimeout']
            );
            //$coreResponse = \App::dataStoreClient()->query('/fixture/v2-create-packet.json');
        } catch (\Curl\Exception $e) {
            \App::logger()->error($e->getMessage(), ['curl', 'order/create']);
            \App::exception()->remove($e);

            $message = (708 == $e->getCode()) ? 'Товара нет в наличии' : $e->getMessage();

            $result['error'] = ['message' => $message];
            $result['errorContent'] = \App::closureTemplating()->render('order-v3/__error', ['error' => $message]);

            return $request->isXmlHttpRequest() ? new \Http\JsonResponse(['result' => $result], 500) : new \Http\RedirectResponse($referer);
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

            $result['error'] = [$message];
            $result['errorContent'] = \App::closureTemplating()->render('order-v3/__error', ['error' => $message]);

            return $request->isXmlHttpRequest() ? new \Http\JsonResponse(['result' => $result], 500) : new \Http\RedirectResponse($referer);
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

            ProductPageSenders::clean();
            ProductPageSendersForMarketplace::clean();
        }

        // удаляем предыдущее разбиение
        $this->session->remove($this->splitSessionKey);

        // устанавливаем флаг первичного просмотра страницы
        //$this->session->set(self::SESSION_IS_READED_KEY, false);

        $result = [
            'page' => \App::closureTemplating()->render('order-v3-1click/__complete', [
                'orders' => $createdOrders,
            ]),
            'orders' => [
                [
                    'id' => $createdOrders[0]->getNumber(),
                    'products' => array_map(function($product) {
                        return ['id' => $product['id'], 'price' => $product['price'], 'quantity' => $product['quantity']];
                    }, reset($splitResult['orders'])['products']),
                ],
            ],
            'lastPartner' => \App::partner()->getName(),
        ];

        return $request->isXmlHttpRequest() ? new \Http\JsonResponse(['result' => $result]) : new \Http\RedirectResponse($referer);
    }
}