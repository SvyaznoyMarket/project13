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
        $repository = \RepositoryManager::enterprize();

        $user = \App::user()->getEntity();
        $session = \App::session();
        $sessionName = \App::config()->enterprize['formDataSessionKey'];

        $data = $session->get($sessionName, []);
        $enterprizeToken = isset($data['enterprizeToken']) ? $data['enterprizeToken'] : null;

        // SITE-3931, SITE-3934
        $isCouponSent = (bool)$request->cookies->get(\App::config()->enterprize['cookieName']);


        // получение купонов
        /**
         * @var $enterpizeCoupon    \Model\EnterprizeCoupon\Entity
         * @var $enterpizeCoupons   \Model\EnterprizeCoupon\Entity[]
         */
        $enterpizeCoupon = null;
        $enterpizeCoupons = [];
        try {
            $repository->prepareCollection(function($data) use (&$enterpizeCoupons, &$enterpizeCoupon, &$isCouponSent, $enterprizeToken, $user) {
                foreach ((array)$data as $item) {
                    $coupon = new \Model\EnterprizeCoupon\Entity($item);
                    $enterpizeCoupons[] = $coupon;

                    // если купон уже отослан, то пытаемся его получить из общего списка
                    if ((bool)$isCouponSent && (bool)$enterprizeToken && $enterprizeToken == $coupon->getToken()) {
                        $enterpizeCoupon = $coupon;
                    }
                }
            });
        } catch (\Exception $e) {
            \App::logger()->error($e);
            \App::exception()->remove($e);
        }

        // получение лимитов для купонов
        $limits = [];
        try {
            $client->addQuery('coupon/limits', [], [], function($data) use (&$limits){
                if ((bool)$data && !empty($data['detail'])) {
                    $limits = $data['detail'];
                }
            });
        } catch (\Exception $e) {
            \App::logger()->error($e->getMessage(), ['enterprize']);
            \App::exception()->remove($e);
        }

        // получаем купоны ренее выданные пользователю
        $userCouponSeries = [];
        $userCoupons = [];
        if (\App::user()->getToken()) {
            try {
                $client->addQuery('user/get-discount-coupons', ['token' => \App::user()->getToken()], [],
                    function ($data) use (&$userCoupons, &$userCouponSeries) {
                        if (isset($data['detail']) && is_array($data['detail'])) {
                            foreach ($data['detail'] as $item) {
                                $entity = new \Model\EnterprizeCoupon\DiscountCoupon\Entity($item);
                                $userCoupons[] = $entity;
                                $userCouponSeries[] = $entity->getSeries();
                            }
                        }
                    }, null, \App::config()->coreV2['timeout'] * 2
                );
            } catch (\Exception $e) {
                \App::logger()->error($e->getMessage(), ['enterprize']);
                \App::exception()->remove($e);
            }
        }

        // выполнение пакета запросов
        \App::curl()->execute();

        // отфильтровываем ненужные купоны
        $enterpizeCoupons = array_filter($enterpizeCoupons, function($coupon) use ($limits, $userCouponSeries) {
            if (!$coupon instanceof \Model\EnterprizeCoupon\Entity || !array_key_exists($coupon->getToken(), $limits)) return false;

            // убераем купоны с кол-вом <= 0
            if ($limits[$coupon->getToken()] <= 0) return false;

            // убераем купоны ренее выданные пользователю
            if (in_array($coupon->getToken(), $userCouponSeries)) return false;

            if (!(bool)$coupon->isForMember() && !(bool)$coupon->isForNotMember()) return false;

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