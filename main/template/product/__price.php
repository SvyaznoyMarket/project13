<?php

return function(
    \Model\Product\Entity $product,
    \Helper\TemplateHelper $helper
) {
    $user = \App::user();
?>

<? if ($product->getPriceOld() && !$user->getRegion()->getHasTransportCompany()): ?>
    <div class="priceOld"><span><?= $helper->formatPrice($product->getPriceOld()) ?></span> <span class="rubl">p</span></div>
<? endif ?>
<div class="bPrice"><strong><?= $helper->formatPrice($product->getPrice()) ?></strong> <span class="rubl">p</span></div>

<? };