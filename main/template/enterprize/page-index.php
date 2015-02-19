<?php
/**
 * @var $page             \View\DefaultLayout
 * @var $user             \Session\User
 * @var $enterpizeCoupons \Model\EnterprizeCoupon\Entity[]
 * @var $enterpizeCoupon  \Model\EnterprizeCoupon\Entity
 * @var $userCoupons      \Model\EnterprizeCoupon\Entity[]
 * @var $coupon           \Model\EnterprizeCoupon\Entity
 * @var $isCouponSent     bool
 * @var $products         \Model\Product\Entity[]
 * @var $form             \View\Enterprize\Form
 */

$isEnterprizeMember = $user->getEntity() && $user->getEntity()->isEnterprizeMember();
$helper = new \Helper\TemplateHelper();
?>

<div class="ep-head clearfix">
    <h1 class="ep-logo">Enter Prize</h1>

    <ul class="ep-head__desc">
        <li class="ep-head__desc__i">Выбери фишку — получи код скидки. Используй его при оформлении заказа.</li>
        <li class="ep-head__desc__i">Нажми на фишку, чтобы узнать условия и срок действия скидки. </li>
        <li class="ep-head__desc__i">Воспользоваться каждой фишкой можно один раз.</li>
    </ul>

    <? if (!$user->getEntity()): ?>
        <ul class="ep-head__control">
            <li class="ep-head__control__i"><a href="<?= \App::router()->generate('user.login') ?>" class="ep-head__control__i__log undrl jsEnterprizeAuthLink">Войти</a></li>
            <li class="ep-head__control__i js-ep-btn-hint-popup"><span class="ep-head__control__i__hint">Как стать участником?</span></li>
            <li class="ep-head__control__i"><a class="ep-head__control__i__lk undrl" href="/reklamnaya-akcia-enterprize">Правила участия в ENTER PRIZE</a></li>
        </ul>

        <div class="epHintPopup js-ep-hint-popup">
            <div class="epHintPopup_close js-ep-hint-popup-close"></div>

            <ol class="epHintPopup_list">
                <li class="epHintPopup_list_i">Выберите фишку на этой странице.</li>
                <li class="epHintPopup_list_i">Заполните анкету.</li>
                <li class="epHintPopup_list_i">Ловите Вашу первую фишку и подтверждение участия в e-mail и SMS.</li>
            </ol>
        </div>
    <? endif ?>

    <? if ($isEnterprizeMember): ?>
        <ul class="ep-head__control">
            <li class="ep-head__control__i ep-head_control__i--color"><span class="ep-head__control__i__log">Вы — игрок <span class="epTextLogo">Enter <span class="epTextLogo_colors">Prize</span></span></span></li>
            <li class="ep-head__control__i js-ep-btn-hint-popup"><span class="ep-head__control__i__hint">Как стать участником?</span></li>
            <li class="ep-head__control__i"><a class="ep-head__control__i__lk undrl" href="/reklamnaya-akcia-enterprize">Правила участия в ENTER PRIZE</a></li>
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
</div>

<? if ((bool)$isCouponSent && (bool)$enterpizeCoupon): ?>

    <div class="ep-selected-coupon clearfix">
        <div class="ep-selected-coupon__l ep-list">
            <div class="ep-list__i">
                <div class="ep-list__lk">
                    <span <? if ($enterpizeCoupon->getBackgroundImage()): ?> style="background-image: url(<?= $enterpizeCoupon->getBackgroundImage() ?>);"<? endif ?> class="ep-coupon">
                        <span class="ep-coupon__inner">
                            <? if ($enterpizeCoupon->getImage()): ?>
                                <span class="ep-coupon__ico"><img src="<?= $enterpizeCoupon->getImage() ?>" /></span>
                            <? endif ?>

                            <? if ($enterpizeCoupon->getName()): ?>
                                <span class="ep-coupon__desc"><?= $enterpizeCoupon->getName() ?></span>
                            <? endif ?>

                            <? if ($enterpizeCoupon->getPrice()): ?>
                                <span class="ep-coupon__price"><?= $page->helper->formatPrice($enterpizeCoupon->getPrice()) . (!$enterpizeCoupon->getIsCurrency() ? '%' : '') ?>
                                    <? if ($enterpizeCoupon->getIsCurrency()): ?>
                                        <span class="rubl">p</span>
                                    <? endif ?>
                                </span>
                            <? endif ?>
                        </span>
                    </span>
                </div>
            </div>
        </div>

        <div class="ep-selected-coupon__r">
            <div class="ep-selected-coupon__desc">
                Мы отправили код фишки на Ваш e-mail и мобильный.<br/>
                При заказе введите код в поле Код фишки, купон, промокод
            </div>

            <? if (isset($products) && !empty($products) && is_array($products)): ?>
                <div class="ep-selected-coupon__slider">
                    <a class="ep-selected-coupon__slider__lk" href="<?= $enterpizeCoupon->getLink() ?: '#' ?>">Выбрать товары с этой скидкой</a>

                    <?= $helper->render('product/__slider', [
                        'type'     => 'enterprize',
                        'title'    => '',
                        'products' => $products,
                        'count'    => null,
                        'limit'    => \App::config()->enterprize['itemsInSlider'],
                    ]) ?>
                </div>
            <? endif ?>
        </div>
    </div>
<? endif ?>

<? if ($isEnterprizeMember && $userCoupons): ?>
    <div class="ep-list ep-list--member">
        <h2 class="ep-tl">ВАШИ ФИШКИ <span class="orange">Enter</span> Prize</h2>

        <?= $helper->render('enterprize/_list', ['enterpizeCoupons' => $userCoupons, 'user' => $user, 'form' => $form]) ?>

        <div class="ep-hint ep-hint--position" style="display: none;">
            <div class="ep-hint__row">
                <div class="ta-r"><a href="" class="undrl" title="">Посмотреть все товары с фишкой</a></div>

                <div class="slideItem slideItem-7item slideItem--center">
                    <div class="slideItem_inn">
                        <ul class="slideItem_lst clearfix" style="width: 1430px; left: 0px;">
                            <li class="slideItem_i jsSliderItem" style="display: list-item;">
                                 <div class="slideItem_i__child">
                                    <div class="slideItem_imgw">
                                        <img alt="Держатель для планшетов Liberty Project C65H41" src="http://fs05.enter.ru/1/1/120/ff/269512.jpg" class="slideItem_img">
                                    </div>

                                    <div class="slideItem_n">
                                        Держатель для планшетов Liberty Project C65H41
                                    </div>

                                    <div class="slideItem_pr">
                                        <span class="td-lineth">1 122</span> <span class="rubl">p</span>
                                        <span class="slideItem_pr__sale">1 134 <span class="rubl">p</span></span>
                                    </div>

                                    <div class="btnBuy">
                                        <a class="jsBuyButton btnBuy__eLink" href="">Купить</a>
                                    </div>
                                </div>
                            </li>

                            <li class="slideItem_i jsSliderItem" style="display: list-item;">
                                 <div class="slideItem_i__child">
                                    <div class="slideItem_imgw">
                                        <img alt="Держатель для планшетов Liberty Project C65H41" src="http://fs05.enter.ru/1/1/120/ff/269512.jpg" class="slideItem_img">
                                    </div>

                                    <div class="slideItem_n">
                                        Держатель для планшетов Liberty Project C65H41
                                    </div>

                                    <div class="slideItem_pr">
                                        <span class="td-lineth">1 122</span> <span class="rubl">p</span>
                                        <span class="slideItem_pr__sale">1 134 <span class="rubl">p</span></span>
                                    </div>

                                    <div class="btnBuy">
                                        <a class="jsBuyButton btnBuy__eLink" href="">Купить</a>
                                    </div>
                                </div>
                            </li>

                            <li class="slideItem_i jsSliderItem" style="display: list-item;">
                                 <div class="slideItem_i__child">
                                    <div class="slideItem_imgw">
                                        <img alt="Держатель для планшетов Liberty Project C65H41" src="http://fs05.enter.ru/1/1/120/ff/269512.jpg" class="slideItem_img">
                                    </div>

                                    <div class="slideItem_n">
                                        Держатель для планшетов Liberty Project C65H41
                                    </div>

                                    <div class="slideItem_pr">
                                        <span class="td-lineth">1 122</span> <span class="rubl">p</span>
                                        <span class="slideItem_pr__sale">1 134 <span class="rubl">p</span></span>
                                    </div>

                                    <div class="btnBuy">
                                        <a class="jsBuyButton btnBuy__eLink" href="">Купить</a>
                                    </div>
                                </div>
                            </li>

                            <li class="slideItem_i jsSliderItem" style="display: list-item;">
                                <div class="slideItem_i__child">
                                    <div class="slideItem_imgw">
                                        <img alt="Держатель для планшетов Liberty Project C65H41" src="http://fs05.enter.ru/1/1/120/ff/269512.jpg" class="slideItem_img">
                                    </div>

                                    <div class="slideItem_n">
                                        Держатель для планшетов Liberty Project C65H41
                                    </div>

                                    <div class="slideItem_pr">
                                        <span class="td-lineth">1 122</span> <span class="rubl">p</span>
                                        <span class="slideItem_pr__sale">1 134 <span class="rubl">p</span></span>
                                    </div>

                                    <div class="btnBuy">
                                        <a class="jsBuyButton btnBuy__eLink" href="">Купить</a>
                                    </div>
                                </div>
                            </li>

                            <li class="slideItem_i jsSliderItem" style="display: list-item;">
                                <div class="slideItem_i__child">
                                    <div class="slideItem_imgw">
                                        <img alt="Держатель для планшетов Liberty Project C65H41" src="http://fs05.enter.ru/1/1/120/ff/269512.jpg" class="slideItem_img">
                                    </div>

                                    <div class="slideItem_n">
                                        Держатель для планшетов Liberty Project C65H41
                                    </div>

                                    <div class="slideItem_pr">
                                        <span class="td-lineth">1 122</span> <span class="rubl">p</span>
                                        <span class="slideItem_pr__sale">1 134 <span class="rubl">p</span></span>
                                    </div>

                                    <div class="btnBuy">
                                        <a class="jsBuyButton btnBuy__eLink" href="">Купить</a>
                                    </div>
                                </div>
                            </li>

                            <li class="slideItem_i jsSliderItem" style="display: list-item;">
                                <div class="slideItem_i__child">
                                    <div class="slideItem_imgw">
                                        <img alt="Держатель для планшетов Liberty Project C65H41" src="http://fs05.enter.ru/1/1/120/ff/269512.jpg" class="slideItem_img">
                                    </div>

                                    <div class="slideItem_n">
                                        Держатель для планшетов Liberty Project C65H41
                                    </div>

                                    <div class="slideItem_pr">
                                        <span class="td-lineth">1 122</span> <span class="rubl">p</span>
                                        <span class="slideItem_pr__sale">1 134 <span class="rubl">p</span></span>
                                    </div>

                                    <div class="btnBuy">
                                        <a class="jsBuyButton btnBuy__eLink" href="">Купить</a>
                                    </div>
                                </div>
                            </li>

                            <li class="slideItem_i jsSliderItem" style="display: list-item;">
                                <div class="slideItem_i__child">
                                    <div class="slideItem_imgw">
                                        <img alt="Держатель для планшетов Liberty Project C65H41" src="http://fs05.enter.ru/1/1/120/ff/269512.jpg" class="slideItem_img">
                                    </div>

                                    <div class="slideItem_n">
                                        Держатель для планшетов Liberty Project C65H41
                                    </div>

                                    <div class="slideItem_pr">
                                        <span class="td-lineth">1 122</span> <span class="rubl">p</span>
                                        <span class="slideItem_pr__sale">1 134 <span class="rubl">p</span></span>
                                    </div>

                                    <div class="btnBuy">
                                        <a class="jsBuyButton btnBuy__eLink" href="">Купить</a>
                                    </div>
                                </div>
                            </li>

                            <li class="slideItem_i jsSliderItem" style="display: list-item;">
                                <div class="slideItem_i__child">
                                    <div class="slideItem_imgw">
                                        <img alt="Держатель для планшетов Liberty Project C65H41" src="http://fs05.enter.ru/1/1/120/ff/269512.jpg" class="slideItem_img">
                                    </div>

                                    <div class="slideItem_n">
                                        Держатель для планшетов Liberty Project C65H41
                                    </div>

                                    <div class="slideItem_pr">
                                        <span class="td-lineth">1 122</span> <span class="rubl">p</span>
                                        <span class="slideItem_pr__sale">1 134 <span class="rubl">p</span></span>
                                    </div>

                                    <div class="btnBuy">
                                        <a class="jsBuyButton btnBuy__eLink" href="">Купить</a>
                                    </div>
                                </div>
                            </li>

                            <li class="slideItem_i jsSliderItem" style="display: list-item;">
                                <div class="slideItem_i__child">
                                    <div class="slideItem_imgw">
                                        <img alt="Держатель для планшетов Liberty Project C65H41" src="http://fs05.enter.ru/1/1/120/ff/269512.jpg" class="slideItem_img">
                                    </div>

                                    <div class="slideItem_n">
                                        Держатель для планшетов Liberty Project C65H41
                                    </div>

                                    <div class="slideItem_pr">
                                        <span class="td-lineth">1 122</span> <span class="rubl">p</span>
                                        <span class="slideItem_pr__sale">1 134 <span class="rubl">p</span></span>
                                    </div>

                                    <div class="btnBuy">
                                        <a class="jsBuyButton btnBuy__eLink" href="">Купить</a>
                                    </div>
                                </div>
                            </li>

                            <li class="slideItem_i jsSliderItem" style="display: list-item;">
                                <div class="slideItem_i__child">
                                    <div class="slideItem_imgw">
                                        <img alt="Держатель для планшетов Liberty Project C65H41" src="http://fs05.enter.ru/1/1/120/ff/269512.jpg" class="slideItem_img">
                                    </div>

                                    <div class="slideItem_n">
                                        Держатель для планшетов Liberty Project C65H41
                                    </div>

                                    <div class="slideItem_pr">
                                        <span class="td-lineth">1 122</span> <span class="rubl">p</span>
                                        <span class="slideItem_pr__sale">1 134 <span class="rubl">p</span></span>
                                    </div>

                                    <div class="btnBuy">
                                        <a class="jsBuyButton btnBuy__eLink" href="">Купить</a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="slideItem_btn slideItem_btn-prv mDisabled jsRecommendedSliderNav"></div>
                    <div class="slideItem_btn slideItem_btn-nxt jsRecommendedSliderNav"></div>
                </div>
            </div>
        </div>
    </div>
<? endif ?>

<h2 class="ep-tl">ВЫБЕРИТЕ ФИШКУ <span class="orange">Enter</span> Prize</h2>

<? /* if ((bool)$isCouponSent): ?>
    <?= $page->render('enterprize/_contentComplete') ?>
<? endif  */ ?>

<? /* if (!$user->getEntity()): ?>
    <h3 class="epListTitle">ФИШКИ <span class="orange">Enter</span> Prize</h3>
<? endif ?>

<? if ($isEnterprizeMember): ?>
    <h3 class="epListTitle">выбирайте еще фишки</h3>
<? endif */?>

<div class="ep-list ep-list--width">
    <?= $helper->render('enterprize/_list', ['enterpizeCoupons' => $enterpizeCoupons, 'user' => $user, 'form' => $form]) ?>
</div>

<script id="tplEnterprizeForm" type="text/html" data-partial="<?= $helper->json([]) ?>">
    <?= file_get_contents(\App::config()->templateDir . '/enterprize/form.mustache') ?>
</script>