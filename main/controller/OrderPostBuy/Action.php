<?php

namespace Controller\OrderPostBuy;

use Model\Order\OrderEntity;

class Action {

    /**
     * @param \Http\Request $request
     * @return \Http\Response
     */
    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if ($_SERVER['APPLICATION_ENV'] === 'local') {
            return new \Http\JsonResponse(['orderNumber' => 123456]);
        }

        $referer = $request->headers->get('referer') ?: '/';
        $user = \App::user();
        $orderCreatePacketResponse = null;   // ответ о ядра
        $orders = [];       // данные для отправки на ядро
        $params = [];

        try {
            $phone = $this->getPhone($request);

            if ($request->get('confirm') != '1') {
                throw new Exception('Подтвердите согласие с офертой');
            }

            $cartSplitResponse = \App::coreClientV2()->query(
                'cart/split',
                [
                    'geo_id'     => \App::user()->getRegion()->getId(),
                    'request_id' => \App::$id, // SITE-4445
                ],
                [
                    'cart' => [
                        'product_list' => [
                            ['id' => $request->get('productId'), 'quantity' => 1]
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

                throw new Exception('Отстуствуют данные по заказам');
            }

            foreach ($cartSplitResponse['orders'] as $order) {
                $orders[] = array_merge(
                    [
                        'mobile' => $phone,
                        'email' => $request->get('email'),
                        'first_name' => $request->get('name')
                    ],
                    (new OrderEntity(array_merge($cartSplitResponse, ['order' => $order]), json_decode($request->request->get('sender'), true)))->getOrderData(),
                    ['type_id' => \Model\Order\Entity::TYPE_POST_BUY]
                );
            }

            if ($user->getEntity() && $user->getEntity()->getToken()) {
                $params['token'] = $user->getEntity()->getToken();
            }

            $params += ['request_id' => \App::$id]; // SITE-4445

            $orderCreatePacketResponse = \App::coreClientV2()->query(
                (\App::config()->newDeliveryCalc ? 'order/create-packet2' : 'order/create-packet'),
                $params,
                $orders,
                \App::config()->coreV2['hugeTimeout']
            );
        } catch (Exception $e) {
            return $request->isXmlHttpRequest() ? new \Http\JsonResponse(['error' => $e->getMessage()]) : new \Http\RedirectResponse($referer);
        } catch (\Curl\Exception $e) {
            \App::logger()->error($e->getMessage(), ['curl', 'order/create']);
            \App::exception()->remove($e);
            return $request->isXmlHttpRequest() ? new \Http\JsonResponse(['error' => 708 == $e->getCode() ? 'Товара нет в наличии' : (\App::config()->debug ? $e->getMessage() : 'Ошибка при создании заявки')]) : new \Http\RedirectResponse($referer);
        } catch (\RuntimeException $e) {
            \App::logger()->error($e->getMessage(), ['curl', 'order/create']);
            \App::exception()->remove($e);
            return $request->isXmlHttpRequest() ? new \Http\JsonResponse(['error' => \App::config()->debug ? $e->getMessage() : 'Ошибка при создании заявки']) : new \Http\RedirectResponse($referer);
        } catch (\Exception $e) {
            if (!in_array($e->getCode(), \App::config()->order['excludedError'])) {
                \App::logger('order')->error([
                    'error'   => ['code' => $e->getCode(), 'message' => $e->getMessage(), 'detail' => $e instanceof \Curl\Exception ? $e->getContent() : null, 'trace' => $e->getTraceAsString()],
                    'url'     => (\App::config()->newDeliveryCalc ? 'order/create-packet2' : 'order/create-packet') . ((bool)$params ? ('?' . http_build_query($params)) : ''),
                    'data'    => $orders,
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

            return $request->isXmlHttpRequest() ? new \Http\JsonResponse(['error' => \App::config()->debug ? $e->getMessage() : 'Ошибка при создании заявки']) : new \Http\RedirectResponse($referer);
        }

        \App::logger()->info(['action' => __METHOD__, 'core.response' => $orderCreatePacketResponse], ['order']);

        return $request->isXmlHttpRequest() ? new \Http\JsonResponse(['orderNumber' => isset($orderCreatePacketResponse[0]['number_erp']) ? $orderCreatePacketResponse[0]['number_erp'] : null]) : new \Http\RedirectResponse($referer);
    }

    private function getPhone(\Http\Request $request) {
        $phone = $request->get('phone');

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
}