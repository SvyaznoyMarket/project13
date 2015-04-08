<!-- При пустой корзине -->
<li class="userbtn_i topbarfix_cart mEmpty" data-bind=" visible: cart().length == 0 ">
    <a href="/cart" class="topbarfix_cart_tl"><i class="i-header i-header--cart"></i> <span class="topbarfix-cart__tx">Корзина</span></a>
</li>

<!-- При непустой корзине -->
<li class="userbtn_i topbarfix_cart js-topbarfixNotEmptyCart" data-bind=" visible: cart().length > 0 " style="display: none">

    <a href="<?= $page->url('cart') ?>" class="topbarfix_cart_tl">
        <i class="i-header i-header--cart"></i>
        <span class="topbarfix-cart__tx">Корзина</span>
        <span class="topbarfix_cart_qn topbarfix_cartQuan" data-bind="text: cart().length"></span>
    </a>

    <div class="userbar-dd minicart topbarfix_cartOn" >
        <div class="topbarfix-cart-wrap">
            <table class="table-cart">
                <tbody data-bind="foreach: cart">
                    <tr class="table-cart__i">
                        <td class="table-cart__img-wrap">
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

        <div class="minicart__buy">
            <a href="<?= $page->url('order') ?>" class="minicart__btn btn-type btn-type--buy btn-type--longer">Оформить заказ</a>
        </div>
    </div>

    <div class="hintDd"><!-- если похожии товары есть то добавляем класс mhintDdOn -->
    </div>
</li>