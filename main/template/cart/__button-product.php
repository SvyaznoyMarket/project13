<?php

return function (
    $class = null,
    $value = 'Купить',
    \Model\Product\BasicEntity $product,
    \Helper\TemplateHelper $helper
) {

$disabled = !$product->getIsBuyable();
if ($disabled) {
    $url = '#';
} else {
    $urlParams = [
        'productId' => $product->getId(),
    ];
    if ($helper->hasParam('sender')) {
        $urlParams['sender'] = $helper->getParam('sender') . '|' . $product->getId();
    }
    $url = $helper->url('cart.product.add', $urlParams);
}

?>

<a href="<?= $url ?>" class="<?= sprintf('cartButton-product-%s', $product->getId()) ?> jsBuyButton<? if ($disabled): ?> mDisabled<? endif ?><?php if ($class): ?> <?= $class ?><? endif ?>"><?= $value ?></a>

<? };