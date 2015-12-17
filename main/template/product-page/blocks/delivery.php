<?php

$f = function (
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product
) {

    if (!$product->getIsBuyable() || !$product->delivery) return;

    /** @var \Model\Product\Delivery\Delivery|null $deliveryPickup */
    $deliveryPickup = $product->delivery->getPickupWithMinDate() ?: null;
    /** @var \Model\Product\Delivery\Delivery|null $deliveryDelivery */
    $deliveryDelivery = $product->delivery->getDeliveryWithMinDate() ?: null;

?>
    <!-- в наличии -->
    <div class="buy-now-inshop <?= $deliveryPickup ? 'jsShowDeliveryMap' : 'buy-now-inshop--text' ?>" data-product-id="<?= $product->getId() ?>" data-product-ui="<?= $product->getUi() ?>" <? if (!$deliveryPickup) : ?>style="cursor: default"<? endif ?>>
        <span class="buy-now-inshop__tl">В наличии</span>
        <? if ($deliveryPickup) : ?>
            <div class="buy-now-inshop__line jsDeliveryPickupAvailable">
                Самовывоз
                <span class="buy-now-inshop__mark">
                    <? if ($deliveryPickup->dateInterval): ?>
                        <span data-date="<?= $helper->json($deliveryPickup->dateInterval) ?>"><?= sprintf('%s %s,', $deliveryPickup->dateInterval->from ? ('с ' . $deliveryPickup->dateInterval->from->format('d.m')) : '', $deliveryPickup->dateInterval->to ? (' по ' . $deliveryPickup->dateInterval->to->format('d.m')) : '') ?></span>
                    <? elseif ($deliveryPickup->dayRange): ?>
                        <span data-date="<?= $helper->json($deliveryPickup->getMinDate() ? $deliveryPickup->getMinDate()->date->format('Y-m-d') : null) ?>"><?= sprintf('%s-%s %s', $deliveryPickup->dayRange['from'], $deliveryPickup->dayRange['to'], $helper->numberChoice($deliveryPickup->dayRange['to'], ['день', 'дня', 'дней'])) ?></span>
                    <? else: ?>
                        <?= mb_strtolower($helper->humanizeDate($deliveryPickup->getMinDate()->date)) ?>,
                    <? endif ?>
                    <?= $deliveryPickup->price == 0 ? 'бесплатно' : $helper->formatPrice($deliveryPickup->price) . '&nbsp;<span class="rubl">p</span>' ?>
                </span>
            </div>
        <? endif ?>

        <? if ($deliveryDelivery) : ?>
            <div class="buy-now-inshop__line jsDeliveryStandardAvailable">
                Доставка
                <span class="buy-now-inshop__mark">
                <? if ($deliveryDelivery->dayRange): ?>
                    <span data-date="<?= $helper->json($deliveryDelivery->getMinDate() ? $deliveryDelivery->getMinDate()->date->format('Y-m-d') : null) ?>"><?= sprintf('%s-%s %s', $deliveryDelivery->dayRange['from'], $deliveryDelivery->dayRange['to'], $helper->numberChoice($deliveryDelivery->dayRange['to'], ['день', 'дня', 'дней'])) ?></span>
                <? else: ?>
                    <?= mb_strtolower($helper->humanizeDate($deliveryDelivery->getMinDate()->date)) ?>,
                <? endif ?>
                    <?= $deliveryDelivery->price == 0 ? 'бесплатно' : $helper->formatPrice($deliveryDelivery->price) . '&nbsp;<span class="rubl">p</span>' ?>
                </span>
            </div>
        <? endif ?>

    </div>
    <!--/ в наличии -->

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