<?php

return function(
    \Model\Product\BasicEntity $product
) { ?>

<? if ($product->getIsBuyable()): ?>
    <link itemprop="availability" href="http://schema.org/InStock" />
    <div class="inStock">Есть в наличии</div>
<? elseif (!$product->getIsBuyable() && $product->getState()->getIsShop()): ?>
    <link itemprop="availability" href="http://schema.org/InStoreOnly" />
<? else: ?>
    <link itemprop="availability" href="http://schema.org/OutOfStock" />
<? endif ?>

<? };