<?php
$f = function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product,
    $videoHtml, $properties3D, $reviewsData, $creditData, $isKit, $buySender, $buySender2, $request
){

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

    <ul class="product-card-assure">
        <li class="product-card-assure__i">
            <div class="product-card-assure__l">
                <img class="product-card-assure__img" src="/styles/product/img/pandora.png">
            </div>

            <span class="product-card-assure__r">Гарантия подлинности и качества</span>
        </li>

        <li class="product-card-assure__i">
            <div class="product-card-assure__l">
                <img class="product-card-assure__img" src="/styles/product/img/jewelery-guar.png">
            </div>

            <span class="product-card-assure__r">Обмен в течение 30 дней<br/>Возврат по гарантии в течение 1 года</span>
        </li>
    </ul>

    <div class="product-card-sharing-list">
        <!-- AddThis Button BEGIN -->
        <div class="addthis_toolbox addthis_default_style mt15 ">
            <a class="addthis_button_facebook"></a>
            <a class="addthis_button_twitter"></a>
            <a class="addthis_button_vk"></a>
            <a class="addthis_button_compact"></a>
            <a class="addthis_counter addthis_bubble_style"></a>
        </div>
        <script type="text/javascript">var addthis_config = {"data_track_addressbar":true, ui_language: "ru"};</script>
        <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-51b040940ada4cd1&domready=1"></script>
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
    <div class="product-card-action i-info" style="display: none">
        <span class="product-card-action__tx i-info__tx">Акция действует<br>ещё 1 день 22:11:07</span>
        <i class="product-card-action__icon i-product i-product--info-warn i-info__icon"></i>

        <!-- попап - подробности акции, чтобы показать/скрыть окно необходимо добавить/удалить класс info-popup--open -->
        <div class="info-popup info-popup--action info-popup--open" style="display: none">
            <i class="closer">×</i>
            <div class="info-popup__inn">
                <a href="" title=""><img src="/styles/product/img/trust-sale.png" alt=""></a>
            </div>
        </div>
        <!--/ попап - подробности акции -->

        <!-- попап - подробности акции, чтобы показать/скрыть окно необходимо добавить/удалить класс info-popup--open -->
        <div class="action-hint info-popup info-popup--action info-popup--open" style="display: none">
            <i class="closer">×</i>
            <div class="info-popup__inn">
                <div class="action-hint__desc">
                    <img class="action-hint__img" src="/styles/product/img/shild-124x38.png">

                    <a class="action-hint__lk">Черная пятница в Enter</a>
                </div>
            </div>
        </div>
        <!--/ попап - подробности акции -->
    </div>

    <? if ($product->getPriceOld()) : ?>
        <div class="product-card-old-price">
            <span class="product-card-old-price__inn"><?= $helper->formatPrice($product->getPriceOld()) ?></span> <span class="rubl">p</span>
        </div>
    <? endif ?>

    <!-- цена товара -->
    <div class="product-card-price i-info">
        <span class="product-card-price__val i-info__tx"><?= $helper->formatPrice($product->getPrice()) ?><span class="rubl">p</span></span>
        <i class="i-product i-product--info-normal i-info__icon"></i>

        <!-- попап - узнатьо снижении цены, чтобы показать/скрыть окно необходимо добавить/удалить класс info-popup--open -->
        <div class="best-price-popup info-popup info-popup--best-price info-popup--open" style="display: none">
            <i class="closer">×</i>

            <strong class="best-price-popup__tl">Узнать о снижении цены</strong>

            <p class="best-price-popup__desc">Вы получите письмо,<br>когда цена станет ниже 9 000 <span class="rubl">p</span></p>

            <input class="best-price-popup__it textfield" placeholder="Ваш email" value="">

            <input type="checkbox" name="subscribe" id="subscribe" value="1" autocomplete="off" class="customInput customInput-defcheck jsCustomRadio js-customInput jsSubscribe" checked="">
            <label class="best-price-popup__check customLabel customLabel-defcheck mChecked" for="subscribe">Подписаться на рассылку и получить купон со скидкой 300 рублей на следующую покупку</label>

            <div style="text-align: center">
                <a href="#" class="best-price-popup__btn btn-type btn-type--buy">Сохранить</a>
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