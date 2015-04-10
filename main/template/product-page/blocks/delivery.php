<?php
$f = function (
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product
) {
    $deliveryPickup = $product->delivery && $product->delivery->getPickupWithMinDate() ? $product->delivery->getPickupWithMinDate() : null;
    $deliveryDelivery = $product->delivery && $product->delivery->getDeliveryWithMinDate() ? $product->delivery->getDeliveryWithMinDate() : null;

    if (!$product->getIsBuyable()) return;
?>
    <!-- в наличии -->
    <div class="buy-now-inshop">
        <span class="buy-now-inshop__tl">В наличии</span>

        <? if ($deliveryPickup) : ?>
            <div class="buy-now-inshop__line">Самовывоз <span class="buy-now-inshop__mark"><?= mb_strtolower($helper->humanizeDate($deliveryPickup->getMinDate()->date)) ?>, <?= $deliveryPickup->price == 0 ? 'бесплатно' : $helper->formatPrice($deliveryPickup->price) . '&nbsp;<span class="rubl">p</span>' ?></span></div>
        <? endif ?>

        <? if ($deliveryDelivery) : ?>
            <div class="buy-now-inshop__line">Доставка <span class="buy-now-inshop__mark"><?= mb_strtolower($helper->humanizeDate($deliveryDelivery->getMinDate()->date)) ?>, <?= $deliveryDelivery->price == 0 ? 'бесплатно' : $helper->formatPrice($deliveryDelivery->price) . '&nbsp;<span class="rubl">p</span>' ?></span></div>
        <? endif ?>

    </div>
    <!--/ в наличии -->
<? }; return $f;