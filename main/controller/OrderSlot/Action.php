<?php

namespace Controller\OrderSlot;

use Model\Order\OrderEntity;

class Action {

    /**
     * @param \Http\Request $request
     * @return \Http\Response
     */
    public function create(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if ($_SERVER['APPLICATION_ENV'] === 'local') {
            if ($request->request->get('email')) {
                return new \Http\JsonResponse(['error' => 'Ошибка при создании заявки']);
            } else {
                return new \Http\JsonResponse(['orderNumber' => 123456]);
            }
        }

        $referer = $request->headers->get('referer') ?: '/';
        $orderCreatePacketParams = [];
        $orderCreatePacketData = [];
        $orderCreatePacketResponse = null;

        try {
            $phone = $this->getValidatedPhone($request->request->get('phone'));

            if ($request->request->get('confirm') != '1') {
                throw new Exception('Подтвердите согласие с офертой');
            }

            $cartSplitResponse = $this->queryCartSplit($request->request->get('productId'));

            $orderCreatePacketParams = $this->getOrderCreatePacketParams();
            $orderCreatePacketData = $this->getOrderCreatePacketData($cartSplitResponse, $phone, $request->request->get('email'), $request->request->get('name'), $request->request->get('sender'));

            $orderCreatePacketResponse = \App::coreClientV2()->query(
                (\App::config()->newDeliveryCalc ? 'order/create-packet2' : 'order/create-packet'),
                $orderCreatePacketParams,
                $orderCreatePacketData,
                \App::config()->coreV2['hugeTimeout']
            );
        } catch (Exception $e) {
            return $request->isXmlHttpRequest() ? new \Http\JsonResponse(['error' => $e->getMessage()]) : new \Http\RedirectResponse($referer);
        } catch (\Curl\Exception $e) {
            \App::logger()->error($e->getMessage(), ['curl', 'order/create']);
            \App::exception()->remove($e);
            return $request->isXmlHttpRequest() ? new \Http\JsonResponse(['error' => 708 == $e->getCode() ? 'Товара нет в наличии' : (\App::config()->debug ? $e->getMessage() : 'Ошибка при создании заявки')]) : new \Http\RedirectResponse($referer);
        } catch (\Exception $e) {
            if (!in_array($e->getCode(), \App::config()->order['excludedError'])) {
                \App::logger('order')->error([
                    'error'   => ['code' => $e->getCode(), 'message' => $e->getMessage(), 'trace' => $e->getTraceAsString()],
                    'url'     => (\App::config()->newDeliveryCalc ? 'order/create-packet2' : 'order/create-packet') . ($orderCreatePacketParams ? ('?' . http_build_query($orderCreatePacketParams)) : ''),
                    'data'    => $orderCreatePacketData,
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

        \App::logger()->info(['action' => __METHOD__, 'core.response' => $orderCreatePacketResponse], ['order']);

        $result = [
            'orderNumber' => isset($orderCreatePacketResponse[0]['number_erp']) ? $orderCreatePacketResponse[0]['number_erp'] : null,
        ];

        if (\App::config()->googleAnalytics['enabled'] && $orderCreatePacketResponse && is_array($orderCreatePacketResponse)) {
            $orders = [];
            foreach ($orderCreatePacketResponse as $order) {
                $orders[] = new \Model\Order\Entity($order);
            }

            $result['orderAnalytics'] = \Util\Analytics::getForOrder($orders);
        }

        return $request->isXmlHttpRequest() ? new \Http\JsonResponse($result) : new \Http\RedirectResponse($referer);
    }

    private function getValidatedPhone($phone) {
        if (empty($phone)) {
            throw new Exception('Не указан телефон');
        }

        $phone = preg_replace('/^\+7/', '8', $phone);
        $phone = preg_replace('/[^\d]/', '', $phone);

        if (11 != strlen($phone)) {
            throw new Exception('Неверный номер телефона');
        }

        return $phone;
    }

    private function queryCartSplit($productId) {
        $cartSplitResponse = \App::coreClientV2()->query(
            'cart/split',
            [
                'geo_id'     => \App::user()->getRegion()->getId(),
                'request_id' => \App::$id, // SITE-4445
            ],
            [
                'cart' => [
                    'product_list' => [
                        ['id' => $productId, 'quantity' => 1]
                    ]
                ]
            ],
            3 * \App::config()->coreV2['timeout']
        );

        $cartSplitResultObject = new \Model\OrderDelivery\Entity($cartSplitResponse);
        if (!$cartSplitResultObject->orders) {
            foreach ($cartSplitResultObject->errors as $error) {
                if (708 == $error->code) {
                    throw new Exception('Товара нет в наличии');
                }
            }

            throw new \Exception('Отстуствуют данные по заказам');
        }

        return $cartSplitResponse;
    }

    private function getOrderCreatePacketParams() {
        $params = [];
        $user = \App::user();

        if ($user->getEntity() && $user->getEntity()->getToken()) {
            $params['token'] = $user->getEntity()->getToken();
        }

        $params += ['request_id' => \App::$id]; // SITE-4445

        return $params;
    }

    private function getOrderCreatePacketData($cartSplitResponse, $phone, $email, $name, $sender) {
        $data = [];

        foreach ($cartSplitResponse['orders'] as $order) {
            $data[] = array_merge(
                (new OrderEntity(array_merge($cartSplitResponse, ['order' => $order]), json_decode($sender, true)))->getOrderData(),
                [
                    'mobile' => $phone,
                    'email' => $email,
                    'first_name' => $name,
                    'type_id' => \Model\Order\Entity::TYPE_SLOT,
                ]
            );
        }

        return $data;
    }
}