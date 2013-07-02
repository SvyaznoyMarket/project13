<?php

return function (
    \Helper\TemplateHelper $helper
) { ?>

<ul class="bWidgetBuy__eDelivery" data-value="<?= $helper->json(['url' => $helper->url('product.delivery')]) ?>">
    <li class="bWidgetBuy__eDelivery-item bWidgetBuy__eDelivery-price">
        <span>Доставка <strong>290</strong> <span class="rubl">p</span></span>
        <div>Завтра, 16.05.2013</div>
    </li>
    <li class="bWidgetBuy__eDelivery-item bWidgetBuy__eDelivery-free">
        <span>Самовывоз <strong>бесплатно</strong></span>
        <div>Завтра, 16.05.2013</div>
    </li>

    <li class="bWidgetBuy__eDelivery-item bWidgetBuy__eDelivery-now click">
        <span class="dotted">Есть в магазинах</span>
        <div>Купить сегодня без предзаказа</div>
    </li>
</ul>

<ul style="display: block;" class="bDeliveryFreeAddress">
    <li>
        м. Белорусская,<br/>
        ул. Грузинский вал, д. 31
    </li>
    <li>
        м. Ленинский проспект, <br/>
        ул. Орджоникидзе, д. 11, стр. 10
    </li>
    <li>
        м. Белорусская, <br/>
        ул. Грузинский вал, д. 31
    </li>
    <li>
        м. Ленинский проспект, <br/>
        ул. Орджоникидзе, д. 11, стр. 10
    </li>
    <li>
        м. Белорусская, <br/>
        ул. Грузинский вал, д. 31
    </li>
    <li>
        м. Ленинский проспект, <br/>
        ул. Орджоникидзе, д. 11, стр. 10
    </li>
    <li>
        м. Белорусская, <br/>
        ул. Грузинский вал, д. 31
    </li>
    <li>
        м. Ленинский проспект, <br/>
        ул. Орджоникидзе, д. 11, стр. 10
    </li>
</ul><!--/выпадающий список при клике по - Есть в магазинах -->

<? };