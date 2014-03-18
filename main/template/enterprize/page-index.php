<?php
/**
 * @var $page             \View\DefaultLayout
 * @var $user             \Session\User
 * @var $enterpizeCoupons \Model\EnterprizeCoupon\Entity[]
 */
?>

<?
$isEnterprizeMember = $user->getEntity() && $user->getEntity()->isEnterprizeMember();
?>

<div class="enterPrize">

    <h1 class="enterPrize__logo">Enter Prize</h1>

    <div class="bgPage"></div>

    <? if ($isEnterprizeMember): ?>
        <div class="enterPrizeHello" style="display: none;">Всё, что вы хотели, со скидкой до 70%</div>
    <? endif ?>

    <div class="enterPrizeHello mReg">Всё, что вы хотели, со скидкой до 70%</div>

    <!-- <ul class="enterPrize__rules clearfix">
        <li class="enterPrize__rules__item">
            1. Выбери фишку со скидкой.<br/>
            2. Заполни три поля.<br/>
            3. Лови фишку на e-mail и в СМС.<br/>
            Разыграй фишку в заказе и получи скидку.
        </li>

        <li class="enterPrize__rules__sep"></li>
        <li class="enterPrize__rules__item" style="width: 168px;"><span class="sep">Получи</span> номер фишки на E-mail и мобильный телефон, которые укажешь для участия в Enter Prize!</li>
        <li class="enterPrize__rules__sep"></li>
        <li class="enterPrize__rules__item"><span class="sep">Покупай</span> со скидкой, используя номер фишки при оплате!</li>
    </ul> -->

    <? if (!$user->getEntity()): ?>
        <p class="enterPrizeDesc">Уже в ENTER PRIZE? <a href="<?= \App::router()->generate('user.login') ?>" class="jsEnterprizeAuthLink">Войдите и получите ещё скидки</a></p>
    <? endif ?>

    <? if ($isEnterprizeMember): ?>
        <p class="enterPrizeDesc"><span class="enterPrizeDesc__text">Как ещё получать фишки?</span></p>

        <div class="enterPrizeListWrap">
            <ul class="enterPrizeList">
                <li class="enterPrizeList__item mBlue">
                    <strong>Сайт www.enter.ru</strong><br/>
                    Всегда входите в личный кабинет.<br/>
                    Заказывайте товары как обычно.
                </li>

                <li class="enterPrizeList__item mOrange">
                    <strong>Розничные магазины ENTER</strong><br/>
                    Входите в личный кабинет в терминале.<br/>
                    Заказывайте товары через терминал.
                </li>

                <li class="enterPrizeList__item mGreen">
                    <strong>Контакт-сENTER 8 800 700 00 09</strong><br/>
                    Скажите оператору Контакт-cENTER, что Вы — участник Enter Prize!
                    Оператор поможет оформить заказ.
                </li>
            </ul>

            <div class="enterPrizeFinish">Ловите номер фишки в чеке после оплаты заказа!</div>
        </div>

        <p class="enterPrizeDesc"><span class="enterPrizeDesc__text">Как играть фишками и получать скидки?</span></p>

        <div class="enterPrizeListWrap">
            <ul class="enterPrizeList">
                <li class="enterPrizeList__item mBlue">
                    <strong>Сайт www.enter.ru</strong><br/>
                    Входите в личный кабинет на www.enter.ru!<br/>
                    При оформлении Заказа в поле КУПОН или ФИШКА вводите номер фишки! 
                </li>

                <li class="enterPrizeList__item mOrange">
                    <strong>Розничные магазины ENTER</strong><br/>
                    Скажите сотруднику магазина, что Вы — участник Enter Prize!<br/>
                    И сообщите номер Фишки при оплате заказа! 
                </li>

                <li class="enterPrizeList__item mGreen">
                    <strong>Контакт-сENTER 8 800 700 00 09</strong><br/>
                    Скажите оператору Контакт-cENTER, что Вы — участник Enter Prize!<br/>
                    И при оформлении заказа сообщите номер Фишки! 
                </li>
            </ul>
        </div>
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