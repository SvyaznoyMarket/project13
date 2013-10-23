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
     * Возвращает ссылку для страницы подписки /subscribe_friends
     *
     * @return string
     */
    public static function getSubscribeLink()
    {
        $link = null;
        $user = \App::user();

        try {
            $target_name = "q1";

            $order_id = "___ORDERID___"; // TODO
            if ( $user ) {
                $uEntity = $user->getEntity();
                if ( $uEntity ) {
                    $order_id = $uEntity->getEmail();
                }
            }

            $link = "cityadspix.com/track/$order_id/ct/{$target_name}/c/" . self::PARTNER_ID;
        } catch (\Exception $e) {
            \App::logger()->error($e, ['partner', 'cityads']);
        }

        return $link;
    }

}