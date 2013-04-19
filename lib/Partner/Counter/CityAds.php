<?php

namespace Partner\Counter;

class CityAds {
    const NAME = 'cityads';

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
}