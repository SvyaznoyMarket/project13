<?php

namespace Controller\Enterprize;

class IndexAction {
    /**
     * @param \Http\Request $request
     * @return \Http\Response
     * @throws \Exception
     */
    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (!\App::config()->enterprize['enabled']) {
            throw new \Exception\NotFoundException();
        }

        $client = \App::dataStoreClient();
        $user = \App::user()->getEntity();

        /** @var $enterpizeCoupons \Model\EnterprizeCoupon\Entity[] */
        $enterpizeCoupons = [];
        $client->addQuery('enterprize/coupon-type.json', [], function($data) use (&$enterpizeCoupons, $user) {
            foreach ((array)$data as $item) {
                if (empty($item['token'])) continue;

                $coupon = new \Model\EnterprizeCoupon\Entity($item);

                if (
                    ($coupon->isForMember() && $user && $user->isEnterprizeMember())
                    || ($coupon->isForNotMember() && (!$user || !$user->isEnterprizeMember()))
                ) {
                    $enterpizeCoupons[] = $coupon;
                }
            }
        });
        $client->execute();

        $page = new \View\Enterprize\IndexPage();
        $page->setParam('enterpizeCoupons', $enterpizeCoupons);

        return new \Http\Response($page->show());
    }
}