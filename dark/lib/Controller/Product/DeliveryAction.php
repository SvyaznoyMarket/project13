<?php

namespace Controller\Product;

class DeliveryAction {
    public function execute(\Http\Request $request) {
        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http request');
        }

        $productIds = $request->get('ids');
        if (!(bool)$productIds) {
            return new \Http\JsonResponse(array('success' => false));
        }

        $regionId =
            $request->get('region')
            ? (int)$request->get('region')
            : (int)$request->cookies->get(\App::config()->region['cookieName']);

        $params = array('product_list' => array());
        foreach($productIds as $productId) {
            $params['product_list'][] = array('id' => (int)$productId, 'quantity' => 1);
        }

        $response = \App::coreClientV2()->query('delivery/calc', array('geo_id' => $regionId), $params);
        if (empty($response['product_list'])) {
            \App::logger()->error('Core delivery/calc has not "product_list" data');
            return new \Http\JsonResponse(array('success' => false));
        }

        $helper = new \View\Helper();

        $data = array();
        foreach($response['product_list'] as $productId => $productData){
            if (!isset($productData['delivery_mode_list'])) continue;

            $data[$productId] = array();
            foreach($productData['delivery_mode_list'] as $delivery) {
                $date = reset($delivery['date_list']);
                $date = $date['date'];

                $data[$productId][] = array(
                    'typeId'           => $delivery['id'],
                    'date'             => $helper->humanizeDate($date),
                    'token'            => $delivery['token'],
                    'price'            => $delivery['price'],
                    'transportCompany' => \App::user()->getRegion()->getHasTransportCompany(),
                );
            }
        }

        return new \Http\JsonResponse(array('success' => true, 'data' => $data));
    }
}