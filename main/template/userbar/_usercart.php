<?
    $isOrderWithCart = \App::abTest()->isOrderWithCart();
?>

<!-- При пустой корзине -->
<li class="userbtn_i topbarfix_cart mEmpty" data-bind=" visible: cart().products().length == 0 ">
    <a href="/cart" class="topbarfix_cart_tl"><i class="i-header i-header--cart"></i> <span class="topbarfix-cart__tx">Корзина</span></a>
</li>

<!-- При непустой корзине -->
<li class="userbtn_i topbarfix_cart js-topbarfixNotEmptyCart" data-bind=" visible: cart().products().length > 0 " style="display: none">

    <a href="<?= $page->url('cart') ?>" class="topbarfix_cart_tl">
        <i class="i-header i-header--cart"></i>
        <span class="topbarfix-cart__tx">Корзина</span>
        <span class="topbarfix_cart_qn topbarfix_cartQuan" data-bind="text: cart().products().length"></span>
    </a>

    <div class="userbar-dd minicart topbarfix_cartOn" >
        <div class="topbarfix-cart-wrap" data-bind="css: {'min-sum': isMinOrderSumVisible() }"><!--сюда добавить класс "min-sum" если корзина у нас для выводит сообщение о минимальной сумме заказа-->
            <table class="table-cart">
                <tbody data-bind="foreach: cart().products()">
                    <tr class="table-cart__i" data-bind="css: {'unaval': !isAvailable } ">
                        <td class="table-cart__img-wrap" valign="middle">
                            <a data-bind="attr: { href: link }">
                                <img class="table-cart__img" src="" data-bind="attr: { src: img }"/>
                            </a>
                        </td>
                        <td class="table-cart__name">
                            <a class="table-cart__name-inn" data-bind="attr: { href: link, title: name }, text: name"></a>
                            <span class="table-cart__unavalible-text" data-bind="visible: !isAvailable"></span>
                        </td>
                        <td class="table-cart__inf">
                            <span class="price"><span data-bind="html: window.printPrice(price)"></span> &nbsp;<span class="rubl">p</span></span>
                            <span class="quan"><!--ko text: quantity--><!--/ko--> шт.</span>
                            <a class="del jsCartDelete" data-bind="attr: { href: deleteUrl, 'data-product-id': id, 'data-product-article': article }">удалить</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- для кнопки с иконкой btnBuy-inf -->
        <div class="<?= 'btn-container btn-container--quick-buy' ?>" data-bind="visible: !isMinOrderSumVisible()">
            <a
                href="<?= $isOrderWithCart ? $page->url('orderV3.delivery') : $page->url('orderV3') ?>"
                class="<?= 'btn-type btn-type--buy' ?>"
            >
                <?= ((isset($cartTextInOrderButton) && (true === $cartTextInOrderButton)) ? 'В корзину' : 'Оформить заказ') ?>
            </a>
        </div>

        <? if (\App::abTest()->isOrderMinSumRestriction()) : ?>
        <!-- Минимальная сумма заказа -->
        <div class="deliv-free-info" data-bind="visible: isMinOrderSumVisible()">
            <span class="deliv-free-info__min-sum">Минимальная стоимость заказа 990 <span class="rubl">p</span></span>
            <span class="deliv-free-info__intro">До оформления заказа осталось</span>
            <span class="deliv-free-info__remain-sum"><span data-bind="text: minOrderSum - cart().sum()"><?= \App::config()->minOrderSum ?></span>&thinsp;<span class="rubl">p</span></span>
            <a href="/slices/all_labels" class="deliv-free-info__sale-lnk">Выбрать товары по суперцене</a>
        </div>

        <? endif ?>

    </div>

    <div class="hintDd"><!-- если похожии товары есть то добавляем класс mhintDdOn -->
    </div>
</li>