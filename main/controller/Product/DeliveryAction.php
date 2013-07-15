<?php

namespace Controller\Product;

class DeliveryAction {
    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     * @throws \Exception\NotFoundException
     */
    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http request');
        }

        $helper = new \View\Helper();
        $user = \App::user();

        try {
            $productData = [];
            foreach ((array)$request->get('product') as $product) {
                if (!isset($product['id'])) {
                    continue;
                }

                $productData[] = [
                    'id'       => (int)$product['id'],
                    'quantity' => !empty($product['quantity']) ? (int)$product['quantity'] : 1,
                ];
            }

            if (!(bool)$productData) {
                throw new \Exception('Для расчета доставки не передены ид и количества товаров');
            }

            $regionId =
                $request->get('region')
                    ? (int)$request->get('region')
                    : $user->getRegionId();
            if (!$regionId) {
                $regionId = $user->getRegion()->getId();
            }

            $exception = null;
            $result = [];
            \App::coreClientV2()->addQuery(
                'delivery/calc',
                ['geo_id' => $regionId],
                ['product_list' => $productData],
                function($data) use (&$result) {
                    $result = array_merge([
                        'product_list'  => [],
                        'interval_list' => [],
                        'shop_list'     => [],
                        'geo_list'      => [],
                    ], $data);
                },
                function(\Exception $e) use (&$exception) {
                    $exception = $e;
                    \App::exception()->remove($e);
                }
            );
            \App::coreClientV2()->execute();
            if ($exception instanceof \Exception) {
                throw $exception;
            }

            if (!(bool)$result['product_list']) {
                throw new \Exception('При расчете доставки получен пустой список товаров');
            }

            $responseData = [
                'success'          => true,
                'product'          => [],
                'region'           => [
                    'transportCompany' => \App::user()->getRegion()->getHasTransportCompany(),
                ],
            ];

            $shopData = &$result['shop_list'];
            foreach ($result['product_list'] as $item) {
                $product = [
                    'id'    => $item['id'],
                    //'token' => $item['token'],
                    'delivery' => [],
                ];

                if (isset($item['delivery_mode_list'])) foreach ($item['delivery_mode_list'] as $deliveryItem) {
                    if (!isset($deliveryItem['date_list']) || !is_array($deliveryItem['date_list'])) continue;

                    $delivery = [
                        'id'    => $deliveryItem['id'],
                        'token' => $deliveryItem['token'],
                        'price' => $deliveryItem['price'],
                        'shop'  => [],
                    ];

                    $firstDate = reset($deliveryItem['date_list']);
                    $firstDate = isset($firstDate['date']) ? new \DateTime($firstDate['date']) : null;
                    $delivery['date'] = $firstDate ?
                        ['value' => $firstDate->format('d.m.Y'), 'name' => $helper->humanizeDate($firstDate)]
                        : [];
                    $delivery['days'] = $helper->getDaysDiff($firstDate);

                    $day = 0;
                    foreach ($deliveryItem['date_list'] as $dateItem) {
                        $day++;
                        if ($day > 7) continue;

                        if (in_array($delivery['token'], ['self', 'now'])) {
                            foreach ($dateItem['shop_list'] as $shopItem) {
                                $address = $shopData[$shopItem['id']]['address'];
                                if (($regionId != $shopData[$shopItem['id']]['geo_id']) && isset($regionData[$shopData[$shopItem['id']]['geo_id']]['name'])) {
                                    $address = $regionData[$shopData[$shopItem['id']]['geo_id']]['name'] . ', ' . $address;
                                }

                                $shop = [
                                    'id'        => (int)$shopItem['id'],
                                    'name'      => $shopData[$shopItem['id']]['name'],
                                    'regime'    => $shopData[$shopItem['id']]['working_time'], // что за описка "regtime"?
                                    'latitude'  => $shopData[$shopItem['id']]['coord_lat'],
                                    'longitude' => $shopData[$shopItem['id']]['coord_long'],
                                ];

                                if (!in_array($shop, $delivery['shop'])) {
                                    $delivery['shop'][] = $shop;
                                }
                            }
                        }
                    }

                    $product['delivery'][] = $delivery;
                }

                $responseData['product'][] = $product;
            }
        } catch(\Exception $e) {
            $responseData = [
                'success' => false,
                'error'   => [
                    'code' => $e->getCode(),
                    'code' => 'Не удалось расчитать доставку' . (\App::config()->debug ? ('' . $e->getMessage()) : ''),
                ],
            ];
        }

        return new \Http\JsonResponse($responseData);
    }
}