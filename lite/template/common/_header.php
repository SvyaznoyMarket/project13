<div class="header">
    <div class="wrapper table">
        <div class="header__side header__logotype table-cell">
            <a href="/" class="logotype"></a>
        </div>

        <div class="header__center table-cell">
            <div class="header__line header__line--top">
                <a href="" class="location dotted js-popup-show jsRegionSelection" data-popup="region"><?= \App::user()->getRegion()->getName() ?></a>

                <ul class="header-shop-info">
                    <li class="header-shop-info__item"><a href="" class="header-shop-info__link underline">Магазины и самовывоз</a></li>
                    <li class="header-shop-info__item"><a href="" class="header-shop-info__link underline">Доставка</a></li>
                    <li class="header-shop-info__item"><a href="" class="header-shop-info__link underline">Оплата</a></li>
                    <li class="header-shop-info__item"><a href="" class="header-shop-info__link underline">Партнерам</a></li>
                </ul>

                <div class="phone">
                    <span class="phone__text">+7 495 775-00-06</span>

                    <a href="http://zingaya.com/widget/e990d486d664dfcff5f469b52f6bdb62" title="" class="phone-order" onclick="window.open(typeof(_gat)=='undefined'?this.href+'?referrer='+escape(window.location.href):_gat._getTrackerByName()._getLinkerUrl(this.href+'?referrer='+escape(window.location.href)), '_blank', 'width=236,height=220,resizable=no,toolbar=no,menubar=no,location=no,status=no'); return false;"><i class="phone-order__icon i-controls i-controls--phone"></i>
                        <span class="phone-order__text dotted">Звонок с сайта</span>
                    </a>
                </div>
            </div>

            <div class="header__line header__line--bottom">

                <?= $page->render('common/_search') ?>

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
                                        <div class="notice-cart__price"><!--ko text: formattedPrice--><!--/ko--> p</div>
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
                <!-- если листинг -->
                <!--div class="fltrSet_tggl fltrSet_tggl-up">
                    <span class="fltrSet_tggl_tx">Бренды и параметры</span>
                </div-->
                <!--/ если листинг -->

                <!-- если карточка товара -->
                <div class="header-buy">
                    <div class="header-buy__product header-buy__cell">
                        <div class="header-buy__product-img header-buy__cell"><img src="http://2.imgenter.ru/uploads/media/46/4c/e2/thumb_c661_category_163x163.jpeg" alt="" class="image"></div>
                        <div class="header-buy__product-name header-buy__cell">1,5-спальный комплект постельного белья "Олени и снежинки"</div>
                    </div>

                    <div class="header-buy__price header-buy__cell">
                        <div class="goods__price-old"><span class="line-through">1223</span> р</div>
                        <div class="goods__price-now">19&thinsp;819 p</div>
                    </div>

                    <div class="header-buy__btn header-buy__cell">
                        <a href="" class="goods__btn btn-primary btn-primary_middle">Купить</a>
                    </div>
                </div>
                <!--/ если карточка товара -->

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
                                        <div class="notice-cart__price"><!--ko text: formattedPrice--><!--/ko--> p</div>
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