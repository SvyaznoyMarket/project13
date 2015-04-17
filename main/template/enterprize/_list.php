<?php

/**
 * @param \Helper\TemplateHelper $helper
 * @param \Model\EnterprizeCoupon\Entity[] $enterpizeCoupons
 * @param \Session\User $user
 * @param \View\Enterprize\Form $form
 */
$f = function(
    \Helper\TemplateHelper $helper,
    array $enterpizeCoupons,
    \Session\User $user,
    \View\Enterprize\Form $form
) {
    /** @var $coupon \Model\EnterprizeCoupon\Entity **/

    $userEntity = $user->getEntity();
    $isEnterprizeMember = $userEntity && $userEntity->isEnterprizeMember();
?>

<? $i = 0; foreach(array_chunk($enterpizeCoupons, 4) as $couponsInChunk): ?>
    <div class="ep-list__row clearfix js-enterprize-coupon-parent">
        <? foreach ($couponsInChunk as $columnNum => $coupon): $i++ ?>

            <?
            /** @var \Model\EnterprizeCoupon\Entity[] $couponsInChunk */
            $itemClass = 'ep-list__i js-enterprize-coupon';
            if (!($i % 4)) {
                $itemClass .= ' ep-list__i--last';
            }
            if (!$coupon->getImage()) {
                $itemClass .= ' ep-list__i--noico';
            }

            /*
            $couponLink = $helper->url('enterprize.form.show', ['enterprizeToken' => $coupon->getToken()]);
            if ($isEnterprizeMember) {
                $couponLink = $helper->url('enterprize.show', ['enterprizeToken' => $coupon->getToken()]);
            }
            if ($coupon->isInformationOnly()) {
                $couponLink = $coupon->getDescriptionToken()
                    ? $helper->url('content', ['token' => $coupon->getDescriptionToken()])
                    : null;
            }
            */

            $isNotMember = !$coupon->isForNotMember() && !$isEnterprizeMember;

            $expiredDays = null;
            $discount = $coupon->getDiscount();
            if ($discount && $discount->getTo()) {
                try {
                    if ($discount->getEndDate()) {
                        $expiredDays = $discount->getEndDate()->diff(new \DateTime('now'))->days;
                        if (0 === $expiredDays) {
                            $expiredDays = 1;
                        }
                    }
                } catch (\Exception $e) {}
            }

            $dataValue = [
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
                        ($user->getEntity() && $user->getEntity()->isEnterprizeMember())
                        ? \App::router()->generate('enterprize.form.show', ['enterprizeToken' => $coupon->getToken()])
                        : \App::router()->generate('enterprize.form.update', ['enterprizeToken' => $coupon->getToken()])
                    ,
                ],
            ];
            ?>

            <div class="<?= $itemClass . ($isNotMember ? ' mMembers' : '') ?>" data-value="<?= $helper->json($dataValue) ?>" data-column="col-<?= $columnNum + 1 ?>" title="<?= ((null !== $expiredDays) ? sprintf('Может быть применена в течении %s %s', $expiredDays, $helper->numberChoice($expiredDays, ['дня', 'дней', 'дней'])) : '') ?>">
                <div class="ep-list__lk">
                    <span class="ep-coupon"<? if ($coupon->getBackgroundImage()): ?> style="background-image: url(<?= $coupon->getBackgroundImage() ?>);"<? endif ?>>
                        <span class="ep-coupon__inner">
                            <? if ($coupon->getImage()): ?>
                                <span class="ep-coupon__ico"><img src="<?= $coupon->getImage() ?>" /></span>
                            <? endif ?>

                            <? if ($coupon->getName()): ?>
                                <span class="ep-coupon__desc"><?= $coupon->getName() ?></span>
                            <? endif ?>

                            <? if ($coupon->getPrice()): ?>
                                <span class="ep-coupon__price"><?= $helper->formatPrice($coupon->getPrice()) . (!$coupon->getIsCurrency() ? '%' : '') ?>
                                    <? if ($coupon->getIsCurrency()): ?>
                                        <span class="rubl">p</span>
                                    <? endif ?>
                                </span>
                            <? endif ?>
                        </span>
                    </span>

                    <? if ((null !== $expiredDays) && ($expiredDays <= 3)): ?>
                        <div class="ep-finish">
                            <span class="ep-finish__tl">До конца действия<br/>фишки <?= $helper->numberChoice($expiredDays, ['остался', 'осталось', 'осталось']) ?></span>
                            <span class="ep-finish__num"><?= $expiredDays ?></span>
                            <div class="ep-finish__day"><?= $helper->numberChoice($expiredDays, ['день', 'дня', 'дней']) ?></div>
                        </div>
                    <? endif ?>

                    <? if ($isNotMember): // Только для игроков EnterPrize  ?>
                        <span class="ep-coupon-hover">
                        <span class="couponText">Только<br/> для игроков<br/> <span class="epTextLogo">Enter <span class="epTextLogo_colors">Prize</span></span></span>
                    </span>
                    <? else:?>
                        <? if ( !$dataValue['isUserOwner'] ): // Только для игроков EnterPrize  ?><span class="ep-coupon-hover"></span><? endif ?>
                    <? endif ?>
                </div>
            </div>
        <? endforeach ?>
    </div>
<? endforeach // end chunk ?>

<? }; return $f;