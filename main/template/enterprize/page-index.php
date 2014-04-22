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

        <p class="enterPrizeDesc"><span class="enterPrizeDesc__text">Как получить больше фишек?</span></p>

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
                    Скажите оператору Контакт-cENTER, что Вы — участник Enter Prize!<br/>
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