<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product
) {
    if ($_SERVER['APPLICATION_ENV'] === 'local' || $_SERVER['APPLICATION_ENV'] === 'dev') {
        $product->setPriceOld(10000);
    }

    if (0 === $product->getPrice()) {
        return;
    }
?>
    <? if ($product->getPriceOld()): ?>
        <div class="priceOld"><span><?= $helper->formatPrice($product->getPriceOld()) ?></span> <span class="rubl">p</span></div>
    <? endif ?>

    <span>
        <div class="bPrice5321a13ebb1e5 bInputList">
            <strong itemprop="price"><?= $helper->formatPrice($product->getPrice()) ?></strong>
            <meta itemprop="priceCurrency" content="RUB" />
            <span class="rubl">p</span>
        </div>
    </span>
<? };