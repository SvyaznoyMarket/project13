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

        $client = \App::coreClientV2();
        $user = \App::user()->getEntity();

        /** @var $enterpizeCoupons \Model\EnterprizeCoupon\Entity[] */
        $enterpizeCoupons = [];
        \App::dataStoreClient()->addQuery('enterprize/coupon-type.json', [], function($data) use (&$enterpizeCoupons, $user) {
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

        // получаем лимиты купонов
        $limits = [];
        $client->addQuery('coupon/limits', [], [], function($data) use (&$limits){
            if ((bool)$data && !empty($data['detail'])) {
                $limits = $data['detail'];
            }
        }, function(\Exception $e) {
            \App::logger()->error($e->getMessage(), ['enterprize']);
            \App::exception()->remove($e);
        });
        $client->execute();

        // убераем купоны с нулевым кол-вом
        foreach ($enterpizeCoupons as $key => $coupon) {
            if (!array_key_exists($coupon->getToken(), $limits)) continue;

            $limit = (int)$limits[$coupon->getToken()];
            if (0 === $limit) {
                unset($enterpizeCoupons[$key]);
            }
        }

        $page = new \View\Enterprize\IndexPage();
        $page->setParam('enterpizeCoupons', $enterpizeCoupons);
        $page->setParam('viewParams', ['showSideBanner' => false]);

        return new \Http\Response($page->show());
    }
}