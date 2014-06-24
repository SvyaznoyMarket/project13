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
        $session = \App::session();
        $sessionName = \App::config()->enterprize['formDataSessionKey'];

        $data = $session->get($sessionName, []);
        $enterprizeToken = isset($data['enterprizeToken']) ? $data['enterprizeToken'] : null;

        // SITE-3931, SITE-3934
        $isCouponSent = (bool)$request->cookies->get(\App::config()->enterprize['cookieName']);

        /**
         * @var $enterpizeCoupon    \Model\EnterprizeCoupon\Entity
         * @var $enterpizeCoupons   \Model\EnterprizeCoupon\Entity[]
         */
        $enterpizeCoupon = null;
        $enterpizeCoupons = [];
        \App::dataStoreClient()->addQuery(
            'enterprize/coupon-type.json',
            [],
            function($data) use (&$enterpizeCoupons, &$enterpizeCoupon, $enterprizeToken, $isCouponSent, $user) {
                foreach ((array)$data as $item) {
                    if (empty($item['token'])) continue;

                    $coupon = new \Model\EnterprizeCoupon\Entity($item);

                    if (
                        ($coupon->isForMember() && $user && $user->isEnterprizeMember())
                        || ($coupon->isForNotMember() && (!$user || !$user->isEnterprizeMember()))
                    ) {
                        $enterpizeCoupons[] = $coupon;
                    }

                    // если купон уже отослан, то пытаемся его получить из общего списка
                    if ($isCouponSent && (bool)$enterprizeToken && $enterprizeToken == $coupon->getToken()) {
                        $enterpizeCoupon = $coupon;
                    }
                }
            }
        );

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

        // получаем купоны ренее выданные пользователю
        $userCouponSeries = [];
        $userCoupons = [];
        if ( \App::user()->getToken() ) {
            $client->addQuery('user/get-discount-coupons', ['token' => \App::user()->getToken()], [],
                function ($data) use (&$userCoupons, &$userCouponSeries) {
                    if (!isset($data['detail']) || !is_array($data['detail'])) {
                        return;
                    }

                    foreach ($data['detail'] as $item) {
                        $entity = new \Model\EnterprizeCoupon\DiscountCoupon\Entity($item);
                        $userCoupons[] = $entity;
                        $userCouponSeries[] = $entity->getSeries();
                    }
                },
                function (\Exception $e) {
                    \App::logger()->error($e->getMessage(), ['enterprize']);
                    \App::exception()->remove($e);
                },
                \App::config()->coreV2['timeout'] * 2
            );
        }

        // выполнение пакета запросов
        $client->execute();

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
        $page->setParam('enterpizeCoupon', $enterpizeCoupon);
        $page->setParam('viewParams', ['showSideBanner' => false]);
        $page->setParam('isCouponSent', $isCouponSent);

        $response = new \Http\Response($page->show());

        if ($isCouponSent) {
            $response->headers-> clearCookie(\App::config()->enterprize['cookieName']);
        }

        return $response;
    }
}