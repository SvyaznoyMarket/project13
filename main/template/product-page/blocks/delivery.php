<?php

/**
 * @param \Helper\TemplateHelper $helper
 * @param \Model\Product\Entity $product
 * @return string
 */
$f = function (
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product
) {

    if (!$product->delivery) return;

    $hasInCEnterShop = call_user_func(function() use($product) {
        foreach ($product->delivery->shops as $shop) {
            if (in_array($shop->getId(), [2, 204])) {
                return true;
            }
        }

        return false;
    });

    /** @var \Model\Product\Delivery\Delivery|null $deliveryPickup */
    $deliveryPickup = $product->delivery->getPickupWithMinDate() ?: null;
    /** @var \Model\Product\Delivery\Delivery|null $deliveryDelivery */
    $deliveryDelivery = $product->delivery->getDeliveryWithMinDate() ?: null;

    // проверка наличия способов получения товара
    if (!$deliveryPickup && !$deliveryDelivery) {
        return '';
    }

    $deliveryPickupCount = 0
        + (int)$product->delivery->hasEnterDelivery
        + (int)$product->delivery->hasPickpointDelivery
        + (int)$product->delivery->hasSvyaznoyDelivery
        + (int)$product->delivery->hasHermesDelivery
        + (int)$product->delivery->hasEurosetDelivery
    ;
?>
    <!-- в наличии -->
    <div <? if ($deliveryPickup): ?>class="jsShowDeliveryMap" style="cursor: pointer;"<? endif ?> data-product-ui="<?= $product->ui ?>">
        <? if ($hasInCEnterShop): ?>
            <div class="buy-now-incenter">
                Есть в cENTER
            </div>
        <? endif ?>

        <div class="buy-now-inshop">
            <? if ($deliveryPickup) : ?>
                <div class="buy-now-inshop__line jsDeliveryPickupAvailable">
                    <span class="buy-now-inshop__line-name">Самовывоз</span>
                    <span class="buy-now-inshop__mark">
                    <? if (!\App::abTest()->isHiddenDeliveryInterval()): ?>
                        <? if ($deliveryPickup->dateInterval): ?>
                            <span data-date="<?= $helper->json($deliveryPickup->dateInterval) ?>"><?= sprintf('%s %s' . (\App::config()->product['showDeliveryPrice'] ? ',' : ''), $deliveryPickup->dateInterval->from ? ('с ' . $deliveryPickup->dateInterval->from->format('d.m')) : '', $deliveryPickup->dateInterval->to ? (' по ' . $deliveryPickup->dateInterval->to->format('d.m')) : '') ?></span>
                        <? elseif ($deliveryPickup->dayRange): ?>
                            <span data-date="<?= $helper->json($deliveryPickup->getMinDate() ? $deliveryPickup->getMinDate()->date->format('Y-m-d') : null) ?>"><?= sprintf('%s-%s %s', $deliveryPickup->dayRange['from'], $deliveryPickup->dayRange['to'], $helper->numberChoice($deliveryPickup->dayRange['to'], ['день', 'дня', 'дней'])) ?></span>
                        <? else: ?>
                            <?= mb_strtolower($helper->humanizeDate($deliveryPickup->getMinDate()->date)) ?><? if (\App::config()->product['showDeliveryPrice']): ?>,<? endif ?>
                        <? endif ?>
                    <? endif ?>

                    <? if (\App::config()->product['showDeliveryPrice']): ?>
                        <?= $deliveryPickup->price == 0 ? 'бесплатно' : $helper->formatPrice($deliveryPickup->price) . '&nbsp;<span class="rubl">p</span>' ?>
                    <? endif ?>
                    </span>
                </div>
            <? endif ?>

            <? if ($deliveryDelivery) : ?>
                <div class="buy-now-inshop__line jsDeliveryStandardAvailable">
                    Доставка
                    <span class="buy-now-inshop__mark">
                    <? if (!\App::abTest()->isHiddenDeliveryInterval()): ?>
                        <? if ($deliveryDelivery->dayRange): ?>
                            <span data-date="<?= $helper->json($deliveryDelivery->getMinDate() ? $deliveryDelivery->getMinDate()->date->format('Y-m-d') : null) ?>"><?= !empty($deliveryDelivery->dayRange['name']) ? $deliveryDelivery->dayRange['name'] : sprintf('%s-%s %s', $deliveryDelivery->dayRange['from'], $deliveryDelivery->dayRange['to'], $helper->numberChoice($deliveryDelivery->dayRange['to'], ['день', 'дня', 'дней'])) ?></span>
                        <? else: ?>
                            <?= mb_strtolower($helper->humanizeDate($deliveryDelivery->getMinDate()->date)) ?><? if (\App::config()->product['showDeliveryPrice']): ?>,<? endif ?>
                        <? endif ?>
                    <? endif ?>

                    <? if (\App::config()->product['showDeliveryPrice']): ?>
                        <?= $deliveryDelivery->price == 0 ? 'бесплатно' : $helper->formatPrice($deliveryDelivery->price) . '&nbsp;<span class="rubl">p</span>' ?>
                    <? endif ?>
                    </span>
                </div>
            <? endif ?>

        </div>
    </div>
    <!--/ в наличии -->

    <div class="product-cart-get">
        <? if ($deliveryPickup) : ?>
        <div class="product-cart-get__inner">
            <div class="product-cart-get__title">Пункты выдачи заказов</div>
            <div class="product-cart-get__grids product-cart-get__grids_<?= $deliveryPickupCount ?> ">
                <? if ($product->delivery->hasEnterDelivery): ?>
                    <div class="product-cart-get__grids-cell" style="background-color:#000000;">
                        <img src="/styles/product/img/logo/enter.png" alt="Enter">
                    </div>
                <? endif ?>

                <? if ($product->delivery->hasSvyaznoyDelivery): ?>
                    <div class="product-cart-get__grids-cell" style="background-color:#ffffff;">
                        <img src="/styles/product/img/logo/svyaznoy.png" alt="Связной">
                    </div>
                <? endif ?>

                <? if ($product->delivery->hasEurosetDelivery): ?>
                    <div class="product-cart-get__grids-cell" style="background-color:#ffe735;">
                        <img src="/styles/product/img/logo/euroset.png" alt="Евросеть">
                    </div>
                <? endif ?>

                <? if ($product->delivery->hasPickpointDelivery): ?>
                    <div class="product-cart-get__grids-cell" style="background-color:#fff;">
                        <img src="/styles/product/img/logo/pickpoint.png" alt="PickPoint">
                    </div>
                <? endif ?>

                <? if ($product->delivery->hasHermesDelivery): ?>
                    <div class="product-cart-get__grids-cell" style="background-color:#0091cd;">
                        <img src="/styles/product/img/logo/hermes.png" alt="Hermes">
                    </div>
                <? endif ?>
            </div>
        </div>
        <? endif ?>

        <? if ($deliveryDelivery) : ?>
        <div class="product-cart-get__inner">
            <div class="product-cart-get__title">Доставка</div>
            <div class="product-cart-get__grids product-cart-get__grids_delivery">
                <div class="product-cart-get__grids-cell">
                    <img src="/styles/product/img/logo/spsr.png" alt="SPSR">
                </div>
            </div>
        </div>
        <? endif ?>
    </div>

    <style>
        .spinner {
            margin: 250px auto;
            width: 50px;
            height: 30px;
            text-align: center;
            font-size: 10px;
        }

        .spinner > div {
            background-color: #d1d0cf;
            height: 100%;
            width: 6px;
            display: inline-block;

            -webkit-animation: stretchdelay 1.2s infinite ease-in-out;
            animation: stretchdelay 1.2s infinite ease-in-out;
        }

        .spinner .rect2 {
            -webkit-animation-delay: -1.1s;
            animation-delay: -1.1s;
        }

        .spinner .rect3 {
            -webkit-animation-delay: -1.0s;
            animation-delay: -1.0s;
        }

        .spinner .rect4 {
            -webkit-animation-delay: -0.9s;
            animation-delay: -0.9s;
        }

        .spinner .rect5 {
            -webkit-animation-delay: -0.8s;
            animation-delay: -0.8s;
        }

        @-webkit-keyframes stretchdelay {
            0%, 40%, 100% { -webkit-transform: scaleY(0.4) }
            20% { -webkit-transform: scaleY(1.0) }
        }

        @keyframes stretchdelay {
            0%, 40%, 100% {
                transform: scaleY(0.4);
                -webkit-transform: scaleY(0.4);
            }  20% {
                   transform: scaleY(1.0);
                   -webkit-transform: scaleY(1.0);
               }
        }
    </style>
    <div class="jsProductPointsMap" style="width: 922px; height: 520px; display: none;">
        <div class="spinner">
            <div class="rect1"></div>
            <div class="rect2"></div>
            <div class="rect3"></div>
            <div class="rect4"></div>
            <div class="rect5"></div>
        </div>
    </div>



<? }; return $f;