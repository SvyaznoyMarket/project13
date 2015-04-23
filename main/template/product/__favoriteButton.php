<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product,
    \Model\Favorite\Product\Entity $favoriteProduct = null
) {
    $removeUrl = null;

    if ($favoriteProduct) {
        $text = 'В избранном';
        $url = $helper->url('user.favorites');
        $removeUrl = $helper->url('favorite.delete', ['productUi' => $product->getUi()]);
    } else {
        $text = 'В избранное';
        $url = $helper->url('favorite.add', ['productUi' => $product->getUi()]);
    }
?>

<span class="id-favoriteButton-<?= $product->getUi() ?>">
    <a
        <? if (!$favoriteProduct): ?> data-ajax="true"<? endif ?>
        href="<?= $url ?>"
        class="jsFavoriteLink product-card-tools__lk <? if ($favoriteProduct): ?> product-card-tools__lk--active<? endif ?>"
    >
        <i class="product-card-tools__icon i-tools-icon i-tools-icon--wish"></i>
        <span class="product-card-tools__tx"><?= $text ?></span>
        <? if ($removeUrl): ?>
            <a class="jsFavoriteLink" data-ajax="true" href="<?= $removeUrl ?>" title="Удалить из избранного"> X</a>
       <? endif ?>
    </a>
</span>

<? };