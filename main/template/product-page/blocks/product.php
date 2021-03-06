<?php

use \Model\Product\Label;

$f = function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product,
    $trustfactors, $videoHtml, $properties3D, $reviewsData, $creditData, $isKit, $buySender, $buySender2, $request, $favoriteProductsByUi, $shopStates = []
){
    // отдельная картинка для шильдика
    $labelImage = $product->getLabel() ? $product->getLabel()->getImageUrlWithTag(Label::MEDIA_TAG_RIGHT_SIDE) : null;
    $labelName = $product->getLabel() ? $product->getLabel()->getName() : '';

$modelName = $product->model && $product->model->property ? $product->model->property->name : null;
$price =
    ($product->getRootCategory() && $product->getRootCategory()->getPriceChangePercentTrigger())
    ? round($product->getPrice() * $product->getRootCategory()->getPriceChangePercentTrigger())
    : 0
;

$mainPropertyCount = count($product->getMainProperties());

?>

<div class="product-card clearfix">

        <!-- блок с фото -->
        <?= $helper->render('product-page/blocks/photo', ['product' => $product, 'videoHtml' => $videoHtml, 'properties3D' => $properties3D, 'shopStates' => $shopStates]) ?>
<!--/ блок с фото -->

<!-- краткое описание товара -->
<div class="product-card__c">

    <?= $helper->render('product-page/blocks/reviews.short', ['reviewsData' => $reviewsData]) ?>

    <?= $helper->render('product-page/blocks/variants', ['product' => $product, 'trustfactors' => $trustfactors]) ?>

    <? if ($product->getTagline()) : ?>
    <p class="product-card-desc"><?= $product->getTagline() ?></p>
    <? endif ?>

    <dl class="product-card-prop">
        <? $i = 0; foreach ($product->getMainProperties() as $property) : $i++ ?>
            <? if ($i == 5 && $mainPropertyCount >= 5 && $product->getSecondaryGroupedProperties()) : ?>

                    <a class="product-card-prop__lk" href="#more" onclick="$('.jsScrollSpyMoreLink').trigger('click'); return false;">Все характеристики</a>

            <? break; endif; ?>
            <? if ($property->getName() == $modelName) continue ?>
            <dt class="product-card-prop__i product-card-prop__i--name"><?= $property->getName() ?></dt>
            <dd class="product-card-prop__i product-card-prop__i--val"><?= $property->getStringValue() ?></dd>
        <? endforeach ?>
    </dl>

    <?= $helper->render('product-page/blocks/trustfactors', ['trustfactors' => $trustfactors]) ?>

    <div class="product-card__addings">
        <ul class="pay-system-list">
            <li class="pay-system-list__i"><img class="pay-system-list__img" src="/styles/order-new/img/payment/pay-card-g.png"></li>
            <li class="pay-system-list__i"><img class="pay-system-list__img" src="/styles/order-new/img/payment/pay-yandex-g.png"></li>
            <li class="pay-system-list__i"><img class="pay-system-list__img" src="/styles/order-new/img/payment/pay-webmoney-g.png"></li>
            <li class="pay-system-list__i"><img class="pay-system-list__img" src="/styles/order-new/img/payment/pay-qiwi-g.png"></li>
            <li class="pay-system-list__i"><img class="pay-system-list__img" src="/styles/order-new/img/payment/pay-psb-g.png"></li>
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

            <script type="text/javascript">var addthis_config = {"data_track_addressbar":false, ui_language: "ru"};</script>
            <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-51b040940ada4cd1&domready=1"></script>
            <? /* <!-- AddThis Button END --> */ ?>
        </div>
    </div>

</div>
<!--/ краткое описание товара -->

<!-- купить -->
<div class="product-card__r">
    <? if (($label = $product->getLabel()) && $label->expires && !$label->isExpired() && $label->showExpirationDate && !$product->needPrepayment) : ?>
        <?= $label->url ? '<a href="' . $label->url .'" style="cursor: pointer">' : '' ?>
        <!-- Шильдик с правой стороны -->
        <div class="product-card-action i-info <?= !$labelImage ? 'product-card-action-no-image' : ''?>">

            <span class="product-card-action__tx i-info__tx js-countdown"
                  data-expires="<?= $label->expires->format('U') ?>">Акция действует<br>ещё <span class="js-countdown-out"><?= $label->getDateDiffString() ?></span>
            </span>

            <? if ($labelImage) : ?>
                <i class="product-card-action__icon i-product i-product--info-warn i-info__icon jsProductCardNewLabelInfo"></i>

                <!-- попап - подробности акции, чтобы показать/скрыть окно необходимо добавить/удалить класс info-popup--open -->
                <div class="info-popup info-popup--action jsProductCardNewLabelPopup">
                    <i class="closer jsProductCardNewLabelInfo">×</i>
                    <div class="info-popup__inn">
                        <img src="<?= $labelImage ?>" alt="<?= \App::helper()->escape($labelName) ?>">
                    </div>
                </div>
                <!--/ попап - подробности акции -->
            <? endif ?>
        </div>
        <?= $label->url ? '</a>' : '' ?>
    <? endif ?>
    <div class="product-card-price i-info <? if ($product->needPrepayment) : ?>product-card-price_prepayment<? endif ?>">
    <? if ($product->getPriceOld()) : ?>
        <div class="product-card-old-price">
            <span class="product-card-old-price__inn"><?= $helper->formatPrice($product->getPriceOld()) ?></span> <span class="rubl">p</span>
        </div>
    <? endif ?>

    <!-- цена товара -->

        <span class="product-card-price__val i-info__tx"><?= $helper->formatPrice($product->getPrice()) ?><span class="rubl">p</span></span>

        <i class="i-product i-product--info-normal i-info__icon js-lowPriceNotifier-opener js-lowPriceNotifier" data-values="<?= $helper->json([
            'price' => $price && $price < $product->getPrice() ? $helper->formatPrice($price) : null,
            'actionChannelName' => '',
            'userOfficeUrl' => $helper->url(\App::config()->user['defaultRoute']),
            'submitUrl' => $helper->url('product.notification.lowerPrice', ['productId' => $product->getId()]),
        ]) ?>"></i>

        <script id="tpl-lowPriceNotifier-popup" type="text/html" data-partial="<?= $helper->json([]) ?>">
            <?= file_get_contents(\App::config()->templateDir . '/product-page/blocks/lowPricePopup.mustache') ?>
        </script>
        <? if ($product->needPrepayment) : ?>
            <div class="product-card-price__warning-prepayment">
                <img src="/styles/common/img/ya-logo.png" alt="Яндекс касса">
                <span>Обязательная предоплата</span>
            </div>
        <? endif ?>
    </div>
    <!--/ цена товара -->
    
    <div class="buy-online">
        <?= $helper->render('cart/__button-product', [
            'product'  => $product,
            'onClick'  => isset($addToCartJS) ? $addToCartJS : null,
            'sender'   => $buySender + [
                'from' => preg_filter('/\?+?.*$/', '', $request->server->get('HTTP_REFERER')) == null ? $request->server->get('HTTP_REFERER') : preg_filter('/\?+?.*$/', '', $request->server->get('HTTP_REFERER')) // удаляем из REFERER параметры
            ],
            'sender2' => $buySender2,
            'noUpdate'  => true,
            'location' => 'product-card',
            'inShowroomAsButton' => false,
            'shopStates' => $shopStates,
        ]) // Кнопка купить ?>
    </div>

    <? if ($creditData['creditIsAllowed'] && !count($product->getPartnersOffer()) && !$product->isInShopOnly()): ?>
        <!-- купить в кредит -->
        <a
            class="buy-on-credit btn-type btn-type--normal btn-type--longer jsProductCreditButton"
            href="<?= $helper->url('cart.product.setList', ['products' => [['ui' => $product->ui, 'quantity' => '+1', 'up' => '1']]]) ?>"
            data-credit='<?= (isset($creditData['creditData']) ? $creditData['creditData'] : '') ?>'
            data-target=".<?= \View\Id::cartButtonForProduct($product->getId()) ?>"
        >
            <span class="buy-on-credit__tl">Купить в кредит</span>
            <span class="buy-on-credit__tx">от <mark class="buy-on-credit__mark jsProductCreditPrice">...</mark>&nbsp;&nbsp;<span class="rubl">p</span> в месяц</span>
        </a>
        <!--/ купить в кредит -->
    <? endif ?>

    <div class="js-showTopBar"></div>

    <!-- сравнить, добавить в виш лист -->
    <ul class="product-card-tools">
        <? if ($product->isOneClickAvailable()): ?>
        <li class="product-card-tools__i product-card-tools__i--onclick">
            <?= $helper->render('cart/__button-product-oneClick', [
                'product' => $product,
                'sender'  => $buySender,
                'sender2' => $buySender2,
                'value' => 'Купить в 1 клик',
                'location'  => 'product-card',
            ]) ?>
        </li>
        <? endif ?>

        <li class="product-card-tools__i product-card-tools__i--compare js-compareProduct"
            data-bind="compareButtonBinding: compare"
            data-id="<?= $product->getId() ?>"
            data-type-id="<?= $product->getType() ? $product->getType()->getId() : null ?>">
            <a
                id="<?= ('compareButton-' . $product->getId()) ?>"
                href="<?= \App::router()->generateUrl('compare.add', ['productId' => $product->getId(), 'location' => 'product']) ?>"
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

    <? if (\App::config()->product['deliveryCalc'] && $product->getIsBuyable()): ?>
        <?= $helper->render('product-page/blocks/delivery', ['product' => $product]) ?>
    <? endif ?>

</div>
<!--/ купить -->
</div>

<? }; return $f;