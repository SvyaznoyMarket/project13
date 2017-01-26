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
        <div class="product-card__info--oldprice"><span class="td-lineth"><?= $helper->formatPrice($product->getPriceOld()) ?></span> <span class="rubl">p</span></div>
    <? endif ?>

    <span>
        <div class="product-card__price">
            <strong><?= $helper->formatPrice($product->getPrice()) ?></strong>
            <span class="rubl">p</span>
        </div>
    </span>
<? };