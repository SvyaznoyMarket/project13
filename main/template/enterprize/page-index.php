<?php
/**
 * @var $page             \View\DefaultLayout
 * @var $user             \Session\User
 * @var $enterpizeCoupons \Model\EnterprizeCoupon\Entity[]
 */
?>

<div class="enterPrize">

    <h1 class="enterPrize__logo">Enter Prize</h1>

    <div class="bgPage"></div>

    <ul class="enterPrize__rules clearfix">
        <li class="enterPrize__rules__item"><span class="sep">Выбери</span> свою фишку со скидкой и жми получить!</li>
        <li class="enterPrize__rules__sep"></li>
        <li class="enterPrize__rules__item" style="width: 168px;"><span class="sep">Получи</span> номер фишки на E-mail и мобильный телефон, которые укажешь для участия в Enter Prize!</li>
        <li class="enterPrize__rules__sep"></li>
        <li class="enterPrize__rules__item"><span class="sep">Покупай</span> со скидкой, используя номер фишки при оплате!</li>
    </ul>

    <ul class="enterPrize__list clearfix">

        <? $i = 0; foreach ($enterpizeCoupons as $coupon): $i++ ?>

            <?
            $itemClass = 'enterPrize__list__item';
            if (!($i % 4)) {
                $itemClass .= ' mLast';
            }
            if (!$coupon->getImage()) {
                $itemClass .= ' mNoIco';
            }
            ?>

            <li class="<?= $itemClass ?>">
                <a class="enterPrize__list__link<? if (!\App::user()->getEntity()): ?> jsEnterprizeAuthLink<? endif ?>" href="<?= $page->url('enterprize.get', ['enterprize_coupon' => $coupon->getToken()]) ?>">
                <span class="cuponImg"<? if ($coupon->getBackgroundImage()): ?> style="background-image: url(<?= $coupon->getBackgroundImage() ?>);"<? endif ?>>
                    <span class="cuponImg__inner">
                        <? if ($coupon->getImage()): ?>
                            <span class="cuponIco"><img src="<?= $coupon->getImage() ?>" /></span>
                        <? endif ?>

                        <? if ($coupon->getName()): ?>
                            <span class="cuponDesc"><?= $coupon->getName() ?></span>
                        <? endif ?>

                        <? if ($coupon->getPrice()): ?>
                            <span class="cuponPrice"><?= $coupon->getPrice() . (!$coupon->getIsCurrency() ? '%' : '') ?> <? if ($coupon->getIsCurrency()): ?><span class="rubl">p</span><? endif ?></span>
                        <? endif ?>
                    </span>
                </span>

                <span class="cuponImgHover">
                    <span class="cuponBtn">Получить</span>
                </span>
                </a>
            </li>
        <? endforeach ?>
    </ul>
</div>