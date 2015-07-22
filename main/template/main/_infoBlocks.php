<ul class="shopInfo clearfix">
    <li class="shopInfo_i jsShopInfoPreview" data-id="1" data-name="Доставка">
        <i class="shopInfo_l i-shopInfo i-shopInfo-delivery"></i>
        <div class="shopInfo_r">
            <span class="shopInfo_tl undrl">Доставка</span>
            <p class="shopInfo_tx">Доставляем по всей России</p>
        </div>
    </li>
    <li class="shopInfo_i jsShopInfoPreview" data-id="2" data-name="Самовывоз">
        <i class="shopInfo_l i-shopInfo i-shopInfo-deliveryself"></i>
        <div class="shopInfo_r">
            <span class="shopInfo_tl undrl">Самовывоз</span>
            <p class="shopInfo_tx">Более 3800 пунктов выдачи</p>
        </div>
    </li>
    <li class="shopInfo_i jsShopInfoPreview" data-id="3" data-name="Удобно платить">
        <i class="shopInfo_l i-shopInfo i-shopInfo-payment"></i>
        <div class="shopInfo_r">
            <span class="shopInfo_tl undrl">Удобно платить</span>
            <p class="shopInfo_tx">Способ оплаты на твой вкус</p>
        </div>
    </li>
    <li class="shopInfo_i jsShopInfoPreview" data-name="WOW Акции">
        <i class="shopInfo_l i-shopInfo i-shopInfo-wow"></i>
        <a href="/special_offers" target="_blank" class="shopInfo_r jsShopInfoLink">
            <span class="shopInfo_tl undrl">WOW Акции</span>
            <p class="shopInfo_tx">Лучшие предложения</p>
        </a>
    </li>
</ul>

<div class="shopInfoTab jsShopInfoBlock" data-id="1">
    <ul class="shopInfoTab_lst">
        <li class="shopInfoTab_i">
            <div class="shopInfoTab_iconw"><div class="shopInfoTab_icon"><i class="i-shopinfo i-shopinfo-delivery1"></i></div></div>
            <div class="shopInfoTab_tx">ВЫГОДНЫЕ ЦЕНЫ</div>
        </li>
        <li class="shopInfoTab_i">
            <div class="shopInfoTab_iconw"><div class="shopInfoTab_icon"><i class="i-shopinfo i-shopinfo-delivery2"></i></div></div>
            <div class="shopInfoTab_tx">БЕСПЛАТНАЯ ДОСТАВКА<br/>НА РЯД ТОВАРОВ</div>
        </li>
        <li class="shopInfoTab_i">
            <div class="shopInfoTab_iconw"><div class="shopInfoTab_icon"><i class="i-shopinfo i-shopinfo-delivery3"></i></div></div>
            <div class="shopInfoTab_tx">ДОСТАВКА ПО РОССИИ</div>
        </li>
        <li class="shopInfoTab_i">
            <div class="shopInfoTab_iconw"><div class="shopInfoTab_icon"><i class="i-shopinfo i-shopinfo-delivery4"></i></div></div>
            <div class="shopInfoTab_tx">ДОСТАВКА ДО ДВЕРИ<br/>НА ЛЮБОЙ ЭТАЖ</div>
        </li>
    </ul>

    <a href="/delivery_types#delivr_buy" target="_blank" class="shopInfoTab_btn">Подробнее об условиях, стоимости, сроках и интервалах доставки</a>
</div>

<div class="shopInfoTab shopInfoTab-v2 jsShopInfoBlock" data-id="2">
    <ul class="shopInfoTab_lst">
        <li class="shopInfoTab_i">
            <div class="shopInfoTab_iconw"><div class="shopInfoTab_icon"><i class="i-shopinfo i-shopinfo-selfdelivery1"></i></div></div>
            <div class="shopInfoTab_tx">САМОВЫВОЗ<br/>ИЗ МАГАЗИНА ENTER</div>
        </li>
        <li class="shopInfoTab_i">
            <div class="shopInfoTab_iconw"><div class="shopInfoTab_icon"><i class="i-shopinfo i-shopinfo-selfdelivery2"></i></div></div>
            <div class="shopInfoTab_tx">РЕЗЕРВ ТОВАРА НА 2 ДНЯ</div>
        </li>
        <li class="shopInfoTab_i">
            <div class="shopInfoTab_iconw"><div class="shopInfoTab_icon"><i class="i-shopinfo i-shopinfo-selfdelivery3"></i></div></div>
            <div class="shopInfoTab_tx">СРОК ДОСТАВКИ<br/>В МАГАЗИН 1-4 ДНЯ</div>
        </li>
        <li class="shopInfoTab_i">
            <div class="shopInfoTab_iconw"><div class="shopInfoTab_icon"><i class="i-shopinfo i-shopinfo-selfdelivery4"></i></div></div>
            <div class="shopInfoTab_tx">ПОЛУЧЕНИЕ ТОВАРА<br/>В ПОСТАМАТАХ PICKPOINT</div>
        </li>
    </ul>
    <a href="/delivery_types#delivr_self" target="_blank" class="shopInfoTab_btn">Подробнее об условиях, стоимости, сроках и интервалах доставки</a>
</div>

<div class="shopInfoTab shopInfoTab-v3 jsShopInfoBlock" data-id="3">
    <ul class="shopInfoTab_lst">
        <li class="shopInfoTab_i">
            <div class="shopInfoTab_iconw"><div class="shopInfoTab_icon"><i class="i-shopinfo i-shopinfo-payment1"></i></div></div>
            <div class="shopInfoTab_tx">НАЛИЧНЫЕ</div>
        </li>
        <li class="shopInfoTab_i">
            <div class="shopInfoTab_iconw"><div class="shopInfoTab_icon"><i class="i-shopinfo i-shopinfo-payment2"></i></div></div>
            <div class="shopInfoTab_tx">БАНКОВСКАЯ КАРТА</div>
        </li>
        <? if (\App::config()->payment['creditEnabled']) : ?>
        <li class="shopInfoTab_i">
            <div class="shopInfoTab_iconw"><div class="shopInfoTab_icon"><i class="i-shopinfo i-shopinfo-payment3"></i></div></div>
            <div class="shopInfoTab_tx">КРЕДИТ</div>
        </li>
        <? endif ?>
        <? if (false): ?>
        <li class="shopInfoTab_i">
            <div class="shopInfoTab_iconw"><div class="shopInfoTab_icon"><i class="i-shopinfo i-shopinfo-payment4"></i></div></div>
            <div class="shopInfoTab_tx">PAYPAL</div>
        </li>
        <? endif ?>
        <li class="shopInfoTab_i">
            <div class="shopInfoTab_iconw"><div class="shopInfoTab_icon"><i class="i-shopinfo i-shopinfo-payment5"></i></div></div>
            <div class="shopInfoTab_tx">ОНЛАЙН-БАНК</div>
        </li>
    </ul>

    <a href="/how_pay" target="_blank" class="shopInfoTab_btn">Подробнее об этих и других способах оплат</a>
</div>