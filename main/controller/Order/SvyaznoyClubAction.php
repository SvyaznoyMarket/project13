<?php

namespace Controller\Order;

class SvyaznoyClubAction {

    /**
     * @param \Http\Request $request
     * @return \Http\Response
     */
    public function complete(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $data = [
            'OrderId'    => $request->get('OrderId'),
            'Status'     => $request->get('Status'),
            'Discount'   => $request->get('Discount'),
            'CardNumber' => $request->get('CardNumber'),
            'Error'      => $request->get('Error'),
            'Signature'  => $request->get('Signature'),
        ];

        $response = null;
        try {
            $result = \App::coreClientV2()->query('payment/svyaznoy-club', [], $data, \App::config()->coreV2['hugeTimeout']);

            if (!isset($result['detail']['order']) || !is_array($result['detail']['order'])) {
                throw new \Exception('Не получена информация о заказе');
            }

            $order = new \Model\Order\CreatedEntity($result['detail']['order']);

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

            if (503 == $e->getCode()) {
                $page = new \View\Order\SvyaznoyServiceMixFailPage();
                $page->setParam('paymentData', $data);
            }
        }

        return new \Http\Response($page->show());
    }
} 