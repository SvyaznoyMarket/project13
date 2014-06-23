<?php
/**
 * @var $page             \View\DefaultLayout
 * @var $user             \Session\User
 * @var $enterpizeCoupons \Model\EnterprizeCoupon\Entity[]
 * @var $isCouponSent     bool
 */
?>

<?
$isEnterprizeMember = $user->getEntity() && $user->getEntity()->isEnterprizeMember();
?>

<div class="enterPrize">

    <h1 class="enterPrize__logo">Enter Prize</h1>

    <div class="bgPage"></div>

    <? if ((bool)$isCouponSent): ?>
        <div class="popup" id="enterprize-info-block">
            <div class="popupbox">
                <div class="font18 pb18">Вы можете заказать прямо сейчас любой товар Enter c фишкой, которую Вы получили по e-mail и в SMS. Или выбрать еще фишки <a href="#" class="closePopup">ЗДЕСЬ!</a></div>
            </div>
            <p style="text-align:center"><a href="#" class="closePopup bBigOrangeButton">OK</a></p>
        </div>
    <? endif ?>
    
    <? if (!$user->getEntity()): ?>
        <div class="enterPrizeHello">Всё, что вы хотели, со скидкой до 70%</div>

        <? /*p class="enterPrizeDesc">
            В каждой фишке - код на скидку.<br/>
            Купите дешевле iPhone, горный велосипед или многое другое.<br/>
            Интересно? Жми на фишку!
        </p*/ ?>

        <p class="enterPrizeDesc">Уже в ENTER PRIZE? <a href="<?= \App::router()->generate('user.login') ?>" class="jsEnterprizeAuthLink">Войдите и получите ещё скидки</a></p>
    <? endif ?>

    <? if ($isEnterprizeMember): ?>
        <div class="enterPrizeHello mReg">Всё, что вы хотели, со скидкой до 70%</div>
        <?= $page->render('enterprize/_contentDescription') ?>
    <? endif ?>

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

            $couponLink = $page->url('enterprize.form.show', ['enterprizeToken' => $coupon->getToken()]);
            if ($isEnterprizeMember) {
                $couponLink = $page->url('enterprize.show', ['enterprizeToken' => $coupon->getToken()]);
            }
            if ($coupon->isInformationOnly()) {
                $couponLink = $coupon->getDescriptionToken()
                    ? $page->url('content', ['token' => $coupon->getDescriptionToken()])
                    : null;
            } ?>

            <li class="<?= $itemClass ?>">
                <a class="enterPrize__list__link" href="<?= $couponLink ? $couponLink : '#' ?>">
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

    <p class="rulesEP"><a href="/reklamnaya-akcia-enterprize">Правила участия в ENTER PRIZE</a></p>
</div>