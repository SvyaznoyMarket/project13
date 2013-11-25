<?php

namespace Controller\Enterprize;

class Action {
    /**
     * @param \Http\Request $request
     * @return \Http\Response
     * @throws \Exception
     */
    public function index(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::dataStoreClient();

        /** @var $enterpizeCoupons \Model\EnterprizeCoupon\Entity[] */
        $enterpizeCoupons = [];
        $client->addQuery('enterprize/coupon-type.json', [], function($data) use (&$enterpizeCoupons) {
            foreach ((array)$data as $item) {
                if (empty($item['token'])) continue;
                $enterpizeCoupons[] = new \Model\EnterprizeCoupon\Entity($item);
            }
        });
        $client->execute();

        $page = new \View\Enterprize\IndexPage();
        $page->setParam('enterpizeCoupons', $enterpizeCoupons);

        return new \Http\Response($page->show());
    }

    public function create(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();


        $responseData = [
            'success' => false,
        ];

        try {
            $form = array_merge([
                'first_name'        => null,
                'phonenumber'       => null,
                'email'             => null,
                'sclub_card_number' => null,
                'agreed'            => null,
                'coupon_type'       => null,
            ], (array)$request->get('coupon_request'));

            

            // TODO: запрос в ядро на создание купона
        } catch (\Exception $e) {
            $responseData['success'] = false;
            $responseData['error'] = ['code' => $e->getCode(), 'message' => $e->getMessage()];
        }

        return new \Http\JsonResponse($responseData);
    }
}