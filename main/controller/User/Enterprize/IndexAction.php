<?php


namespace Controller\User\Enterprize;

use EnterApplication\CurlTrait;
use EnterQuery as Query;

class IndexAction extends \Controller\User\PrivateAction {
    use CurlTrait;

    public function execute(\Http\Request $request) {
        $config = \App::config();
        $curl = $this->getCurl();

        $userEntity = \App::user()->getEntity();

        $discountQuery = new Query\Coupon\GetByUserToken();
        $discountQuery->userToken = $userEntity->getToken();
        $discountQuery->prepare();

        $couponQuery = new Query\Coupon\Series\Get();
        $couponQuery->memberType = $userEntity->isEnterprizeMember() ? '1' : null;
        $couponQuery->prepare();

        // настройки из cms
        /** @var Query\Config\GetByKeys|null $configQuery */
        $configQuery =
            $config->userCallback['enabled']
            ? (new Query\Config\GetByKeys(['site_call_phrases']))->prepare()
            : null
        ;

        $curl->execute();

        // купоны, сгруппированные по сериям
        $discountsGroupedByCoupon = [];
        foreach ($discountQuery->response->coupons as $item) {
            $discount = new \Model\EnterprizeCoupon\DiscountCoupon\Entity($item);
            $discountsGroupedByCoupon[$discount->getSeries()][] = $discount;
        }

        /** @var \Model\EnterprizeCoupon\Entity[] $coupons */
        $coupons = [];
        foreach ($couponQuery->response->couponSeries as $item) {
            $token = isset($item['uid']) ? (string)$item['uid'] : null;
            if (!$token || !isset($discountsGroupedByCoupon[$token])) {
                continue;
            }

            foreach ($discountsGroupedByCoupon[$token] as $discount) {
                $coupon = new \Model\EnterprizeCoupon\Entity($item);
                $coupon->setDiscount($discount);
                $coupons[] = $coupon;
            }
        }

        // добавляем ссылку на получение купонов
        $coupons[] = null;
        $couponsByRow = array_chunk($coupons, 4);

        // SITE-6622
        $callbackPhrases = [];
        if ($configQuery) {
            foreach ($configQuery->response->keys as $item) {
                if ('site_call_phrases' === $item['key']) {
                    $value = json_decode($item['value'], true);
                    $callbackPhrases = !empty($value['private']) ? $value['private'] : [];
                }
            }
        }

        $page = new \View\User\Enterprize\IndexPage();
        $page->setParam('coupons', $coupons);
        $page->setParam('couponsByRow', $couponsByRow);
        $page->setGlobalParam('callbackPhrases', $callbackPhrases);

        return new \Http\Response($page->show());
    }
}