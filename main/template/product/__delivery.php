<?php

return function (
    \Helper\TemplateHelper $helper
) { ?>

<script id="widget_delivery_standart" type="text/html">
    <span>Доставка <strong><%=price%></strong> <span class="rubl">p</span></span>
    <div><%=dateString%></div>
</script>
<script id="widget_delivery_self" type="text/html">
    <span>Самовывоз <strong>бесплатно</strong></span>
    <div><%=dateString%></div>
</script>
<script id="widget_delivery_shop" type="text/html">
    <li class="bDeliveryFreeAddress__eShop">
        <%=metro%>,<br/>
        <%=adress%>
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
            <li class="bDeliveryFreeAddress__eShop">
                м. Белорусская,<br/>
                ул. Грузинский вал, д. 31
            </li>
            <li class="bDeliveryFreeAddress__eShop">
                м. Ленинский проспект, <br/>
                ул. Орджоникидзе, д. 11, стр. 10
            </li>
            <li class="bDeliveryFreeAddress__eShop">
                м. Белорусская, <br/>
                ул. Грузинский вал, д. 31
            </li>
            <li class="bDeliveryFreeAddress__eShop">
                м. Ленинский проспект, <br/>
                ул. Орджоникидзе, д. 11, стр. 10
            </li>
            <li class="bDeliveryFreeAddress__eShop">
                м. Белорусская, <br/>
                ул. Грузинский вал, д. 31
            </li>
            <li class="bDeliveryFreeAddress__eShop">
                м. Ленинский проспект, <br/>
                ул. Орджоникидзе, д. 11, стр. 10
            </li>
            <li class="bDeliveryFreeAddress__eShop">
                м. Белорусская, <br/>
                ул. Грузинский вал, д. 31
            </li>
            <li class="bDeliveryFreeAddress__eShop">
                м. Ленинский проспект, <br/>
                ул. Орджоникидзе, д. 11, стр. 10
            </li>
        </ul><!--/выпадающий список при клике по - Есть в магазинах -->
    </li>
</ul>

<? };