<?php

namespace Controller\Order;

class PaypalAction {
    public function complete(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();

        try {
            $token = trim((string)$request->get('token'));
            if (!$token) {
                throw new \Exception\NotFoundException('Не передан параметр token');
            }

            $payerId = trim((string)$request->get('PayerID'));
            if (!$token) {
                throw new \Exception\NotFoundException('Не передан параметр PayerID');
            }

            $result = $client->query(
                'payment/paypal-get-info',
                [
                    'token'   => $token,
                    'PayerID' => $payerId,
                ],
                []
            );
            \App::logger()->info(['action' => __METHOD__, 'core.response' => $result], ['order', 'paypal']);

            if (!isset($result['order']) || !is_array($result['order'])) {
                throw new \Exception('Не получена информация о заказе');
            }

            $order = new \Model\Order\CreatedEntity($result['order']);

            $orders = [
                $order,
            ];

            \App::debug()->add('core.response', json_encode($result, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 200, \Debug\Collector::TYPE_INFO);

            $page = new \View\Order\PaypalSuccessPage();
            $page->setParam('orders', $orders);
        } catch(\Exception $e) {
            \App::debug()->add('core.response', $e, 200, \Debug\Collector::TYPE_ERROR);

            $page = new \View\Order\PaymentFailPage();
        }

        return new \Http\Response($page->show());
    }

    /**
     * @param \Http\Request $request
     * @return \Http\Response
     */
    public function fail(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $page = new \View\Order\PaymentFailPage();

        return new \Http\Response($page->show());
    }
}