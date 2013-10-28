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
<div class="bWidgetBuy mWidget mLiftGift">
    <div class="bTitle">Подари жизнь!<span class="bSubTitle">Благотворительный фонд</div>

    Специальная цена при благотворительной покупке в подарок ребенку:

    <div class="bPrice"><strong class="jsPrice"><?= $helper->formatPrice($product->getPrice()) ?></strong> <span class="rubl">p</span></div>

    <div class="bWidgetBuy__eBuy btnBuy mBtnLifeGift">
        <a class="bLifeGiftLink jsLifeGiftButton" href="<?= $url ?>" data-group="<?= $product->getId() ?>">Подарить</a>
    </div>

    <div class="bLiftGiftLogo"><img src="/css/lifeGift/img/podari-zhizn-logo-people.png" /></div>


    <div class="bLiftGiftFootnote">Фонд "Подари жизнь" помогает детям с трудными заболеваниями.<br/>С 15 ноября по 15 декабря вы можете купить этот товар в подарок ребенку.<br/>Доставку мы возьмем на себя.</div>
</div>

<? };