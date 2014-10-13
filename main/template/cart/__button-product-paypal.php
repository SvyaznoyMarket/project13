<?php

return function (
    \Helper\TemplateHelper $helper,
    \Model\Product\BasicEntity $product
) {

    if (
        !\App::config()->payment['paypalECS']
        || (5 === $product->getStatusId()) // SITE-2924
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
<div class="bWidgetBuy__eBuy btnBuy mBtnPayPal">
    <a class="bPayPalLink jsPayPalButton" href="<?= $url ?>"><img src="https://www.paypalobjects.com/ru_RU/i/btn/btn_xpressCheckout.gif" /></a>
</div>

<? };