<?php

return function (
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product
) {

    if (
        !\App::config()->lifeGift['enabled']
        || !$product->getIsBuyable()
        || (5 === $product->getStatusId()) // SITE-2924
        || ($product->getLabel() && $product->getLabel()->id != \App::config()->lifeGift['labelId'])
    ) {
        return '';
    }

    $urlParams = [
        'productId' => $product->getId(),
    ];

    $url = $helper->url('orderV3.lifegift', $urlParams);

?>
<div class="bWidgetBuy mWidget mLiftGift js-WidgetBuy">

    <div class="bWidgetBuy__eBuy btnBuy mBtnLifeGift">
        <a class="bLifeGiftLink " href="<?= $url ?>">Подарить</a>
    </div>

    <ul class="bDeliveryGift">
        <li class="bDeliveryGift__eItem mDeliveryPrice"><span><span class="bJustText">Доставка</span></span>  в Фонд &#171;Подари жизнь&#187;, 16.12.2014</li>
    </ul>

    <div class="bGiftText">Специальная цена этого подарка</div>

    <div class="bPrice"><strong class="jsPrice"><?= $helper->formatPrice($product->getPrice()) ?></strong> <span class="rubl">p</span></div>
</div>

<? };