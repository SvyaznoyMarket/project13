<?php

use \Model\Product\Label;

$f = function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product,
    $trustfactors, $videoHtml, $properties3D, $reviewsData, $creditData, $isKit, $buySender, $buySender2, $request
){

$coupon = $product->coupons ? $product->getBestCoupon() : null;

?>

<div class="product-card clearfix">

        <!-- блок с фото -->
        <?= $helper->render('product-page/blocks/photo', ['product' => $product, 'videoHtml' => $videoHtml, 'properties3D' => $properties3D ]) ?>
<!--/ блок с фото -->

<!-- краткое описание товара -->
<div class="product-card__c">

    <?= $helper->render('product-page/blocks/reviews.short', ['reviewsData' => $reviewsData]) ?>

    <?= $helper->render('product-page/blocks/variants', ['product' => $product]) ?>

    <p class="product-card-desc"><?= $product->getAnnounce() ?></p>

    <dl class="product-card-prop">
        <? foreach ($product->getMainProperties() as $property) : ?>
            <dt class="product-card-prop__i product-card-prop__i--name"><?= $property->getName() ?></dt>
            <dd class="product-card-prop__i product-card-prop__i--val"><?= $property->getStringValue() ?></dd>
        <? endforeach ?>
    </dl>

    <?= $helper->render('product-page/blocks/trustfactors', ['trustfactors' => $trustfactors]) ?>

    <div class="product-card-sharing-list">
        <!-- AddThis Button BEGIN -->
        <div class="addthis_toolbox addthis_default_style mt15 ">
            <a class="addthis_button_facebook"></a>
            <a class="addthis_button_twitter"></a>
            <a class="addthis_button_vk"></a>
            <a class="addthis_button_compact"></a>
            <a class="addthis_counter addthis_bubble_style"></a>
        </div>
        <script type="text/javascript">var addthis_config = { data_track_addressbar:true, ui_language: "ru" };</script>
        <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-51b040940ada4cd1&domready=1" async></script>
        <!-- AddThis Button END -->
    </div>

    <ul class="pay-system-list">
        <li class="pay-system-list__i"><i class="pay-system-list__icon i-paysystem-icon i-paysystem-icon--visa"></i></li>
        <li class="pay-system-list__i"><i class="pay-system-list__icon i-paysystem-icon i-paysystem-icon--mastercard"></i></li>
        <li class="pay-system-list__i"><i class="pay-system-list__icon i-paysystem-icon i-paysystem-icon--psb"></i></li>
    </ul>
</div>
<!--/ краткое описание товара -->

<!-- купить -->
<div class="product-card__r">

    <? if ($coupon) : ?>
        <div class="ep js-pp-ep-fishka">
           <div class="ep__fishka">%</div>
           <div class="ep__desc">Фишка со скидкой <?= sprintf('%u%s', $coupon->getPrice(), $coupon->getIsCurrency() ? '<span class="rubl">p</span>' : '%') ?> на этот товар</div>
        <!-- всплывающее окно у фишки -->
        <div class="ep-hint ep-hint-card ep-hint--2col  js-enterprize-coupon-hint col-3">
            <div class="ep-hint__closer clsr-light js-ep-hint-closer"></div>

            <div class="ep-hint__row clearfix js-ep-reg-form">
                <div class="ep-hint__col">
                    <p class="ep-hint__lvl1">
                        Фишка со скидкой <strong>300 <span class="rubl">p</span></strong>
                            на <strong>Все товары Enter</strong>
                    </p>

                    <p class="ep-hint__lvl2">Действует
                        c 30.01.2015
                        по 30.06.2015
                    </p>

                    <div class="ep-hint__lvl3">
                        Скидка по акции не суммируется со скидками по другим акциям ООО "Энтер"<br>
        Фишка не действует на электронику, бытовую технику, 
        <br>товары Tchibo и украшения Pandora<br>
        Фишка не действует на товары продавцов-партнеров


        <br>
                        <div>Минимальная сумма заказа 2000 <span class="rubl">p</span></div>
                    </div>



                    <p class="ep-hint__lvl4 m"><a href="" class="undrl js-ep-rules-toggle">Как воспользоваться кодом фишки и получить скидку?</a></p>

                    <ul class="ep-rules-lst js-ep-rules-list">
                        <li class="ep-rules-lst__i mBlue">
                            <strong>Сайт www.enter.ru</strong><br>
                            При оформлении заказа в поле "Ввести код скидки" введите номер фишки!
                        </li>

                        <li class="ep-rules-lst__i mOrange">
                            <strong>Розничные магазины ENTER</strong><br>
                            Сообщите номер фишки сотруднику магазина при оплате заказа!
                        </li>

                        <li class="ep-rules-lst__i mGreen">
                            <strong>Наш Контакт-сENTER</strong><br>
                            Позвоните по телефону 8-800-700-00-09 и сообщите номер заказа и фишки оператору
                        </li>
                    </ul>
                </div>

                <div class="ep-hint__col ep-hint__col--r">
                    <form class="form-reg jsEnterprizeForm" data-form="new-form" action="/enterprize/form/update?enterprizeToken=4f565391-a855-11e4-93f6-e4115baba634&amp;parent_ri=556f1aca46a0c" method="post">
                        <input type="hidden" name="user[guid]" value="4f565391-a855-11e4-93f6-e4115baba634">

                        <fieldset class="form-reg__fld">
                            <div class="form-reg__tl">МЫ ОТПРАВИМ КОД НА СКИДКУ В SMS И E-MAIL</div>

                            <div class="errtx-light js-global-error"></div>

                            <div class="txFld-w">
                                <input class="txFld-it" type="text" name="user[name]" id="" value="">
                                <label class="txFld-lbl" for=""><span class="txFld-lbl__str">*</span> Имя</label>
                            </div>

                            <div class="txFld-w">
                                <input class="txFld-it" type="text" placeholder="name@domain.ru" name="user[email]" id="" value="">
                                <label class="txFld-lbl" for=""><span class="txFld-lbl__str">*</span> E-mail</label>
                            </div>

                            <div class="txFld-hint">Получи номер фишки в смс</div>

                            <div class="txFld-w">
                                <input class="txFld-it js-phone-mask" type="text" placeholder="+7 (___) ___-__-__" data-mask="+7 (xxx) xxx-xx-xx" name="user[mobile]" value="">
                                <label class="txFld-lbl" for=""><span class="txFld-lbl__str"></span> Телефон</label>
                            </div>
                        </fieldset>

                        <fieldset class="form-reg__fld form-reg__sbsrb clearfix">
                            <div class="form-reg__sbsrb__i">
                                <input type="checkbox" name="user[isSubscribe]" checked="checked" id="isSubscribe" class="customInput customInput-orangecheck js-customInput">
                                <label for="subscribe" class="customLabel customLabel-orangecheck mChecked">Получить<br> рекламную рассылку</label>
                            </div>

                            <div class="form-reg__sbsrb__i fl-r">
                                <input type="checkbox" id="agree" name="user[agree]" class="customInput customInput-orangecheck js-customInput">
                                <label for="agree" class="customLabel customLabel-orangecheck">Согласен<br> <a class="agree-lk undrl" target="_blank" href="/reklamnaya-akcia-enterprize">с условиями оферты</a></label>
                            </div>
                        </fieldset>

                        <fieldset class="form-reg__fld form-reg__btn clearfix">
                            <div class="form-reg__login">
                                У Вас есть логин и пароль?<br>
                                <a href="/login" class="form-reg__login__lk undrl bAuthLink">Войти</a>
                            </div>

                            <button type="submit" class="btn-def fl-r">Зарегистрироваться</button>
                        </fieldset>
                    </form>
                </div>
            </div>

            <div class="ep-hint__row ep-hint__row--cmplt js-ep-reg-complete">
                <div class="ep-hint__cmplt-tl">
                    Спасибо!
                </div>
                <p class="ep-hint__cmplt-tx js-ep-reg-complete-text"></p>

            </div>


            
        </div>
<!--END всплывающее окно у фишки -->
        </div>


    <? endif; ?>

    <? if ($product->getLabel() && $product->getLabel()->expires && !$product->getLabel()->isExpired()) : ?>
        <!-- Шильдик с правой стороны -->
        <div class="product-card-action i-info">

            <span class="product-card-action__tx i-info__tx"
                  data-expires="<?= $product->getLabel()->expires->format('U') ?>">Акция действует<br>ещё <span><?= $product->getLabel()->getDateDiffString() ?></span>
            </span>

            <? if ($product->getLabel()->getImageUrlWithTag(Label::MEDIA_TAG_RIGHT_SIDE)) : ?>
                <i class="product-card-action__icon i-product i-product--info-warn i-info__icon"></i>

                <!-- попап - подробности акции, чтобы показать/скрыть окно необходимо добавить/удалить класс info-popup--open -->
                <div class="info-popup info-popup--action">
                    <i class="closer">×</i>
                    <div class="info-popup__inn">
                        <a href="" title=""><img src="<?= $product->getLabel()->getImageUrlWithTag(Label::MEDIA_TAG_RIGHT_SIDE) ?>" alt=""></a>
                    </div>
                </div>
                <!--/ попап - подробности акции -->
            <? endif ?>
        </div>
    <? endif ?>

    <? if ($product->getPriceOld()) : ?>
        <div class="product-card-old-price">
            <span class="product-card-old-price__inn"><?= $helper->formatPrice($product->getPriceOld()) ?></span> <span class="rubl">p</span>
        </div>
    <? endif ?>

    <!-- цена товара -->
    <div class="product-card-price i-info">
        <span class="product-card-price__val i-info__tx"><?= $helper->formatPrice($product->getPrice()) ?><span class="rubl">p</span></span>

        <i class="i-product i-product--info-normal i-info__icon jsBestPricePopupOpener"></i>

        <!-- попап - узнатьо снижении цены, чтобы показать/скрыть окно необходимо добавить/удалить класс info-popup--open -->
        <div class="best-price-popup info-popup info-popup--best-price jsBestPricePopup jsLowPriceNotifer">
            <i class="closer jsBestPricePopupCloser">×</i>

            <strong class="best-price-popup__tl">Узнать о снижении цены</strong>

            <p class="best-price-popup__desc">Вы получите письмо,<br>когда цена станет ниже
                <?= $helper->formatPrice($product->getPrice() * ($product->getMainCategory() ? $product->getMainCategory()->getPriceChangePercentTrigger() : 1)) ?>&nbsp;<span class="rubl">p</span></p>

            <input class="best-price-popup__it textfield jsLowerPriceEmailInput" placeholder="Ваш email" value="">
            <div class="jsLowerPriceError" style="color: #cb3735"></div>

            <input type="checkbox" name="subscribe" id="subscribe" value="1" autocomplete="off" class="customInput customInput-defcheck jsCustomRadio js-customInput jsSubscribe" checked="">
            <label class="best-price-popup__check customLabel customLabel-defcheck mChecked" for="subscribe">Подписаться на рассылку и получить купон со скидкой 300 рублей на следующую покупку</label>

            <div style="text-align: center">
                <a href="#" class="best-price-popup__btn btn-type btn-type--buy jsLowerPriceSubmitBtn"
                   data-url="<?= $helper->url('product.notification.lowerPrice', ['productId' => $product->getId()]) ?>"
                    >Сохранить</a>
            </div>
        </div>
        <!--/ попап - узнатьо снижении цены -->
    </div>
    <!--/ цена товара -->

    <!-- применить скидку -->
    <div class="product-card-discount-switch" style="display: none">
        <div class="product-card-discount-switch__i discount-switch">
            <input class="discount-switch__it" type="checkbox" name="" id="discount-switch">
            <label class="discount-switch__lbl" for="discount-switch"></label>
        </div>

        <span class="product-card-discount-switch__i product-card-discount-switch__i--tx">Скидка 10%</span>
        <img class="product-card-discount-switch__i product-card-discount-switch__img" src="/styles/product/img/i-fishka.png">
    </div>
    <!--/ применить скидку -->

    <div class="buy-online">
        <?= $helper->render('cart/__button-product', [
            'product'  => $product,
            'onClick'  => isset($addToCartJS) ? $addToCartJS : null,
            'sender'   => $buySender + [
                    'from' => preg_filter('/\?+?.*$/', '', $request->server->get('HTTP_REFERER')) == null ? $request->server->get('HTTP_REFERER') : preg_filter('/\?+?.*$/', '', $request->server->get('HTTP_REFERER')) // удаляем из REFERER параметры
                ],
            'sender2' => $buySender2,
            'location' => 'product-card',
        ]) // Кнопка купить ?>
    </div>

    <? if ($product->getPrice() >= \App::config()->product['minCreditPrice']) : ?>
        <!-- купить в кредит -->
        <a class="buy-on-credit btn-type btn-type--normal btn-type--longer jsProductCreditButton" href="" style="display: none"
           data-credit='<?= $creditData['creditData'] ?>'>
            <span class="buy-on-credit__tl">Купить в кредит</span>
            <span class="buy-on-credit__tx">от <mark class="buy-on-credit__mark jsProductCreditPrice">0</mark>&nbsp;&nbsp;<span class="rubl">p</span> в месяц</span>
        </a>
        <!--/ купить в кредит -->
    <? endif ?>

    <div class="js-showTopBar"></div>

    <!-- сравнить, добавить в виш лист -->
    <ul class="product-card-tools">
        <li class="product-card-tools__i product-card-tools__i--onclick">

            <? if (!count($product->getPartnersOffer()) && (!$isKit || $product->getIsKitLocked())): ?>
                <?= $helper->render('cart/__button-product-oneClick', [
                    'product' => $product,
                    'sender'  => $buySender,
                    'sender2' => $buySender2,
                    'value' => 'Купить в 1 клик'
                ]) // Покупка в один клик ?>
            <? endif ?>
        </li>

        <li class="product-card-tools__i product-card-tools__i--compare js-compareProduct"
            data-bind="compareButtonBinding: compare"
            data-id="<?= $product->getId() ?>"
            data-type-id="<?= $product->getType() ? $product->getType()->getId() : null ?>">
            <a id="<?= 'compareButton-' . $product->getId() ?>"
               href="<?= \App::router()->generate('compare.add', ['productId' => $product->getId(), 'location' => 'product']) ?>"
               class="product-card-tools__lk jsCompareLink"
               data-is-slot="<?= (bool)$product->getSlotPartnerOffer() ?>"
               data-is-only-from-partner="<?= $product->isOnlyFromPartner() ?>"
                >
                <i class="product-card-tools__icon i-tools-icon i-tools-icon--product-compare"></i>
                <span class="product-card-tools__tx">Сравнить</span>
            </a>
        </li>

        <li class="product-card-tools__i product-card-tools__i--wish">
            <?= $helper->render('product/__favoriteButton', ['product' => $product, 'favoriteProduct' => isset($favoriteProductsByUi[$product->getUi()]) ? $favoriteProductsByUi[$product->getUi()] : null]) ?>
        </li>
    </ul>
    <!--/ сравнить, добавить в виш лист -->

    <?= $helper->render('product-page/blocks/delivery', ['product' => $product]) ?>

</div>
<!--/ купить -->
</div>

<? }; return $f;