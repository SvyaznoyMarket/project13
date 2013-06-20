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
            return new \Http\JsonResponse(['success' => false]);
        }

        $regionId =
            $request->get('region')
            ? (int)$request->get('region')
            : $user->getRegionId();

        if (!$regionId) {
            $regionId = $user->getRegion()->getId();
        }

        $params = ['product_list' => []];
        foreach($productIds as $productId) {
            $params['product_list'][] = ['id' => (int)$productId, 'quantity' => 1];
        }

        try {
            $response = [];
            \App::coreClientV2()->addQuery('delivery/calc', ['geo_id' => $regionId], $params, function ($data) use (&$response) {
                $response = $data;
            });
            \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['default'], \App::config()->coreV2['retryCount']);

        } catch (\Exception $e) {
            \App::logger()->error($e);
            \App::exception()->remove($e);
            return new \Http\JsonResponse(['success' => false]);
        }

        if (empty($response['product_list'])) {
            $e = new \Exception('Core delivery/calc has no "product_list" data');
            \App::exception()->add($e);
            \App::logger()->error($e);

            return new \Http\JsonResponse(['success' => false]);
        }

        $helper = new \View\Helper();
        $data = [];
        $shopData = isset($response['shop_list']) ? $response['shop_list'] : [];
        //print "<pre>"; var_dump($response['product_list']); exit;
        foreach($response['product_list'] as $productId => $productData){
            if (!isset($productData['delivery_mode_list'])) continue;

            $data[$productId] = [];
            foreach($productData['delivery_mode_list'] as $delivery) {
                $token = $delivery['token'];
                if ($token == 'now') $token = 'self';
                $date = reset($delivery['date_list']);
                $day = 0;
                $dateOriginal = $date['date'];
                $shops = [];
                if (!is_object($dateOriginal) || !is_a($dateOriginal, 'DateTime')) {
                    $dateObj = new \DateTime($dateOriginal);
                } else $dateObj = null;

                foreach ($delivery['date_list'] as $dateData) {
                    $day++;
                    if ($day > 7) continue;

                    if(in_array($token, ['self', 'now'])) {
                        foreach ($dateData['shop_list'] as $dateShopData) {
                            $address = $shopData[$dateShopData['id']]['address'];
                            if (($regionId != $shopData[$dateShopData['id']]['geo_id']) && isset($regionData[$shopData[$dateShopData['id']]['geo_id']]['name'])) {
                                $address = $regionData[$shopData[$dateShopData['id']]['geo_id']]['name'] . ', ' . $address;
                            }

                            $shop = [
                                'id'        => (int)$dateShopData['id'],
                                'regtime'   => $shopData[$dateShopData['id']]['working_time'], // что за описка "regtime"?
                                'address'   => $address,
                                'latitude'  => $shopData[$dateShopData['id']]['coord_lat'],
                                'longitude' => $shopData[$dateShopData['id']]['coord_long'],
                            ];

                            if (!in_array($shop, $shops)) {
                                $shops[] = $shop;
                            }
                        }
                    }
                }

                $data[$productId][] = [
                    'typeId'           => $delivery['id'],
                    'date'             => $helper->humanizeDate($dateOriginal),
                    'token'            => $token,
                    'price'            => $delivery['price'],
                    'transportCompany' => \App::user()->getRegion()->getHasTransportCompany(),
                    'days'             => $helper->getDaysDiff($dateOriginal),
                    'origin_date'      => $dateObj?$dateObj->format('d.m.Y'):$dateObj,
                    'shops'            => $shops,
                ];

            }
        }

        return new \Http\JsonResponse(['success' => true, 'data' => $data]);
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

        $params = [
            'product_list' => [
                [
                    'id'       => $productId,
                    'quantity' => $productQuantity
                ],
            ]
        ];

        $responseData = [];
        try {
            $response = [];
            \App::coreClientV2()->addQuery('delivery/calc', ['geo_id' => $regionId, 'days_num' => 7], $params, function ($data) use (&$response) {
                if ((bool)$data) {
                    $response = $data;
                    if (!isset($response['product_list'])) $response['product_list'] = [];
                    if (!isset($response['geo_list'])) $response['geo_list'] = [];
                    if (!isset($response['shop_list'])) $response['shop_list'] = [];
                }
            });
            \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['forever'], \App::config()->coreV2['retryCount']);

            $productData = $response['product_list'];
            $productData = array_pop($productData);
            $shopData = $response['shop_list'];
            $regionData = [];
            foreach ($response['geo_list'] as $regionItem) {
                if (!isset($regionItem['id'])) continue;
                $regionData[$regionItem['id']] = $regionItem;
            }

            foreach ($productData['delivery_mode_list'] as $deliveryData) {
                $token = $deliveryData['token'];
                if ($token == 'now') $token = 'self';

                $dates = [];
                $shops = [];
                $day = 0;
                foreach ($deliveryData['date_list'] as $dateData) {
                    $day++;
                    if ($day > 7) continue;

                    $date = [
                        'name'  => $helper->humanizeDate($dateData['date']),
                        'value' => $dateData['date']
                    ];

                    if(in_array($token, ['self', 'now'])) {
                        $date['shopIds'] = [];
                        foreach ($dateData['shop_list'] as $dateShopData) {
                            $address = $shopData[$dateShopData['id']]['address'];
                            if (($regionId != $shopData[$dateShopData['id']]['geo_id']) && isset($regionData[$shopData[$dateShopData['id']]['geo_id']]['name'])) {
                                $address = $regionData[$shopData[$dateShopData['id']]['geo_id']]['name'] . ', ' . $address;
                            }

                            $shop = [
                                'id'        => (int)$dateShopData['id'],
                                'regtime'   => $shopData[$dateShopData['id']]['working_time'],
                                'address'   => $address,
                                'latitude'  => $shopData[$dateShopData['id']]['coord_lat'],
                                'longitude' => $shopData[$dateShopData['id']]['coord_long'],
                            ];

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
                        $date['intervals'] = array_key_exists('interval_list', $dateData) ? $dateData['interval_list'] : [];
                    }

                    $dates[] = $date;
                }

                $item = [
                    'modeId' => $deliveryData['id'],
                    'name'   => $token =='standart' ? 'курьерская доставка' : $deliveryData['name'],
                    'token'  => $token,
                    'price'  => (int)$deliveryData['price'],
                    'shops'  => $shops,
                    'dates'  => $dates,
                ];
                $responseData[$token] = $item;
            }
        } catch (\Exception $e) {
            \App::exception()->remove($e);
            \App::logger()->error($e);

            $responseData['data'] = [];

            return new \Http\JsonResponse([
                'success'     => false,
                'error'       => \App::config()->debug ? $e->getMessage() : 'Ошибка',
                'currentDate' => date('Y-m-d'),
            ]);
        }


        return new \Http\JsonResponse([
            'success'     => true,
            'data'        => $responseData,
            'currentDate' => date('Y-m-d'),
        ]);
    }
}