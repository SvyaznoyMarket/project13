<?php

namespace Controller\Enterprize;


use Model\EnterprizeCoupon\Entity;

trait CouponTrait {

    /** Возвращает данные для mustache
     * @param Entity $coupon
     * @return array
     */
    public static function getCouponData(Entity $coupon) {

        $helper = \App::helper();
        $userEntity = \App::user()->getEntity();

        return [
            'name'        => $coupon->getName(),
            'token'       => $coupon->getToken(),
            'number'      => $coupon->getDiscount() ? $coupon->getDiscount()->getNumber() : null,
            'discount'    => $helper->formatPrice($coupon->getPrice()) . ($coupon->getIsCurrency() ? ' <span class="rubl">p</span>' : '%'),
            'start'       =>
                (false && $coupon->getDiscount())
                    ? ($coupon->getDiscount()->getStartDate() instanceof \DateTime ? $coupon->getDiscount()->getStartDate()->format('d.m.Y') : null)
                    : ($coupon->getStartDate() instanceof \DateTime ? $coupon->getStartDate()->format('d.m.Y') : null)
            ,
            'end'         =>
                (false && $coupon->getDiscount())
                    ? ($coupon->getDiscount()->getEndDate() instanceof \DateTime ? $coupon->getDiscount()->getEndDate()->format('d.m.Y') : null)
                    : ($coupon->getEndDate() instanceof \DateTime ? $coupon->getEndDate()->format('d.m.Y') : null)
            ,
            'description' => $coupon->getSegmentDescription(),
            'minOrderSum' => $helper->formatPrice($coupon->getMinOrderSum()),
            'isUserOwner' => (bool)$coupon->getDiscount(),
            'link'        =>
                $coupon->getName()
                    ? [
                    'name' => $coupon->getName(),
                    'url'  => $coupon->getLink(),
                ]
                    : null
            ,
            'slider'      => [
                'url' => \App::router()->generate('enterprize.slider', ['enterprizeToken' => $coupon->getToken()]),
            ],
            'user'        =>
                [
                    'isAuthorized' => (bool)$userEntity,
                    'isMember'     => $userEntity && $userEntity->isEnterprizeMember(),
                ]
                + (
                $userEntity
                    ? [
                    'mobile' => preg_replace('/^8/', '+7', $userEntity->getMobilePhone()),
                    'name'   => $userEntity->getFirstName(),
                    'email'  => $userEntity->getEmail(),
                ]
                    :
                    []
                )
            ,
            'form'        => [
                'action' =>
                    ($userEntity && $userEntity->isEnterprizeMember())
                        ? \App::router()->generate('enterprize.form.show', ['enterprizeToken' => $coupon->getToken()])
                        : \App::router()->generate('enterprize.form.update', ['enterprizeToken' => $coupon->getToken()])
                ,
            ],
        ];
    }

}