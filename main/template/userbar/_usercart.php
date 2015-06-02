<!-- При пустой корзине -->
<li class="userbtn_i topbarfix_cart mEmpty" data-bind=" visible: cart().length == 0 ">
    <a href="/cart" class="topbarfix_cart_tl">Корзина</a>
</li>

<!-- При непустой корзине -->
<li class="userbtn_i topbarfix_cart js-topbarfixNotEmptyCart" data-bind=" visible: cart().length > 0 " style="display: none">

    <a href="<?= $page->url('cart') ?>" class="topbarfix_cart_tl">
        <span class="topbarfix_cart_tx">Корзина</span>
        <strong class="topbarfix_cart_qn topbarfix_cartQuan" data-bind="text: cart().length"></strong>
    </a>

    <div class="topbarfix_dd topbarfix_cartOn" >
        <div class="topbarfix-cart-wrap">
            <table class="table-cart">
                <tbody data-bind="foreach: cart">
                    <tr class="table-cart__i">
                        <td class="table-cart__img-wrap" valign="middle">
                            <a data-bind="attr: { href: link }">
                                <img class="table-cart__img" src="" data-bind="attr: { src: img }"/>
                            </a>
                        </td>
                        <td class="table-cart__name">
                            <a class="table-cart__name-inn" data-bind="attr: { href: link, title: name }, text: name"></a>
                        </td>
                        <td class="table-cart__inf">
                            <span class="price"><span data-bind="html: window.printPrice(price)"></span> &nbsp;<span class="rubl">p</span></span>
                            <span class="quan"><!--ko text: quantity--><!--/ko--> шт.</span>
                            <a class="del jsCartDelete" data-bind="attr: { href: deleteUrl }">удалить</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- для кнопки с иконкой btnBuy-inf -->
        <div class="btnBuy quickOrder" data-bind="css: {'btnBuy-inf': infoIconVisible() }"><a href="<?= $page->url('order') ?>" class="btnBuy__eLink quickOrder__link">Оформить заказ</a></div>

        <div class="buyInfo" data-bind="visible: !infoIconVisible() && infoBlock_1Visible() ">
            До бесплатного самовывоза осталось
            <div class="buyInfo_pr"><span data-bind="text: ENTER.config.pageConfig.selfDeliveryLimit - cartSum()">175</span> <span class="rubl">p</span></div>
            <a class="buyInfo_lk jsAbSelfDeliveryLink" href="/slices/all_labels">Выбрать товары по суперценам</a> >
        </div>

        <div class="buyInfo" data-bind="visible: !infoIconVisible() && infoBlock_2Visible() ">
            <div class="buyInfo_self">Самовывоз<br/>БЕСПЛАТНО</div>
        </div>
    </div>

    <div class="hintDd"><!-- если похожии товары есть то добавляем класс mhintDdOn -->
    </div>

</li>