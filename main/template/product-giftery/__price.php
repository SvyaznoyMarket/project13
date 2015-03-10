<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product
) {

    if (0 === $product->getPrice()) {
        return;
    }
?>
    <? /* Старой цены у товаров marketplace пока не может быть
    <? if ($product->getPriceOld()): ?>
        <div class="priceOld"><span><?= $helper->formatPrice($product->getPriceOld()) ?></span> <span class="rubl">p</span></div>
    <? endif ?>
    */ ?>

    <span>
        <div class="bPrice5321a13ebb1e5 bInputList">
            от <strong itemprop="price"><?= \App::config()->partners['Giftery']['lowestPrice'] ?></strong>
            <meta itemprop="priceCurrency" content="RUB" />
            <span class="rubl">p</span>
        </div>
    </span>
<? };