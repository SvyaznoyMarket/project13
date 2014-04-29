<?php

namespace Controller\Order;

class SvyaznoyClubAction {

    /**
     * @param \Http\Request $request
     * @return \Http\Response
     */
    public function complete(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $response = null;
        try {
            // обязательные параметры которые должны прийти
            $requiredParams = ['OrderId', 'Status', 'CardNumber', 'Signature'];

            // определяем непришедшие параметры
            $missingParams = array_filter($requiredParams, function($param) use ($request) {
                return !(bool)$request->get($param);
            });
            if (!empty($missingParams)) {
                throw new \Exception(sprintf('Связной-клуб не передал параметры: [%s]', implode(', ', $missingParams)));
            }

            $orderId = $request->get('OrderId');
            $status = $request->get('Status');
            $discount = $request->get('Discount');
            $cardNumber = $request->get('CardNumber');
            $error = $request->get('Error');
            $signature = $request->get('Signature');

            $result = \App::coreClientV2()->query(
                'payment/svyaznoy-club',
                [],
                [
                    'OrderId'    => $orderId,
                    'Status'     => $status,
                    'Discount'   => $discount,
                    'CardNumber' => $cardNumber,
                    'Error'      => $error,
                    'Signature'  => $signature,
                ],
                \App::config()->coreV2['hugeTimeout']
            );


            if (!isset($result['order']) || !is_array($result['order'])) {
                throw new \Exception('Не получена информация о заказе');
            }

            $order = new \Model\Order\CreatedEntity($result['order']);

            $orders = [
                $order,
            ];

            \App::debug()->add('core.response', json_encode($result, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 200, \Debug\Collector::TYPE_INFO);

            $page = new \View\Order\SvyaznoyClubSuccessPage();
            $page->setParam('orders', $orders);

        } catch (\Exception $e) {
            \App::debug()->add('core.response', $e, 200, \Debug\Collector::TYPE_ERROR);
            \App::exception()->remove($e);

            $page = new \View\Order\PaymentFailPage();
        }

        return new \Http\Response($page->show());
    }
} 