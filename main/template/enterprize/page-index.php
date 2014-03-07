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

    <p class="enterPrizeDesc">Уже в ENTER PRIZE? <a href="">Войди и получи ещё скидки</a></p>

    <? if ($isEnterprizeMember): ?>
        <p class="enterPrizeDesc"><span class="enterPrizeDesc__text">Как ещё получать фишки?</span></p>

        <div class="enterPrizeListWrap">
            <ul class="enterPrizeList">
                <li class="enterPrizeList__item mBlue">
                    <strong>Сайт www.enter.ru</strong><br/>
                    Всегда входи в личный кабинет.<br/>
                    Заказывай товары как обычно.
                </li>

                <li class="enterPrizeList__item mOrange">
                    <strong>Розничные магазины ENTER</strong><br/>
                    Входи в личный кабинет в терминале.<br/>
                    Заказывай товары через терминал.
                </li>

                <li class="enterPrizeList__item mGreen">
                    <strong>Контакт-сENTER 8 800 700 00 09</strong><br/>
                    Скажи оператору Контакт-cENTER, что ты — участник ENTER PRIZE!<br/>
                    Оператор поможет оформить заказ.
                </li>
            </ul>

            <div class="enterPrizeFinish">Лови номер фишки в чеке после оплаты заказа!</div>
        </div>

        <p class="enterPrizeDesc"><span class="enterPrizeDesc__text">Как играть фишкамии получать скидки?</span></p>

        <div class="enterPrizeListWrap">
            <div class="enterPrizeListTitle">Как получить скидку?</div>

            <ul class="enterPrizeList">
                <li class="enterPrizeList__item mBlue">
                    <strong>Сайт www.enter.ru</strong><br/>
                    Входи в личный кабинет на www.enter.ru!<br/>
                    При оформлении Заказа в поле КУПОН или ФИШКА вводи номер фишки!
                </li>

                <li class="enterPrizeList__item mOrange">
                    <strong>Розничные магазины ENTER</strong><br/>
                    Скажи сотруднику магазина, что ты — участник ENTER PRIZE!<br/>
                    И сообщи номер Фишки при оплате заказа!
                </li>

                <li class="enterPrizeList__item mGreen">
                    <strong>Контакт-сENTER 8 800 700 00 09</strong><br/>
                    Скажи оператору Контакт-cENTER, что ты — участник ENTER PRIZE!<br/>
                    И при оформлении заказа сообщи номер Фишки!
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
            if ($coupon->isInformationOnly()) {
                if ($coupon->getDescriptionToken()) {
                    $couponLink = $page->url('content', ['token' => $coupon->getDescriptionToken()]);
                } else {
                    $couponLink = null;
                }
            }
            ?>

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
</div>