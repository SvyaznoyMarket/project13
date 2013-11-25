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
}