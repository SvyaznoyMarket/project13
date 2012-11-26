<?php

namespace Controller\Product;

class DeliveryAction {
    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     * @throws \Exception\NotFoundException
     */
    public function info(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

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

        try {
            $response = \App::coreClientV2()->query('delivery/calc', array('geo_id' => $regionId), $params);
        } catch (\Exception $e) {
            \App::logger()->error($e);
            return new \Http\JsonResponse(array('success' => false));
        }

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

    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     * @throws \Exception\NotFoundException
     */
    public function oneClick(\Http\Request $request) {
        // TODO: не доделано
        \App::logger()->debug('Exec ' . __METHOD__);

        $helper = new \View\Helper();

        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http request');
        }

        $productId = (int)$request->get('product_id');
        $productQuantity = (int)$request->get('product_quantity');
        $regionId = (int)$request->get('region_id');

        $params = array('product_list' => array(
            array(
                'id'       => $productId,
                'quantity' => $productQuantity
            ),
        ));

        $responseData = array();
        try {
            $response = \App::coreClientV2()->query('delivery/calc', array('geo_id' => $regionId), $params);

            $productData = isset($response['product_list']) ? $response['product_list'] : array();
            $productData = array_pop($productData);
            $shopData = isset($response['shop_list']) ? $response['shop_list'] : array();
            foreach ($productData['delivery_mode_list'] as $deliveryData) {
                $token = $deliveryData['token'];

                $dates = array();
                $shops = array();
                foreach ($deliveryData['date_list'] as $dateData) {
                    $date = array(
                        'name'  => $helper->humanizeDate($dateData['date']),
                        'value' => $dateData['date']
                    );

                    if('self' == $token) {
                        foreach ($dateData['shop_list'] as $dateShopData) {
                            $shop = array(
                                'id'        => $dateShopData['id'],
                                'regtime'   => $shopData[$dateShopData['id']]['working_time'], // что за описка "regtime"?
                                'address'   => $shopData[$dateShopData['id']]['address'],
                                'longitude' => $shopData[$dateShopData['id']]['coord_long'],
                                'latitude'  => $shopData[$dateShopData['id']]['coord_lat'],
                            );

                            $shops[] = $shop;
                        }

                    } else {
                        $date['intervals'] = array_key_exists('interval_list', $dateData) ? $dateData['interval_list'] : array();
                    }

                    $dates[] = $date;
                }

                $item = array(
                    'modeId' => $deliveryData['id'],
                    'name'   => $token =='standart' ? 'курьерская доставка' : $deliveryData['name'],
                    'token'  => $token,
                    'price'  => (int)$deliveryData['price'],
                    'shops'  => $shops,
                    'dates'  => $dates,
                );
                $responseData[$token] = $item;
            }
        } catch (\Exception $e) {
            \App::logger()->error($e);
            return new \Http\JsonResponse(array('success' => false, 'error' => \App::config()->debug ? $e->getMessage() : 'Ошибка'));
        }


        return new \Http\JsonResponse(array(
            'success' => true,
            'data'    => $responseData,
        ));
    }
}