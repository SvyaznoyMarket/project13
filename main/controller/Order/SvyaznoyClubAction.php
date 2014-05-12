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
            $data = [
                'OrderId'    => $request->get('OrderId'),
                'Status'     => $request->get('Status'),
                'Discount'   => $request->get('Discount'),
                'CardNumber' => $request->get('CardNumber'),
                'Error'      => $request->get('Error'),
                'Signature'  => $request->get('Signature'),
            ];

//            $result = \App::coreClientV2()->query('payment/svyaznoy-club', [], $data, \App::config()->coreV2['hugeTimeout']);

            $result = null;
            \App::coreClientV2()->addQuery('payment/svyaznoy-club', [], $data,
                function($data) use (&$result) {
                    if ($data) {
                        $result = $data;
                    }
                }, function(\Exception $e) {\App::exception()->remove($e);}
            );
            \App::coreClientV2()->execute(\App::config()->coreV2['hugeTimeout'], 1);

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