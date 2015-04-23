<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product,
    \Model\Favorite\Product\Entity $favoriteProduct = null
) {
    if ($favoriteProduct) {
        $text = 'Удалить';
        $url = $helper->url('favorite.delete', ['productUi' => $product->getUi()]);
    } else {
        $text = 'В избранное';
        $url = $helper->url('favorite.add', ['productUi' => $product->getUi()]);
    }
?>

<a href="<?= $url ?>" class="jsFavoriteLink product-card-tools__lk id-favoriteButton-<?= $product->getUi() ?> <? if ($favoriteProduct): ?> product-card-tools__lk--active<? endif ?>">
    <i class="product-card-tools__icon i-tools-icon i-tools-icon--wish"></i>
    <span class="product-card-tools__tx"><?= $text ?></span>
</a>

<? };