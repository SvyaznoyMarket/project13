<?php

return function (
    \Helper\TemplateHelper $helper
) { ?>

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
        <%=name%>
    </li>
</script>


<ul class="bWidgetBuy__eDelivery" data-value="<?= $helper->json(['url' => $helper->url('product.delivery')]) ?>">
    <li class="bWidgetBuy__eDelivery-item bWidgetBuy__eDelivery-price">
    </li>
    <li class="bWidgetBuy__eDelivery-item bWidgetBuy__eDelivery-free">
    </li>

    <li class="bWidgetBuy__eDelivery-item bWidgetBuy__eDelivery-now mOpen">
        <span class="dotted">Есть в магазинах</span>
        <div>Купить сегодня без предзаказа</div>
        <ul class="bDeliveryFreeAddress">
        </ul>
    </li>
</ul>

<? };