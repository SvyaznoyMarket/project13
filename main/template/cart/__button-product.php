<?php

return function (
    $url = null,
    $class = null,
    $value = 'Купить',
    \Model\Product\BasicEntity $product,
    \Helper\TemplateHelper $helper
) {

$class = \View\Id::cartButtonForProduct($product->getId()) . ' jsBuyButton ' . $class;

$disabled = !$product->getIsBuyable();
if ($disabled) {
    $url = '#';
    $class .= ' mDisabled';
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