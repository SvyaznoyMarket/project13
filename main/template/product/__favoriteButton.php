<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product,
    \Model\Favorite\Product\Entity $favoriteProduct = null
) {
?>

<a href="<?= $helper->url('favorite.add', ['productUi' => $product->getUi()]) ?>" class="jsFavoriteLink product-card-tools__lk <? if ($favoriteProduct): ?> product-card-tools__lk--active<? endif ?>">
    <i class="product-card-tools__icon i-tools-icon i-tools-icon--wish"></i>
    <span class="product-card-tools__tx">В избранное</span>
</a>

<? };