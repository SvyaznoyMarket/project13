<?php

namespace Controller\Order;

class PaypalAction {
    public function complete(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();

        $token = trim((string)$request->get('token'));
        if (!$token) {
            throw new \Exception\NotFoundException('Не передан параметр token');
        }

        $payerId = trim((string)$request->get('PayerID'));
        if (!$token) {
            throw new \Exception\NotFoundException('Не передан параметр PayerID');
        }

        $result = null;
        $client->addQuery('payment/paypal-get-info', [], [], function($data) use (&$result) {
            $result = $data;
        });
        $client->execute();

        die(var_dump($result));
    }

    public function fail(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);
    }
}