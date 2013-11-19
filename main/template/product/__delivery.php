<?php

return function (
    \Helper\TemplateHelper $helper,
    \Model\Product\BasicEntity $product,
    array $shopStates = [],
    array $deliveryDataResponse = []
) {
    /**
     * @var $shopStates \Model\Product\ShopState\Entity[]
     */

    $user = \App::user();

    $shopData = [];
    foreach ($shopStates as $shopState) {
        $shop = $shopState->getShop();
        if (!$shop instanceof \Model\Shop\Entity) continue;
        $shopData[] = [
            'id'        => $shop->getId(),
            'name'      => $shop->getName(),
            //'address'   => $shop->getAddress(),
            'regime'    => $shop->getRegime(),
            'longitude' => $shop->getLongitude(),
            'latitude'  => $shop->getLatitude(),
            'url'       => $helper->url('shop.show', ['shopToken' => $shop->getToken(), 'regionToken' => $user->getRegion()->getToken()]),
        ];
    }

    $deliveryData = [];
    if ((bool)$shopData) {
        $deliveryData[] = [
            'id'    => null,
            'token' => 'now',
            'price' => 0,
            'shop'  => $shopData,
            'date'  => [
                'value' => (new \DateTime())->format('d.m.Y'),
                'name'  => 'сегодня',
            ],
            'days'  => 0,
        ];
    }
?>

<script id="widget_delivery_standart" type="text/html">
    <% if (price === 0) { %>
        <span><span class="bJustText">Доставка</span> <strong>бесплатно</strong></span>
    <% } else { %>
        <span><span class="bJustText">Доставка</span> <strong><%=price%></strong> <span class="rubl">p</span></span>
    <% } %>
        <div class="bJustText"><%=dateString%></div>
</script>

<script id="widget_delivery_self" type="text/html">
    <% if (price === 0) { %>
        <span><span class="bJustText">Самовывоз</span> <strong>бесплатно</strong></span>
    <% } else if ( isNaN(price) ) { %>
        <span><span class="bJustText">Самовывоз</span> <strong><%=price%></strong> </span>
    <% } else { %>
        <span><span class="bJustText">Самовывоз</span> <strong><%=price%></strong> <span class="rubl">p</span></span>
    <% } %>
        <div class="bJustText"><%=dateString%></div>
</script>

<script id="widget_delivery_shop" type="text/html">
    <li class="bDeliveryFreeAddress__eShop">
        <a class="bDeliveryFreeAddress__eLink" data-lat="<%=lat%>" data-lng="<%=lng%>" href="<%=url%>"><%=name%></a>
    </li>
</script>

<div id="avalibleShop" class="popup">
    <i class="close" title="Закрыть">Закрыть</i>
    <div id="ymaps-avalshops"></div>
    <a href="#" class="bOrangeButton fr mt5">Перейти к магазину</a>
</div>

<ul class="bDelivery mLoader" data-value="<?= $helper->json([
//    'url'       => $helper->url('product.delivery'), // загружаем всегда (непокупабельный товар может иметь пикпойнты)
    'response'  => $deliveryDataResponse,
    'delivery'  => $deliveryData,
    'loadShops' => $product->getIsBuyable() ? false : true, // загружаем список магазинов, если товар непокупабельный
]) ?>">
    <li class="bDelivery__eItem mDeliveryPrice">
    </li>
    <li class="bDelivery__eItem mDeliveryFree">
    </li>

    <? $closed = !$product->isInShopOnly() ? true : false ?>
    <li class="bDelivery__eItem mDeliveryNow <?= $closed ? 'mClose' : 'mOpen' ?>">
        <span class="bDeliveryNowClick dotted">Сегодня есть в магазинах</span>
        <div class="<?= $closed ? ' hf' : '' ?>">Cегодня, без предзаказа</div>
        <ul class="bDeliveryFreeAddress">
        </ul>
    </li>
</ul>

<? };