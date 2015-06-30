<?php

use Controller\Enterprize\CouponTrait;

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

            $dataValue = CouponTrait::getCouponData($coupon);
            ?>

            <div class="<?= $itemClass . ($isNotMember ? ' mMembers' : '') ?>"
                 data-value="<?= $helper->json($dataValue) ?>"
                 data-column="col-<?= $columnNum + 1 ?>"
                 title="<?= ((null !== $expiredDays) ? sprintf('Может быть применена в течении %s %s', $expiredDays, $helper->numberChoice($expiredDays, ['дня', 'дней', 'дней'])) : '') ?>">
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