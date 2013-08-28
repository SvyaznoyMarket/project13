<?php

return function (
    \Helper\TemplateHelper $helper,
    \Model\Product\BasicEntity $product
) {

    if (
        !\App::config()->payment['paypalECS']
        || !$product->getIsBuyable()
    ) {
        return '';
    }

    $urlParams = [
        'productId' => $product->getId(),
    ];
    if ($helper->hasParam('sender')) {
        $urlParams['sender'] = $helper->getParam('sender') . '|' . $product->getId();
    }
    $url = $helper->url('cart.paypal.product.set', $urlParams);

?>
<div class="bWidgetBuy__eBuy btnBuy">
    <a class="jsBuyButton" href="<?= $url ?>" data-group="<?= $product->getId() ?>">Оплатите с помощью PayPal</a>
</div>

<? };