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

        // получаем купоны ренее выданные пользователю
        $userCouponSeries = [];
        $userCoupons = [];
        try {
            $client->addQuery(
                'user/get-discount-coupons',
                [
                    'client_id' => 'site',
                    'token' => \App::user()->getToken()
                ],
                [],
                function ($data) use (&$userCoupons, &$userCouponSeries) {
                    if (!isset($data['detail']) || !is_array($data['detail'])) {
                        return;
                    }

                    foreach($data['detail'] as $item) {
                        $entity = new \Model\EnterprizeCoupon\DiscountCoupon\Entity($item);
                        $userCoupons[] = $entity;
                        $userCouponSeries[] = $entity->getSeries();
                    }
                }
            );
            $client->execute();
        } catch (\Exception $e) {
            \App::logger()->error($e);
            \App::exception()->remove($e);
        }

        // отфильтровываем ненужные купоны
        $enterpizeCoupons = array_filter($enterpizeCoupons, function($coupon) use ($limits, $userCouponSeries) {
            if (!$coupon instanceof \Model\EnterprizeCoupon\Entity || !array_key_exists($coupon->getToken(), $limits)) return false;

            // убераем купоны с кол-вом <= 0
            if ($limits[$coupon->getToken()] <= 0) return false;

            // убераем купоны ренее выданные пользователю
            if (in_array($coupon->getToken(), $userCouponSeries)) return false;

            return true;
        });

        $page = new \View\Enterprize\IndexPage();
        $page->setParam('enterpizeCoupons', $enterpizeCoupons);
        $page->setParam('viewParams', ['showSideBanner' => false]);

        return new \Http\Response($page->show());
    }
}