<?php

return function(
    \Model\Product\Entity $product,
    \Helper\TemplateHelper $helper
) {
    $user = \App::user();
?>

<? if ($product->getPriceOld() && !$user->getRegion()->getHasTransportCompany()): ?>
    <div class="priceOld"><span><?= $helper->formatPrice($product->getPriceOld()) ?></span> <span class="rubl">p</span></div>
<? elseif (\App::config()->product['showAveragePrice'] && !$product->getPriceOld() && $product->getPriceAverage()): ?>

<? endif ?>
<div class="bPrice"><strong class="jsPrice"><?= $helper->formatPrice($product->getPrice()) ?></strong> <span class="rubl">p</span></div>

<? };