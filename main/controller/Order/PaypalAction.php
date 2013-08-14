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

            $result = null;
            $client->addQuery(
                'payment/paypal-get-info',
                [
                    'token'   => $token,
                    'PayerID' => $payerId,
                ],
                [],
                function($data) use (&$result) { $result = $data; },
                function(\Exception $e) use (&$result) { $result = $e; }
            );
            $client->execute();

            if ($result instanceof \Exception) {
                throw $result;
            }

            \App::debug()->add('core.response', $result, 200, \Debug\Collector::TYPE_INFO);

            $page = new \View\Order\PaymentSuccessPage();
        } catch(\Exception $e) {
            \App::debug()->add('core.response', $e, 200, \Debug\Collector::TYPE_ERROR);

            $page = new \View\Order\PaymentFailPage();
        }

        return new \Http\Response($page->show());
    }

    public function fail(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);
    }
}