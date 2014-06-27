<?php
/**
 * @var $page             \View\DefaultLayout
 * @var $user             \Session\User
 * @var $enterpizeCoupons \Model\EnterprizeCoupon\Entity[]
 * @var $enterpizeCoupon  \Model\EnterprizeCoupon\Entity
 * @var $isCouponSent     bool
 */

$isEnterprizeMember = $user->getEntity() && $user->getEntity()->isEnterprizeMember();
?>

<div class="enterPrize">
    
    <header class="epHeader clearfix">
        <h1 class="epHeader_logo">Enter Prize</h1>

        <? if (!$user->getEntity()): ?>
            <ul class="epHeader_controls">
                <li class="epHeader_controls_item epHeader_controls_item-colors"><a href="<?= \App::router()->generate('user.login') ?>" class="jsEnterprizeAuthLink">Войти</a></li>
                <li class="epHeader_controls_item epHeader_controls_item-border js-ep-btn-hint-popup">Как стать участником?</li>
            </ul>

            <div class="epHintPopup js-ep-hint-popup">
                <div class="epHintPopup_close js-ep-hint-popup-close"></div>

                <ol class="epHintPopup_list">
                    <li class="epHintPopup_list_item">Выберите фишку на этой странице.</li>
                    <li class="epHintPopup_list_item">Заполните анкету.</li>
                    <li class="epHintPopup_list_item">Ловите Вашу первую фишку и подтверждение участия в e-mail и SMS.</li>
                </ol>
            </div>
        <? endif ?>

        <? if ($isEnterprizeMember): ?>
            <ul class="epHeader_controls">
                <li class="epHeader_controls_item epHeader_controls_item-colors">Вы — игрок <span class="epTextLogo">Enter <span class="epTextLogo_colors">Prize</span></span></li>
                <li class="epHeader_controls_item">Теперь любые фишки со скидками - Ваши!</li>
            </ul>
        <? endif ?>

        <? if ((bool)$isCouponSent): ?>
            <div class="popup" id="enterprize-info-block">
                <div class="popupbox">
                    <div class="font18 pb18">Вы можете заказать прямо сейчас любой товар Enter c фишкой, которую Вы получили по e-mail и в SMS. Или выбрать еще фишки <a href="#" class="closePopup">ЗДЕСЬ!</a></div>
                </div>
                <p style="text-align:center"><a href="#" class="closePopup bBigOrangeButton">OK</a></p>
            </div>
        <? endif ?>
    </header>

    <? /*if ($isEnterprizeMember): ?>
        <div class="epSelectFishka clearfix">
            <div class="epSelectFishka_left enterPrize__list">
                <div class="enterPrize__list__item mOrange">
                    <div class="enterPrize__list__link">
                        <span style="background-image: url(http://content.enter.ru/wp-content/uploads/2014/03/fishka_fuksiya_b1.png);" class="cuponImg">
                            <span class="cuponImg__inner">
                                <span class="cuponIco"><img src="http://content.enter.ru/wp-content/uploads/2014/03/electronica.png"></span>

                                <span class="cuponDesc">Фотокамеры SONY</span>

                                <span class="cuponPrice">500 <span class="rubl">p</span></span>
                            </span>
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="epSelectFishka_right">
                <div class="epSelectFishka_desc">
                    Мы отправили код фишки на Ваш e-mail и мобильный.<br/>
                    При заказе введите код в поле Код фишки, купон, промокод
                </div>

                <div class="epSelectFishka_slider">
                    <a class="epSelectFishka_slider_link" href="">Выбрать товары с этой скидкой</a>

                    <div class="bSlider">
                        <div class="bSlider__eInner">
                            <ul class="bSlider__eList clearfix" style="width: 480px; left: 0px;">
                                <li class="bSlider__eItem jsSliderItem" style="display: list-item;">
                                    <div class="product__inner">
                                        <a href="" class="productImg">
                                            <img alt="" src="http://fs09.enter.ru/1/1/120/91/66457.jpg">
                                        </a>
                                        <div class="productName"><a href="">Карта памяти PS Vita Memory Card 8 ГБ (PCH-Z081)</a></div>
                                        <div class="productPrice"><span class="price">1 199 <span class="rubl">p</span></span></div>

                                        <div class="bWidgetBuy__eBuy btnBuy">
                                            <a class="id-cartButton-product-31128 btnBuy__eLink mShopsOnly" href="/cart/one-click/add-product/31128">Резерв</a>
                                        </div>
                                    </div>
                                </li>
                                
                                <li class="bSlider__eItem jsSliderItem" style="display: list-item;">
                                    <div class="product__inner">
                                        <a href="" class="productImg">
                                            <img alt="" src="http://fs09.enter.ru/1/1/120/91/66457.jpg">
                                        </a>
                                        <div class="productName"><a href="">Карта памяти PS Vita Memory Card 8 ГБ (PCH-Z081)</a></div>
                                        <div class="productPrice"><span class="price">1 199 <span class="rubl">p</span></span></div>

                                        <div class="bWidgetBuy__eBuy btnBuy">
                                            <a class="id-cartButton-product-31128 btnBuy__eLink mShopsOnly" href="/cart/one-click/add-product/31128">Резерв</a>
                                        </div>
                                    </div>
                                </li>

                                <li class="bSlider__eItem jsSliderItem" style="display: list-item;">
                                    <div class="product__inner">
                                        <a href="" class="productImg">
                                            <img alt="" src="http://fs09.enter.ru/1/1/120/91/66457.jpg">
                                        </a>
                                        <div class="productName"><a href="">Карта памяти PS Vita Memory Card 8 ГБ (PCH-Z081)</a></div>
                                        <div class="productPrice"><span class="price">1 199 <span class="rubl">p</span></span></div>

                                        <div class="bWidgetBuy__eBuy btnBuy">
                                            <a class="id-cartButton-product-31128 btnBuy__eLink mShopsOnly" href="/cart/one-click/add-product/31128">Резерв</a>
                                        </div>
                                    </div>
                                </li>

                                <li class="bSlider__eItem jsSliderItem" style="display: list-item;">
                                    <div class="product__inner">
                                        <a href="" class="productImg">
                                            <img alt="" src="http://fs09.enter.ru/1/1/120/91/66457.jpg">
                                        </a>
                                        <div class="productName"><a href="">Карта памяти PS Vita Memory Card 8 ГБ (PCH-Z081)</a></div>
                                        <div class="productPrice"><span class="price">1 199 <span class="rubl">p</span></span></div>

                                        <div class="bWidgetBuy__eBuy btnBuy">
                                            <a class="id-cartButton-product-31128 btnBuy__eLink mShopsOnly" href="/cart/one-click/add-product/31128">Резерв</a>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <div class="bSlider__eBtn mPrev mDisabled"><span></span></div>
                        <div class="bSlider__eBtn mNext mDisabled"><span></span></div>
                    </div>
                </div>
            </div>
        </div>
    <? endif*/ ?>

    <section class="epHead">
        <h2 class="epHead_title">Все, что вы хотели, со скидкой до 70%</h2>

        <ul class="epHead_list">
            <li class="epHead_list_item">Выберите фишку — получите <strong>код скидки</strong>. Используйте его при заказе.</li>
            <li class="epHead_list_item">Каждую неделю появляются <strong>новые фишки</strong> в ограниченном количестве.</li>
            <li class="epHead_list_item">О самых интересных скидках мы сообщаем по <strong>e-mail</strong> и в соцсетях.</li>
        </ul>
    </section>

    <? if ((bool)$isCouponSent): ?>
        <?= $page->render('enterprize/_contentComplete') ?>
    <? endif ?>
    
    <? if (!$user->getEntity()): ?>
        <h3 class="epListTitle">Выбирайте вашу первую фишку</h3>
    <? endif ?>

    <? if ($isEnterprizeMember): ?>
        <h3 class="epListTitle">выбирайте еще фишки</h3>
    <? endif ?>

    <ul class="enterPrize__list clearfix">

        <? $i = 0; foreach ($enterpizeCoupons as $coupon): $i++ ?>

            <?
            $priceNumDecimals = false === strpos((string)$coupon->getPrice(), '.') ? 0 : 2;

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


            <? if ($coupon->isForMember() && !$isEnterprizeMember): // Только для игроков EnterPrize  ?>
                <li class="<?= !empty($itemClass) ? "$itemClass " : '' ?>mMembers">
                    <div class="enterPrize__list__link">
                        <span class="cuponImg"<? if ($coupon->getBackgroundImage()): ?> style="background-image: url(<?= $coupon->getBackgroundImage() ?>);"<? endif ?>>
                            <span class="cuponImg__inner">
                                <? if ($coupon->getImage()): ?>
                                    <span class="cuponIco"><img src="<?= $coupon->getImage() ?>" /></span>
                                <? endif ?>

                                <? if ($coupon->getName()): ?>
                                    <span class="cuponDesc"><?= $coupon->getName() ?></span>
                                <? endif ?>

                                <? if ($coupon->getPrice()): ?>
                                    <span class="cuponPrice"><?= $page->helper->formatPrice($coupon->getPrice(), $priceNumDecimals) . (!$coupon->getIsCurrency() ? '%' : '') ?>
                                        <? if ($coupon->getIsCurrency()): ?>
                                            <span class="rubl">p</span>
                                        <? endif ?>
                                    </span>
                                <? endif ?>
                            </span>
                        </span>

                        <span class="cuponImgHover">
                            <span class="cuponText">Только<br/> для игроков<br/> <span class="epTextLogo">Enter <span class="epTextLogo_colors">Prize</span></span></span>
                        </span>
                    </div>
                </li>

            <? else: ?>
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
                                    <span class="cuponPrice"><?= $page->helper->formatPrice($coupon->getPrice(), $priceNumDecimals) . (!$coupon->getIsCurrency() ? '%' : '') ?>
                                        <? if ($coupon->getIsCurrency()): ?>
                                            <span class="rubl">p</span>
                                        <? endif ?>
                                    </span>
                                <? endif ?>
                            </span>
                        </span>

                        <span class="cuponImgHover">
                            <span class="cuponBtn">Получить</span>
                        </span>
                    </a>
                </li>
            <? endif ?>
        <? endforeach ?>
    </ul>

    <p class="rulesEP"><a href="/reklamnaya-akcia-enterprize">Правила участия в ENTER PRIZE</a></p>
</div>