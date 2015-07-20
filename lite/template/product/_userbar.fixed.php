<?
/**
 * @var $product \Model\Product\Entity
 */
$helper = \App::helper();
?>

<!-- параплашка -->
<div class="header header_fix">
    <div class="wrapper table">
        <div class="header__side header__logotype table-cell">
            <a href="/" class="logotype"></a>
        </div>

        <div class="header__center table-cell">
            <div class="header__line header__line_top">
                <ul class="bread-crumbs bread-crumbs_mini">
                    <li class="bread-crumbs__item"><a href="/catalog/electronics" class="bread-crumbs__link underline">Электроника</a></li>
                    <li class="bread-crumbs__item">Игры и консоли</li>
                </ul>
            </div>

            <div class="header__line header__line_bottom">

                <!-- карточка товара -->
                <div class="header-buy">
                    <div class="header-buy__product header-buy__cell">
                        <div class="header-buy__product-img header-buy__cell"><img src="<?= $product->getMainImageUrl('product_120') ?>" alt="" class="image"></div>
                        <div class="header-buy__product-name header-buy__cell"><?= $product->getName() ?></div>
                    </div>

                    <div class="header-buy__price header-buy__cell">
                        <? if ($product->getPriceOld()) : ?><div class="goods__price-old"><span class="line-through"><?= $helper->formatPrice($product->getPriceOld()) ?></span> <span class="rubl-css">P</span></div><? endif ?>
                        <div class="goods__price-now"><?= $helper->formatPrice($product->getPrice()) ?> <span class="rubl-css">P</span></div>
                    </div>

                    <div class="header-buy__btn header-buy__cell">
                        <?= $helper->render('product/_button.buy', [
                            'product' => $product,
                            'class'   => 'btn-primary_middle'
                        ]) ?>
                    </div>
                </div>
                <!--/ карточка товара -->

                <ul class="user-controls">
                    <!--li class="user-controls__item user-controls__item_compare">
                        <a href="" class="user-controls__link">
                            <span class="user-controls__icon"><i class="i-controls i-controls--compare"></i></span>
                            <span class="user-controls__text">Сравнение</span>
                        </a>
                    </li>
                    <li class="user-controls__item user-controls__item_user">
                        <a href="" class="user-controls__link js-popup-show" data-popup="login">
                            <span class="user-controls__icon"><i class="i-controls i-controls--user"></i></span>
                            <span class="user-controls__text">Войти</span>
                        </a>
                    </li-->

                    <li class="user-controls__item user-controls__item_compare active">
                        <a href="" class="user-controls__link">
                            <span class="user-controls__icon"><i class="i-controls i-controls--compare"></i></span>
                            <span class="user-controls__text">Сравнение</span>
                        </a>

                        <div class="notice-dd notice-dd_compare" style="display: none">
                            <div class="notice-compare">
                                <div class="notice-compare__title">Товар добавлен к сравнению</div>

                                <div class="notice-compare__img"><img src="http://a.imgenter.ru/uploads/media/ae/d3/e0/thumb_bcc6_product_160.jpeg" alt="" class="image"></div>
                                <div class="notice-compare__desc">Чехол для Apple iPhone6 XtremeMac Microshield Acc Чехол для App XtremeMac</div>
                            </div>
                        </div>
                    </li>

                    <li class="user-controls__item user-controls__item_user active">
                        <a href="" class="user-controls__link js-popup-show" data-popup="login">
                            <span class="user-controls__icon"><i class="i-controls i-controls--user"></i></span>
                            <span class="user-controls__text">Бурлакова Татьяна Владимировна</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="header__side header-cart table-cell js-module-require" <? if (\App::user() && \App::user()->getCart()->count()) : ?>data-module="enter.cart"<? endif ?> >

            <div class="notice-show jsKnockoutCart">
                <a href="" title="">
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
    </div>
</div>
<!--/ параплашка -->
