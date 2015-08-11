<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product,
    \Model\Favorite\Product\Entity $favoriteProduct = null
) {

    $isInFavorite = $favoriteProduct && $favoriteProduct->isFavourite;

    if ($isInFavorite) {
        $text = 'Убрать из избранного';
        $url = $helper->url('favorite.delete', ['productUi' => $product->getUi()]);
    } else {
        $text = 'В избранное';
        $url = $helper->url('favorite.add', ['productUi' => $product->getUi()]);
    }
?>

<span class="id-favoriteButton-<?= $product->getUi() ?>">
    <a  href="<?= $url ?>"
        class="product-card-tools__lk <? if ($isInFavorite): ?> product-card-tools__lk--active<? endif ?> js-favorite-button"
    >
        <i class="product-card-tools__icon i-tools-icon i-tools-icon--wish"></i>
        <span class="product-card-tools__tx"><?= $text ?></span>
    </a>
</span>

<? };