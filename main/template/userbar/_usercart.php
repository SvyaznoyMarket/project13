<!-- При пустой корзине -->
<div class="topbarfix_cart mEmpty" data-bind=" visible: cart().length == 0 ">
    <a href="/cart" class="topbarfix_cart_tl">Корзина</a>
</div>

<!-- При непустой корзине -->
<div class="topbarfix_cart js-topbarfixNotEmptyCart" data-bind=" visible: cart().length > 0 " style="display: none">

    <a href="<?= $page->url('cart') ?>" class="topbarfix_cart_tl">
        <span class="topbarfix_cart_tx">Корзина</span>
        <strong class="topbarfix_cart_qn topbarfix_cartQuan" data-bind="text: cart().length"></strong>
    </a>

    <div class="topbarfix_dd topbarfix_cartOn" >
        <ul class="cartLst" data-bind="foreach: cart">
            <li class="cartLst_i">
                <a class="cartLst_lk" data-bind="attr: { href: link }"><img class="cartLst_img" src="" data-bind="attr: { src: img }"/></a>
                <div class="cartLst_n"><a data-bind="attr: { href: link }, text: name"></a></div>
                <div class="cartLst_inf">
                    <span class="price"><!--ko text: window.printPrice(price)--><!--/ko--> &nbsp;<span class="rubl">p</span></span>
                    <span class="quan"><!--ko text: quantity--><!--/ko--> шт.</span>
                    <a class="del jsCartDelete" data-bind="attr: { href: deleteUrl }">удалить</a>
                </div>
            </li>
        </ul>
        
        <!-- для кнопки с иконкой btnBuy-inf -->
        <div class="btnBuy quickOrder" data-bind="css: {'btnBuy-inf': infoIconVisible() }"><a href="<?= $page->url('order') ?>" class="btnBuy__eLink quickOrder__link">Оформить заказ</a></div>

        <div class="buyInfo" data-bind="visible: ENTER.config.pageConfig.selfDeliveryTest && !infoIconVisible() && ENTER.config.pageConfig.selfDeliveryLimit - cartSum() > 0">
            До бесплатного самовывоза осталось
            <div class="buyInfo_pr"><span data-bind="text: ENTER.config.pageConfig.selfDeliveryLimit - cartSum()">175</span> <span class="rubl">p</span></div>
            <a class="buyInfo_lk" href="/slices/all_labels">Выбрать товары по суперценам</a> >
        </div>

        <div class="buyInfo" data-bind="visible: ENTER.config.pageConfig.selfDeliveryTest && !infoIconVisible() && ENTER.config.pageConfig.selfDeliveryLimit - cartSum() <= 0">
            <div class="buyInfo_self">Самовывоз<br/>БЕСПЛАТНО</div>
        </div>
    </div>

    <div class="hintDd"><!-- если похожии товары есть то добавляем класс mhintDdOn -->
    </div>

</div>