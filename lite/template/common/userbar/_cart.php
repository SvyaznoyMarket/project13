<div class="header__side header-cart table-cell js-module-require" <? if (\App::user() && \App::user()->getCart()->count()) : ?>data-module="enter.cart"<? endif ?> >

    <div class="notice-show jsKnockoutCart">
        <a href="<?= \App::router()->generate('cart') ?>" title="">
            <i class="header-cart__icon i-controls i-controls--cart"><span class="header-cart__count disc-count" style="display: none" data-bind="text: getProductQuantity, visible: getProductQuantity"></span></i>
            <span class="header-cart__text">Корзина</span>
        </a>

        <div class="notice-dd notice-dd_cart jsCartNotice">
            <div class="notice-dd__inn" style="display: none" data-bind="visible: getProductQuantity">
                <div class="notice-dd__height">
                    <ul class="notice-cart">

                        <!-- ko foreach: cart -->

                        <li class="notice-cart__row">
                            <a class="notice-cart__img notice-cart__cell" data-bind="attr: { href: link }">
                                <img alt="" class="image" data-bind="attr: { src: img }">
                            </a>

                            <a class="notice-cart__name notice-cart__cell" href="" data-bind="text: name, attr: { href: link }"></a>

                            <div class="notice-cart__desc notice-cart__cell">
                                <div class="notice-cart__price"><!--ko text: formattedPrice--><!--/ko--> <span class="rubl-css">P</span></div>
                                <span class="notice-cart__quan"><!--ko text: quantity()--><!--/ko--> шт.</span>
                                <a class="notice-cart__del" data-bind="attr: { href: deleteUrl }, click: $parent.deleteProduct"><i class="notice-cart__icon icon-clear"></i></a>
                            </div>
                        </li>

                        <!--/ko -->

                    </ul>
                </div>
            </div>

            <a href="" class="notice-cart__on-cart btn-simple btn-simple_width">Перейти в корзину</a>
            <a href="" class="btn-primary btn-primary_bigger btn-primary_width">Оформить заказ</a>
        </div>
    </div>
</div>