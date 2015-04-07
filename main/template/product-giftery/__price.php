<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product
) {

    if (0 === $product->getPrice()) {
        return;
    }
?>

        <div class="product-card-price">
        <? /* Старой цены у товаров marketplace пока не может быть
    <? if ($product->getPriceOld()): ?>
        <div class="priceOld"><span><?= $helper->formatPrice($product->getPriceOld()) ?></span> <span class="rubl">p</span></div>
    <? endif ?>
    */ ?>
            <span class="product-card-price__inner">от <strong itemprop="price"><?= \App::config()->partners['Giftery']['lowestPrice'] ?></strong>
                <meta itemprop="priceCurrency" content="RUB" />
                <span class="rubl">p</span>
            </span>
        </div>
<? };