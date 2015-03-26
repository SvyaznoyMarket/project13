<?php

namespace Controller\Order;

class PaypalAction {
    public function complete(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();

        try {
            $token = trim((string)$request->get('token'));
            $payerId = trim((string)$request->get('PayerID'));

            $result = $client->query(
                'payment/paypal-get-info',
                [
                    'token'   => $token,
                    'PayerID' => $payerId,
                ],
                [],
                \App::config()->coreV2['hugeTimeout']
            );
            \App::logger()->info(['action' => __METHOD__, 'core.response' => $result], ['order', 'paypal']);

            if (!isset($result['order']) || !is_array($result['order'])) {
                throw new \Exception('Не получена информация о заказе');
            }

            \App::debug()->add('core.response', json_encode($result, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 200, \Debug\Collector::TYPE_INFO);

            return new \Http\RedirectResponse(\App::router()->generate('orderV3.complete', ['refresh' => 1]));

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
        //\App::logger()->debug('Exec ' . __METHOD__);

        $page = new \View\Order\PaymentFailPage();

        return new \Http\Response($page->show());
    }
}