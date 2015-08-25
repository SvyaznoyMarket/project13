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
    <?= $helper->renderWithMustache('product/_favoriteButton', [
        'ui'           => $product->getUi(),
        'url'          => $url,
        'isInFavorite' => $isInFavorite,
        'text'         => $text,
    ])?>

<? };