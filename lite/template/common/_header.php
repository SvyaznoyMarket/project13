<div class="header table">
    <div class="header__side header__logotype table-cell">
        <a href="" class="logotype"></a>
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

                <a href="" title="" class="phone-order"><i class="phone-order__icon i-controls i-controls--phone"></i> <span class="phone-order__text dotted">Звонок с сайта</span></a>
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

    <div class="header__side header-cart table-cell">
        <div class="notice-show">
            <a href="" title="">
                <i class="header-cart__icon i-controls i-controls--cart"><span class="header-cart__count disc-count">99+</span></i>
                <span class="header-cart__text">Корзина</span>
            </a>

            <div class="notice-dd notice-dd_cart">
                <div class="notice-dd__inn">
                    <div class="notice-dd__height">
                        <ul class="notice-cart">
                            <li class="notice-cart__row">
                                <a class="notice-cart__img notice-cart__cell" href="">
                                    <img src="http://a.imgenter.ru/uploads/media/23/ea/50/thumb_fdd5_product_160.jpeg" alt="" class="image">
                                </a>

                                <a class="notice-cart__name notice-cart__cell" href="">
                                    Портативная акустическая система Promate Mulotov
                                </a>

                                <div class="notice-cart__desc notice-cart__cell">
                                    <div class="notice-cart__price">1 344 590 p</div>
                                    <span class="notice-cart__quan">1 шт.</span>
                                    <a href="" class="notice-cart__del"><i class="notice-cart__icon icon-clear"></i></a>
                                </div>
                            </li>

                            <li class="notice-cart__row">
                                <a class="notice-cart__img notice-cart__cell" href="">
                                    <img src="http://a.imgenter.ru/uploads/media/23/ea/50/thumb_fdd5_product_160.jpeg" alt="" class="image">
                                </a>

                                <a class="notice-cart__name notice-cart__cell" href="">
                                    Портативная акустическая система Promate Mulotov
                                </a>

                                <div class="notice-cart__desc notice-cart__cell">
                                    <div class="notice-cart__price">1 344 590 p</div>
                                    <span class="notice-cart__quan">1 шт.</span>
                                    <a href="" class="notice-cart__del"><i class="notice-cart__icon icon-clear"></i></a>
                                </div>
                            </li>

                            <li class="notice-cart__row">
                                <a class="notice-cart__img notice-cart__cell" href="">
                                    <img src="http://a.imgenter.ru/uploads/media/23/ea/50/thumb_fdd5_product_160.jpeg" alt="" class="image">
                                </a>

                                <a class="notice-cart__name notice-cart__cell" href="">
                                    Портативная акустическая система Promate Mulotov
                                </a>

                                <div class="notice-cart__desc notice-cart__cell">
                                    <div class="notice-cart__price">44 590 p</div>
                                    <span class="notice-cart__quan">1 шт.</span>
                                    <a href="" class="notice-cart__del"><i class="notice-cart__icon icon-clear"></i></a>
                                </div>
                            </li>

                            <li class="notice-cart__row">
                                <a class="notice-cart__img notice-cart__cell" href="">
                                    <img src="http://a.imgenter.ru/uploads/media/23/ea/50/thumb_fdd5_product_160.jpeg" alt="" class="image">
                                </a>

                                <a class="notice-cart__name notice-cart__cell" href="">
                                    Портативная акустическая система Promate Mulotov
                                </a>

                                <div class="notice-cart__desc notice-cart__cell">
                                    <div class="notice-cart__price">344 590 p</div>
                                    <span class="notice-cart__quan">1 шт.</span>
                                    <a href="" class="notice-cart__del"><i class="notice-cart__icon icon-clear"></i></a>
                                </div>
                            </li>

                            <li class="notice-cart__row">
                                <a class="notice-cart__img notice-cart__cell" href="">
                                    <img src="http://a.imgenter.ru/uploads/media/23/ea/50/thumb_fdd5_product_160.jpeg" alt="" class="image">
                                </a>

                                <a class="notice-cart__name notice-cart__cell" href="">
                                    Портативная акустическая система Promate Mulotov
                                </a>

                                <div class="notice-cart__desc notice-cart__cell">
                                    <div class="notice-cart__price">4 590 p</div>
                                    <span class="notice-cart__quan">1 шт.</span>
                                    <a href="" class="notice-cart__del"><i class="notice-cart__icon icon-clear"></i></a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <a href="" class="notice-cart__on-cart btn-simple btn-simple_width">Перейти в корзину</a>
                <a href="" class="btn-primary btn-primary_bigger btn-primary_width">Оформить заказ</a>
            </div>
        </div>
    </div>
</div>