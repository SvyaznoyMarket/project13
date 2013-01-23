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

        $user = \App::user();

        $productIds = $request->get('ids');
        if (!(bool)$productIds) {
            return new \Http\JsonResponse(array('success' => false));
        }

        $regionId =
            $request->get('region')
            ? (int)$request->get('region')
            : $user->getRegionId();

        if (!$regionId) {
            $regionId = $user->getRegion()->getId();
        }

        $params = array('product_list' => array());
        foreach($productIds as $productId) {
            $params['product_list'][] = array('id' => (int)$productId, 'quantity' => 1);
        }

        try {
            $response = \App::coreClientV2()->query('delivery/calc', array('geo_id' => $regionId), $params);
        } catch (\Exception $e) {
            \App::logger()->error($e);
            \App::exception()->remove($e);
            return new \Http\JsonResponse(array('success' => false));
        }

        if (empty($response['product_list'])) {
            $e = new \Exception('Core delivery/calc has no "product_list" data');
            \App::exception()->add($e);
            \App::logger()->error($e);

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
        \App::logger()->debug('Exec ' . __METHOD__);

        $helper = new \View\Helper();

        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http request');
        }

        $productId = (int)$request->get('product_id');
        $productQuantity = (int)$request->get('product_quantity');
        $regionId = (int)$request->get('region_id');
        if (!$regionId) {
            $regionId = \App::user()->getRegion()->getId();
        }

        $params = array('product_list' => array(
            array(
                'id'       => $productId,
                'quantity' => $productQuantity
            ),
        ));

        $responseData = array();
        try {
            $response = \App::coreClientV2()->query('delivery/calc', array('geo_id' => $regionId, 'days_num' => 7), $params);

            $productData = isset($response['product_list']) ? $response['product_list'] : array();
            $productData = array_pop($productData);
            $shopData = isset($response['shop_list']) ? $response['shop_list'] : array();
            $regionData = isset($response['geo_list']) ? $response['geo_list'] : array();

            foreach ($productData['delivery_mode_list'] as $deliveryData) {
                $token = $deliveryData['token'];

                $dates = array();
                $shops = array();
                $day = 0;
                foreach ($deliveryData['date_list'] as $dateData) {
                    $day++;
                    if ($day > 7) continue;

                    $date = array(
                        'name'  => $helper->humanizeDate($dateData['date']),
                        'value' => $dateData['date']
                    );

                    if('self' == $token) {
                        $date['shopIds'] = array();
                        foreach ($dateData['shop_list'] as $dateShopData) {
                            $address = $shopData[$dateShopData['id']]['address'];
                            if (($regionId != $shopData[$dateShopData['id']]['geo_id']) && isset($regionData[$shopData[$dateShopData['id']]['geo_id']]['name'])) {
                                $address = $regionData[$shopData[$dateShopData['id']]['geo_id']]['name'] . ', ' . $address;
                            }

                            $shop = array(
                                'id'        => (int)$dateShopData['id'],
                                'regtime'   => $shopData[$dateShopData['id']]['working_time'], // что за описка "regtime"?
                                'address'   => $address,
                                'latitude'  => $shopData[$dateShopData['id']]['coord_lat'],
                                'longitude' => $shopData[$dateShopData['id']]['coord_long'],
                            );

                            if (!in_array($shop, $shops)) {
                                $shops[] = $shop;
                            }

                            foreach ($response['interval_list'] as $interval) {
                                if (in_array($interval['id'], $dateShopData['interval_list'])) {
                                    $date['shopIds'][] = (int)$dateShopData['id'];
                                }
                            }
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
            \App::exception()->remove($e);
            \App::logger()->error($e);

            $responseData['data'] = array();

            return new \Http\JsonResponse(array(
                'success'     => false,
                'error'       => \App::config()->debug ? $e->getMessage() : 'Ошибка',
                'currentDate' => date('Y-m-d'),
            ));
        }


        return new \Http\JsonResponse(array(
            'success'     => true,
            'data'        => $responseData,
            'currentDate' => date('Y-m-d'),
        ));
    }
}