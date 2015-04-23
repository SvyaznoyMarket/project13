<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product,
    \Model\Favorite\Product\Entity $favoriteProduct = null
) {
?>

<a href="<?= $helper->url('favorite.add', ['productUi' => $product->getUi()]) ?>" class="jsFavoriteLink product-card-tools__lk">
    <i class="product-card-tools__icon i-tools-icon i-tools-icon--wish<? if ($favoriteProduct): ?> i-tools-icon--wishSelected<? endif ?>"></i>
    <span class="product-card-tools__tx">В избранное</span>
</a>

<? };