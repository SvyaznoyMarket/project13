<?php

return function (
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product
) {

    if (
        !\App::config()->lifeGift['enabled']
        || !$product->getIsBuyable()
        || !(\App::config()->lifeGift['labelId'] === $product->getLabelId())
    ) {
        return '';
    }

    $urlParams = [
        'productId' => $product->getId(),
    ];
    if ($helper->hasParam('sender')) {
        $urlParams['sender'] = $helper->getParam('sender') . '|' . $product->getId();
    }
    $url = $helper->url('cart.lifeGift.product.set', $urlParams);

?>
<div class="bWidgetBuy__eBuy btnBuy mBtnLifeGift">
    <a class="bLifeGiftLink jsLifeGiftButton" href="<?= $url ?>" data-group="<?= $product->getId() ?>">Подари Жизнь</a>
</div>

<? };