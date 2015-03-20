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
        $isCouponSent = isset($data['isCouponSent']) ? (bool)$data['isCouponSent'] : false;
        if ($isCouponSent) {
            unset($data['isCouponSent']);
            $session->set($sessionName, $data);
        }

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
            }, $isCouponSent ? 0 : null);
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
        /** @var \Model\EnterprizeCoupon\DiscountCoupon\Entity[] $userDiscounts */
        $userDiscounts = [];
        if (\App::user()->getToken()) {
            try {
                $client->addQuery('user/get-discount-coupons', ['token' => \App::user()->getToken()], [],
                    function ($data) use (&$userDiscounts, &$userCouponSeries) {
                        if (isset($data['detail']) && is_array($data['detail'])) {
                            foreach ($data['detail'] as $item) {
                                $entity = new \Model\EnterprizeCoupon\DiscountCoupon\Entity($item);
                                $userDiscounts[] = $entity;
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

        /** @var \Model\EnterprizeCoupon\Entity[] $enterpizeCouponsByToken */
        $enterpizeCouponsByToken = [];
        foreach ($enterpizeCoupons as $coupon) {
            $enterpizeCouponsByToken[$coupon->getToken()] = $coupon;
        }

        /** @var \Model\EnterprizeCoupon\Entity[] $userCoupons */
        $userCoupons = [];
        foreach ($userDiscounts as $userDiscount) {
            $coupon = isset($enterpizeCouponsByToken[$userDiscount->getSeries()]) ? $enterpizeCouponsByToken[$userDiscount->getSeries()] : null;
            if (
                !$userDiscount->getSeries()
                || !$coupon
                || $userDiscount->getUsed()
                || ($userDiscount->getEndDate() && ($userDiscount->getEndDate() < new \DateTime()))
            ) {
                continue;
            }

            $coupon->setDiscount($userDiscount);

            $userCoupons[] = $coupon;
        }


        // отфильтровываем ненужные купоны
        $enterpizeCoupons = array_filter($enterpizeCoupons, function($coupon) use ($limits, $userCouponSeries, $user) {
            if (!$coupon instanceof \Model\EnterprizeCoupon\Entity || !array_key_exists($coupon->getToken(), $limits)) return false;

            // убираем купоны ренее выданные пользователю
            if (in_array($coupon->getToken(), $userCouponSeries)) {
                return false;
            }

            // убираем купоны с кол-вом <= 0
            if ($limits[$coupon->getToken()] <= 0) return false;

            if (!(bool)$coupon->isForMember() && !(bool)$coupon->isForNotMember()) return false;

            if ($user && $user->isEnterprizeMember() && !(bool)$coupon->isForMember() && (bool)$coupon->isForNotMember()) return false;

            return true;
        });

        // получаем товары
        $products = [];
        if ($enterpizeCoupon) {
            $products = \Controller\Enterprize\FormAction::getProducts($enterpizeCoupon);
        }

        /* SITE-4110 Задаем флаг полной регистрации в EnterPrize. Полная регистрация включает в себя регистрацию
           с помощью метода "coupon/register-in-enter-prize" и подтверждение телефона и email-а */
        $isRegistration = false;
        if ($isCouponSent) {
            try {
                // получение флага с сессии
                if (array_key_exists('isRegistration', $data)) {
                    $isRegistration = $data['isRegistration'];

                    // чистим флаг в сессии
                    unset($data['isRegistration']);
                    $session->set($sessionName, $data);
                }

                if (!$user || !$user->getId()) {
                    throw new \Exception('Купон получили, но пользователь не авторизован');
                }

                // получение флага с хранилища
                $storageResult = \App::coreClientPrivate()->query('storage/get', ['user_id' => $user->getId()]);
                if (!(bool)$storageResult || !isset($storageResult['value'])) {
                    throw new \Exception(sprintf('Не пришли данные с хранилища для user_id=%s', $user->getId()));
                }

                $storageData = (array)json_decode($storageResult['value']);

                if (array_key_exists('isRegistration', $storageData)) {
                    $isRegistration = $storageData['isRegistration'];

                    // чистим флаг в хранилище
                    unset($storageData['isRegistration']);
                    if (empty($storageData)) {
                        $delete = \App::coreClientPrivate()->query('storage/delete', ['user_id' => $user->getId()]);
                    } else {
                        $post = \App::coreClientPrivate()->query('storage/post', ['user_id' => $user->getId()], $storageData);
                    }
                }

            } catch(\Exception $e) {
                \App::exception()->remove($e);
                \App::logger()->error($e);
            }
        }

        $enterprizeData = [];
        if ($isRegistration && $isCouponSent && $enterpizeCoupon) {
            $enterprizeDataDefault = [
                'name'            => null,
                'mobile'          => null,
                'email'           => null,
                'couponName'      => $enterpizeCoupon->getName(),
                'enterprizeToken' => $enterpizeCoupon->getToken(),
                'date'            => date('d.m.Y'),
                'time'            => date('H:i'),
                'enter_id'        => !empty($data['token']) ? $data['token'] : \App::user()->getToken(),
            ];
            $enterprizeData = array_merge($enterprizeDataDefault, array_intersect_key($data, $enterprizeDataDefault));
        }

        $page = new \View\Enterprize\IndexPage();
        $page->setParam('enterpizeCoupons', $enterpizeCoupons);
        $page->setParam('enterpizeCoupon', $enterpizeCoupon);
        $page->setParam('userCoupons', $userCoupons);
        $page->setParam('viewParams', ['showSideBanner' => false]);
        $page->setParam('isCouponSent', $isCouponSent);
        $page->setParam('isRegistration', $isRegistration);
        $page->setParam('form', (new \Controller\Enterprize\FormAction())->getForm());
        $page->setParam('products', $products);
        $page->setParam('enterprizeData', $enterprizeData);

        return new \Http\Response($page->show());
    }
}