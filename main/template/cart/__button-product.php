<?php

return function (
    \Helper\TemplateHelper $helper,
    \Model\Product\BasicEntity $product,
    $url = null,
    $class = null,
    $value = 'Купить'
) {

$class = \View\Id::cartButtonForProduct($product->getId()) . ' jsBuyButton ' . $class;

if (!$product->getIsBuyable()) {
    $url = '#';
    $class .= ' mDisabled';

    if ($product->getIsInShopsOnly()) {
        $class .= ' mShopsOnly';
        $value = 'Только в магазинах';
    } elseif ($product->getState()->getIsShop()) {
        $class .= ' mShopsOnly';
        $value = 'Витринный товар';
    } else {
        $value = 'Нет в наличии';
    }
} else if (!isset($url)) {
    $urlParams = [
        'productId' => $product->getId(),
    ];
    if ($helper->hasParam('sender')) {
        $urlParams['sender'] = $helper->getParam('sender') . '|' . $product->getId();
    }
    $url = $helper->url('cart.product.set', $urlParams);
}

?>
<div class="bWidgetBuy__eBuy btnBuy">
    <a href="<?= $url ?>" class="<?= $class ?>" data-group="<?= $product->getId() ?>"><?= $value ?></a>
</div>

<? };