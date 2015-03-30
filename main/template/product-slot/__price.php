<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product
) {
    if (0 === $product->getPrice()) {
        return;
    }
?>
    <? if ($product->getPriceOld()): ?>
        <div class="product-card__info--oldprice"><span><?= $helper->formatPrice($product->getPriceOld()) ?></span> <span class="rubl">p</span></div>
    <? endif ?>

    <span>
        <div class="product-card__price">
            <strong itemprop="price"><?= $helper->formatPrice($product->getPrice()) ?></strong>
            <meta itemprop="priceCurrency" content="RUB" />
            <span class="rubl">p</span>
        </div>
    </span>
<? };