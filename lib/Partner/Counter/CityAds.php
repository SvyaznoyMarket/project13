<?php

namespace Partner\Counter;

class CityAds {
    const NAME = 'cityads';
    const PARTNER_ID = 5085;

    /**
     * @param \Model\Order\Entity $order
     * @return string[]
     */
    public static function getLink(\Model\Order\Entity $order) {
        $link = null;

        try {
            $link = strtr('https://t.gameleads.ru/{order.id}/q1/{sig}', [
                '{order.id}' => $order->getNumber(),
                '{sig}'      => md5('2085' . $order->getNumber()),
            ]);
        } catch (\Exception $e) {
            \App::logger()->error($e, ['partner', 'cityads']);
        }

        return $link;
    }


    /**
     * @param \Model\Order\Entity $order
     * @param array $productsById
     * @param bool $isScript
     * @return bool|null|string
     */
    public static function getCityAdspixLink(\Model\Order\Entity $order,  array &$productsById = [], $page, $isScript = false) {
        $request = \App::request();
        $click_id = $request->cookies->get('click_id');
        $prx = $request->cookies->get('prx');

        if (!$click_id || !$prx){
            return false;
        }

        $userEntity = \App::user()->getEntity();
        $uid = $userEntity ? $userEntity->getId() : 0;
        $paymentMethod = null;
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

            foreach ($order->getProduct() as $orderProduct)
            {
                /** @var $product \Model\Product\Entity */
                $product = isset($productsById[$orderProduct->getId()]) ? $productsById[$orderProduct->getId()] : null;
                if (!$product) continue;

                if ($product->getRootCategory()) {
                    $category = $product->getRootCategory();
                } else {
                    $category = $product->getCategory();
                    $category = reset($category);
                }
                /** @var $category \Model\Product\Category\Entity*/

                $basket[] = [
                    'pid' => $product->getId(),
                    'pn' => $product->getName(),
                    'up' => $product->getPrice(),
                    'pc' => $category->getId(),
                    'qty' => $orderProduct->getQuantity()
                ];
            }

            $link = strtr('https://cityadspix.com/track/{order_id}/ct/q1/c/2085?click_id={click_id}&prx={prx}&customer_type={customer_type}&payment_method={payment_method}&price={price}&currency={currency}&basket={basket}', [
                '{order_id}'        => $order->getNumber(),
                '{click_id}'        => $click_id,
                '{prx}'             =>  $prx,
                '{customer_type}'   => ($uid)  ? 'returned' : 'new',
                '{payment_method}'  => $paymentMethod,
                '{price}'           => $order->getSum(),
                '{currency}'        => 'RUR',
                //'{basket}'          => str_replace( '\"' ,  '\'', json_encode($basket, JSON_UNESCAPED_UNICODE)),
                '{basket}'          => $page->json($basket),
            ]);

            if ($isScript) $link .= '&md=' . count($order->getProduct());

        } catch (\Exception $e) {
            \App::logger()->error($e, ['partner', 'cityads']);
            \App::exception()->remove($e);
        }

        return $link;
    }


    /**
     * Возвращает ссылку для страницы подписки /subscribe_friends
     *
     * @return string
     */
    public static function getSubscribeLink()
    {
        $link = null;
        //$user = \App::user();

        try {
            $target_name = "q1";

            $order_id = \App::request()->get('email');
            if ( empty($order_id) ) {
                //return false;
                $order_id = 'none';
            }

            /*if ( $user ) {
                $uEntity = $user->getEntity();
                if ( $uEntity ) {
                    $order_id = $uEntity->getEmail();
                }
            }*/

            $link = "cityadspix.com/track/$order_id/ct/{$target_name}/c/" . self::PARTNER_ID;
        } catch (\Exception $e) {
            \App::logger()->error($e, ['partner', 'cityads']);
        }

        return $link;
    }

}