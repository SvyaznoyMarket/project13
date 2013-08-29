<?php

namespace Controller\Order;

trait PaypalTrait {
    /**
     * @param $token
     * @param $payerId
     * @throws \Exception
     * @return array
     */
    public function getPaypalCheckout($token, $payerId) {
        $result = \App::coreClientV2()->query(
            'payment/paypal-get-checkout',
            [
                'token'   => $token,
                'PayerID' => $payerId,
            ]
        );
        \App::logger()->info(['core.response' => $result], ['order', 'paypal']);

        if (empty($result['payment_amount'])) {
            throw new \Exception('Не получена сумма оплаты');
        }

        return $result;
    }
}