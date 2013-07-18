<?php

return function (
    \Model\Product\BasicEntity $product,
    array $shopStates = [],
    \Helper\TemplateHelper $helper
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
        <span>Доставка <strong>бесплатно</strong></span>
    <% } else { %>
        <span>Доставка <strong><%=price%></strong> <span class="rubl">p</span></span>
    <% } %>
        <div><%=dateString%></div>
</script>

<script id="widget_delivery_self" type="text/html">
    <% if (price === 0) { %>
        <span>Самовывоз <strong>бесплатно</strong></span>
    <% } else { %>
        <span>Самовывоз <strong><%=price%></strong> <span class="rubl">p</span></span>
    <% } %>
        <div><%=dateString%></div>
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


<ul class="bWidgetBuy__eDelivery" data-value="<?= $helper->json([
    'url'      => $product->getIsBuyable() ? $helper->url('product.delivery') : '',
    'delivery' => $deliveryData,
]) ?>">
    <li class="bWidgetBuy__eDelivery-item bWidgetBuy__eDelivery-price">
    </li>
    <li class="bWidgetBuy__eDelivery-item bWidgetBuy__eDelivery-free">
    </li>

    <li class="bWidgetBuy__eDelivery-item bWidgetBuy__eDelivery-now <?= $product->getIsBuyable() ? 'mClose' : 'mOpen'?>">
        <span class="bWidgetBuy__eDelivery-nowClick dotted">Есть в магазинах</span>
        <div>Cегодня, без предзаказа</div>
        <ul class="bDeliveryFreeAddress">
        </ul>
    </li>
</ul>

<? };