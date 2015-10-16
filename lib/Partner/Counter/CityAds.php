<?php

namespace Partner\Counter;

use \Model\Product\Category\Entity as Category;

class CityAds {
    const NAME = 'cityads';
    const PARTNER_ID = 5085;
    const ENTER_ID = '2085';

    /**
     * @deprecated
     * @param \Model\Order\Entity $order
     * @return string
     */
    public static function getLink(\Model\Order\Entity $order) {
        $link = null;

        try {
            $link = strtr('https://t.gameleads.ru/{order.id}/q1/{sig}', [
                '{order.id}' => $order->getNumber(),
                '{sig}'      => md5(self::ENTER_ID . $order->getNumber()),
            ]);
        } catch (\Exception $e) {
            \App::logger()->error($e, ['partner', 'cityads']);
        }

        return $link;
    }


    /**
     * @param \Model\Order\Entity $order
     * @param \Model\Product\Entity[] $productsById
     * @param \Templating\HtmlLayout $page
     * @return bool|null|string
     */
    public static function getCityAdspixLink(\Model\Order\Entity $order,  &$productsById = [], $page) {
        $request = \App::request();
        $clickId = $request->cookies->get('click_id');

        if (!$clickId){
            return false;
        }

        $userEntity = \App::user()->getEntity();
        $uid = $userEntity ? $userEntity->getId() : 0;
        $paymentMethod = null;
        $coupon = null;
        $discount = null;
        $commission = 0;
        $link = null;
        $basket = [];

        switch ($order->getPaymentId())
        {
            case \Model\PaymentMethod\Entity::CASH_ID:
                $paymentMethod = 'Cash';
                break;
            case \Model\PaymentMethod\Entity::CARD_ID:
            case \Model\PaymentMethod\Entity::CREDIT_ID:
                $paymentMethod = 'Credit Card';
                break;
            case \Model\PaymentMethod\Entity::CERTIFICATE_ID:
                $paymentMethod = 'Debit Card';
                break;
            case \Model\PaymentMethod\Entity::WEBMONEY_ID:
            case \Model\PaymentMethod\Entity::QIWI_ID:
            case \Model\PaymentMethod\Entity::PAYPAL_ID:
                $paymentMethod = 'Webmoney/Paypal';
                break;
        }


        try {

            foreach ($order->getProduct() as $orderProduct) {
                /** @var $product \Model\Product\Entity */
                $product = isset($productsById[$orderProduct->getId()]) ? $productsById[$orderProduct->getId()] : null;
                if (!$product) continue;

                if ($product->getRootCategory()) {
                    $category = $product->getRootCategory();
                } else {
                    $category = $product->getCategory();
                    $category = reset($category);
                }
                /** @var $category Category*/

                $commission += self::getCategoryCommission($category) * 0.01 * $product->getPrice() * $orderProduct->getQuantity();

                $basket[] = [
                    'pid' => $product->getId(),
                    'pn' => $product->getName(),
                    'up' => $product->getPrice(),
                    'pc' => $category->getId(),
                    'qty' => $orderProduct->getQuantity()
                ];
            }

            $link = strtr('https://cityadspix.com/track/{order_id}/ct/q1/c/{enter_id}?click_id={click_id}&customer_type={customer_type}&payment_method={payment_method}&order_total={order_total}&commission={commission}&currency={currency}&coupon={coupon}&discount={discount}&basket={basket}', [
                '{enter_id}'        => self::ENTER_ID,
                '{order_id}'        => $order->getNumber(),
                '{click_id}'        => $clickId,
                '{customer_type}'   => ($uid)  ? 'returned' : 'new',
                '{payment_method}'  => $paymentMethod,
                '{order_total}'     => $order->getSum(),
                '{commission}'      => sprintf('%0.2f', $commission),
                '{currency}'        => 'RUR',
                '{coupon}'          => $coupon,
                '{discount}'        => $discount,
                '{basket}'          => $page->json($basket),
            ]);

        } catch (\Exception $e) {
            \App::logger()->error($e, ['partner', 'cityads']);
            \App::exception()->remove($e);
        }

        return $link;
    }

    /**
     * Возвращает комиссию для категории в процентах
     * @param Category $category
     * @return float
     */
    private static function getCategoryCommission($category) {
        switch ($category->getUi()) {
            case Category::UI_BYTOVAYA_TEHNIKA:
                return 3.77;
            case Category::UI_MEBEL:
                return 12.36;
            case Category::UI_SDELAY_SAM:
                return 7.8;
            case Category::UI_AVTO:
                return 5.32;
            case Category::UI_DETSKIE_TOVARY:
                return 3.52;
            case Category::UI_TOVARY_DLYA_DOMA:
                return 5.93;
            case Category::UI_AKSESSUARY:
                return 5.0;
            case Category::UI_KRASOTA_I_ZDOROVIE:
                return 3.55;
            case Category::UI_UKRASHENIYA_I_CHASY:
                return 11.82;
            case Category::UI_PARFUMERIA_I_COSMETIKA:
                return 3.9;
            case Category::UI_ZOOTOVARY:
                return 3.17;
            case Category::UI_SPORT_I_OTDYH:
                return 9.0;
            case Category::UI_ELECTRONIKA:
                return 2.0;
            case Category::UI_TCHIBO:
                return 10.4;
            case Category::UI_IGRY_I_KONSOLI:
                return 5.91;
            default:
                return 0.0;
        }

    }


    /**
     * Возвращает ссылку для страницы подписки /subscribe_friends
     *
     * @return string
     */
    public static function getSubscribeLink()
    {
        $link = null;

        try {
            $targetName = "q1";

            $orderId = \App::request()->get('email');
            if ( empty($orderId) ) {
                //return false;
                $orderId = 'none';
            }

            /*if ( $user ) {
                $uEntity = $user->getEntity();
                if ( $uEntity ) {
                    $order_id = $uEntity->getEmail();
                }
            }*/

            $link = "cityadspix.com/track/$orderId/ct/{$targetName}/c/" . self::PARTNER_ID;
        } catch (\Exception $e) {
            \App::logger()->error($e, ['partner', 'cityads']);
        }

        return $link;
    }

}