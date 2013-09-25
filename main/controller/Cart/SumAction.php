<?php

namespace Controller\Cart;

class SumAction {
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

        $client = \App::coreClientV2();

        $productInCartData = (array)$request->get('product');

        $result = [];
        $client->addQuery(
            'cart/get-price',
            ['geo_id' => \App::user()->getRegion()->getId()],
            [
                'product_list'  => $productInCartData,
                'service_list'  => [],
                'warranty_list' => [],
            ],
            function ($data) use (&$result) {
                $result = $data;
            },
            function(\Exception $e) use (&$result) {
                \App::exception()->remove($e);
                $result = $e;
            }
        );
        $client->execute();

        if ($result instanceof \Exception) {
            $responseData = [
                'success' => false,
                'error'   => 'Не удалось расчитать цены товаров' . (\App::config()->debug ? sprintf('. %s', $result) : ''),
            ];
        } else {
            $result = array_merge([
                'sum' => 0,
            ], (array)$result);

            $responseData = [
                'success' => true,
                'sum'     => $result['sum'],
            ];
        }

        return new \Http\JsonResponse($responseData);
    }
}