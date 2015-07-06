<!doctype html>
<html class="no-js" lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="apple-touch-icon" href="apple-touch-icon.png">
        <!-- Place favicon.ico in the root directory -->

        <?= $page->slotStylesheet() ?>
        <?= $page->slotHeadJavascript() ?>
    </head>

    <body>
        <div class="wrapper">
            <div class="header table">
                <div class="header__side header__logotype table-cell">
                    <a href="" class="logotype"></a>
                </div>

                <div class="header__center table-cell">
                    <div class="header__line header__line--top">
                        <a href="" class="location dotted">Набережные челны</a>

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
                        <form class="search-bar">
                            <i class="search-bar__icon i-controls i-controls--search"></i>
                            <input type="text" class="search-bar__it it" placeholder="Поиск товаров">
                            <button class="search-bar__btn btn-normal">Найти</button>
                        </form>

                        <ul class="user-controls">
                            <li class="user-controls__item">
                                <a href="" class="user-controls__link">
                                    <span class="user-controls__icon"><i class="i-controls i-controls--compare"></i></span>
                                    <span class="user-controls__text">Сравнение</span>
                                </a>
                            </li>
                            <li class="user-controls__item">
                                <a href="" class="user-controls__link">
                                    <span class="user-controls__icon"><i class="i-controls i-controls--user"></i></span>
                                    <span class="user-controls__text">Войти</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="header__side header-cart table-cell">
                    <i class="header-cart__icon i-controls i-controls--cart"><span class="header-cart__count disc-count">99+</span></i>
                    <span class="header-cart__text">Корзина</span>
                </div>
            </div>

            <hr class="hr-orange">

            <aside class="left-bar">
                <nav class="site-menu">
                    <li class="site-menu__item">
                        <a href="" class="site-menu__link">
                            <span class="site-menu__icon"></span>
                            <span class="site-menu__text"></span>
                        </a>
                    </li>
                    <li class="site-menu__item">
                        <a href="" class="site-menu__link">
                            <span class="site-menu__icon"></span>
                            <span class="site-menu__text"></span>
                        </a>
                    </li>
                    <li class="site-menu__item">
                        <a href="" class="site-menu__link">
                            <span class="site-menu__icon"></span>
                            <span class="site-menu__text"></span>
                        </a>
                    </li>
                    <li class="site-menu__item">
                        <a href="" class="site-menu__link">
                            <span class="site-menu__icon"></span>
                            <span class="site-menu__text"></span>
                        </a>
                    </li>
                    <li class="site-menu__item">
                        <a href="" class="site-menu__link">
                            <span class="site-menu__icon"></span>
                            <span class="site-menu__text"></span>
                        </a>
                    </li>
                    <li class="site-menu__item">
                        <a href="" class="site-menu__link">
                            <span class="site-menu__icon"></span>
                            <span class="site-menu__text"></span>
                        </a>
                    </li>
                </nav>
            </aside>

            <main class="content">
                <div class="banner-section">
                    <ul class="banner-section-list">
                        <li class="banner-section-list__item">
                            <a href="" class="banner-section-list__link">
                                <img src="" alt="" class="banner-section-list__img">
                                <div class="banner-section-list__desc"></div>
                            </a>
                        </li>

                        <li class="banner-section-list__item">
                            <a href="" class="banner-section-list__link">
                                <img src="" alt="" class="banner-section-list__img">
                                <div class="banner-section-list__desc"></div>
                            </a>
                        </li>

                        <li class="banner-section-list__item">
                            <a href="" class="banner-section-list__link">
                                <img src="" alt="" class="banner-section-list__img">
                                <div class="banner-section-list__desc"></div>
                            </a>
                        </li>
                    </ul>

                    <ul class="banner-section-nav">
                        <li class="banner-section-nav__item"><a href="" class="banner-section-nav__link"></a></li>
                        <li class="banner-section-nav__item"><a href="" class="banner-section-nav__link"></a></li>
                        <li class="banner-section-nav__item"><a href="" class="banner-section-nav__link"></a></li>
                        <li class="banner-section-nav__item"><a href="" class="banner-section-nav__link"></a></li>
                    </ul>
                </div>

                <ul class="shop-adv">
                    <li class="shop-adv__item">
                        <a href="" class="shop-adv__link">
                            <span class="shop-adv__left">
                                <i class="shop-adv__icon"></i>
                            </span>

                            <span class="shop-adv__right">
                                <strong class="shop-adv__title">Доставка</strong>
                                <span class="shop-adv__text">Доставляем по всей России</span>
                            </span>
                        </a>
                    </li>

                    <li class="shop-adv__item">
                        <a href="" class="shop-adv__link">
                            <span class="shop-adv__left">
                                <i class="shop-adv__icon"></i>
                            </span>

                            <span class="shop-adv__right">
                                <strong class="shop-adv__title">Самовывоз</strong>
                                <span class="shop-adv__text">Более 1300 точек выдачи</span>
                            </span>
                        </a>
                    </li>

                    <li class="shop-adv__item">
                        <a href="" class="shop-adv__link">
                            <span class="shop-adv__left">
                                <i class="shop-adv__icon"></i>
                            </span>

                            <span class="shop-adv__right">
                                <strong class="shop-adv__title">Удобно платить</strong>
                                <span class="shop-adv__text">Способ оплаты на твой вкус</span>
                            </span>
                        </a>
                    </li>

                    <li class="shop-adv__item">
                        <a href="" class="shop-adv__link">
                            <span class="shop-adv__left">
                                <i class="shop-adv__icon"></i>
                            </span>

                            <span class="shop-adv__right">
                                <strong class="shop-adv__title">Акции</strong>
                                <span class="shop-adv__text">Выгодные предложения</span>
                            </span>
                        </a>
                    </li>
                </ul>

                <div class="section">
                    <div class="section__title">Мы рекомендуем</div>

                    <div class="section__content">
                        <div class="slider-section">
                            <ul class="slider-goods">
                                <li class="slider-goods__item">
                                    <div class="sticker"></div>

                                    <a href="" class="slider-goods__img">
                                        <img src="" alt="" class="">
                                    </a>

                                    <a class="slider-goods__name">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</a>

                                    <div class="slider-goods__price-old">45 990 <span class="rubl">p</span></div>
                                    <div class="slider-goods__price-now">45 990 <span class="rubl">p</span></div>

                                    <a class="slider-goods__btn">Купить</a>
                                </li>

                                <li class="slider-goods__item">
                                    <div class="sticker"></div>

                                    <a href="" class="slider-goods__img">
                                        <img src="" alt="" class="">
                                    </a>

                                    <a class="slider-goods__name">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</a>

                                    <div class="slider-goods__price-old">45 990 <span class="rubl">p</span></div>
                                    <div class="slider-goods__price-now">45 990 <span class="rubl">p</span></div>

                                    <a class="slider-goods__btn">Купить</a>
                                </li>

                                <li class="slider-goods__item">
                                    <div class="sticker"></div>

                                    <a href="" class="slider-goods__img">
                                        <img src="" alt="" class="">
                                    </a>

                                    <a class="slider-goods__name">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</a>

                                    <div class="slider-goods__price-old">45 990 <span class="rubl">p</span></div>
                                    <div class="slider-goods__price-now">45 990 <span class="rubl">p</span></div>

                                    <a class="slider-goods__btn">Купить</a>
                                </li>

                                <li class="slider-goods__item">
                                    <div class="sticker"></div>

                                    <a href="" class="slider-goods__img">
                                        <img src="" alt="" class="">
                                    </a>

                                    <a class="slider-goods__name">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</a>

                                    <div class="slider-goods__price-old">45 990 <span class="rubl">p</span></div>
                                    <div class="slider-goods__price-now">45 990 <span class="rubl">p</span></div>

                                    <a class="slider-goods__btn">Купить</a>
                                </li>

                                <li class="slider-goods__item">
                                    <div class="sticker"></div>

                                    <a href="" class="slider-goods__img">
                                        <img src="" alt="" class="">
                                    </a>

                                    <a class="slider-goods__name">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</a>

                                    <div class="slider-goods__price-old">45 990 <span class="rubl">p</span></div>
                                    <div class="slider-goods__price-now">45 990 <span class="rubl">p</span></div>

                                    <a class="slider-goods__btn">Купить</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="section">
                    <div class="section__title">Популярные товары</div>

                    <div class="section__content">
                        <div class="slider-section">
                            <ul class="slider-goods">
                                <li class="slider-goods__item">
                                    <div class="sticker"></div>

                                    <a href="" class="slider-goods__img">
                                        <img src="" alt="" class="">
                                    </a>

                                    <a class="slider-goods__name">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</a>

                                    <div class="slider-goods__price-old">45 990 <span class="rubl">p</span></div>
                                    <div class="slider-goods__price-now">45 990 <span class="rubl">p</span></div>

                                    <a class="slider-goods__btn">Купить</a>
                                </li>

                                <li class="slider-goods__item">
                                    <div class="sticker"></div>

                                    <a href="" class="slider-goods__img">
                                        <img src="" alt="" class="">
                                    </a>

                                    <a class="slider-goods__name">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</a>

                                    <div class="slider-goods__price-old">45 990 <span class="rubl">p</span></div>
                                    <div class="slider-goods__price-now">45 990 <span class="rubl">p</span></div>

                                    <a class="slider-goods__btn">Купить</a>
                                </li>

                                <li class="slider-goods__item">
                                    <div class="sticker"></div>

                                    <a href="" class="slider-goods__img">
                                        <img src="" alt="" class="">
                                    </a>

                                    <a class="slider-goods__name">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</a>

                                    <div class="slider-goods__price-old">45 990 <span class="rubl">p</span></div>
                                    <div class="slider-goods__price-now">45 990 <span class="rubl">p</span></div>

                                    <a class="slider-goods__btn">Купить</a>
                                </li>

                                <li class="slider-goods__item">
                                    <div class="sticker"></div>

                                    <a href="" class="slider-goods__img">
                                        <img src="" alt="" class="">
                                    </a>

                                    <a class="slider-goods__name">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</a>

                                    <div class="slider-goods__price-old">45 990 <span class="rubl">p</span></div>
                                    <div class="slider-goods__price-now">45 990 <span class="rubl">p</span></div>

                                    <a class="slider-goods__btn">Купить</a>
                                </li>

                                <li class="slider-goods__item">
                                    <div class="sticker"></div>

                                    <a href="" class="slider-goods__img">
                                        <img src="" alt="" class="">
                                    </a>

                                    <a class="slider-goods__name">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</a>

                                    <div class="slider-goods__price-old">45 990 <span class="rubl">p</span></div>
                                    <div class="slider-goods__price-now">45 990 <span class="rubl">p</span></div>

                                    <a class="slider-goods__btn">Купить</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="section">
                    <div class="section__title">Ещё у нас покупают</div>

                    <div class="section__content">
                        <div class="catalog grid-4col">
                            <div class="catalog__item grid-4col__item">
                                <a href="" class="catalog__link"></a>
                                <div class="catalog__img"><img src="" alt=""></div>
                                <div class="catalog__text"></div>
                            </div>
                            <div class="catalog__item grid-4col__item">
                                <a href="" class="catalog__link"></a>
                                <div class="catalog__img"><img src="" alt=""></div>
                                <div class="catalog__text"></div>
                            </div>
                            <div class="catalog__item grid-4col__item">
                                <a href="" class="catalog__link"></a>
                                <div class="catalog__img"><img src="" alt=""></div>
                                <div class="catalog__text"></div>
                            </div>
                            <div class="catalog__item grid-4col__item">
                                <a href="" class="catalog__link"></a>
                                <div class="catalog__img"><img src="" alt=""></div>
                                <div class="catalog__text"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section">
                    <div class="section__title">Популярные бренды</div>

                    <div class="section__content">
                        <div class="brand-list grid-9col">
                            <div class="brand-list__item grid-9col__item">
                                <a href="" class="brand-list___link"></a><img src="" alt="" class="brand-list__img">
                            </div>
                            <div class="brand-list__item grid-9col__item">
                                <a href="" class="brand-list___link"></a><img src="" alt="" class="brand-list__img">
                            </div>
                            <div class="brand-list__item grid-9col__item">
                                <a href="" class="brand-list___link"></a><img src="" alt="" class="brand-list__img">
                            </div>
                            <div class="brand-list__item grid-9col__item">
                                <a href="" class="brand-list___link"></a><img src="" alt="" class="brand-list__img">
                            </div>
                            <div class="brand-list__item grid-9col__item">
                                <a href="" class="brand-list___link"></a><img src="" alt="" class="brand-list__img">
                            </div>
                            <div class="brand-list__item grid-9col__item">
                                <a href="" class="brand-list___link"></a><img src="" alt="" class="brand-list__img">
                            </div>
                            <div class="brand-list__item grid-9col__item">
                                <a href="" class="brand-list___link"></a><img src="" alt="" class="brand-list__img">
                            </div>
                            <div class="brand-list__item grid-9col__item">
                                <a href="" class="brand-list___link"></a><img src="" alt="" class="brand-list__img">
                            </div>
                            <div class="brand-list__item grid-9col__item">
                                <a href="" class="brand-list___link"></a><img src="" alt="" class="brand-list__img">
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>

        <div class="footer">
            <div class="footer__left">
                <ul class="footer-list">
                    <li class="footer-list__item"><a href="" class="footer-list__link"></a></li>
                    <li class="footer-list__item"><a href="" class="footer-list__link"></a></li>
                    <li class="footer-list__item"><a href="" class="footer-list__link"></a></li>
                    <li class="footer-list__item"><a href="" class="footer-list__link"></a></li>
                    <li class="footer-list__item"><a href="" class="footer-list__link"></a></li>
                    <li class="footer-list__item"><a href="" class="footer-list__link"></a></li>
                    <li class="footer-list__item"><a href="" class="footer-list__link"></a></li>
                    <li class="footer-list__item"><a href="" class="footer-list__link"></a></li>
                </ul>

                <div class="subscribe">
                    <div class="subscribe__title"></div>

                    <form action="" class="subscribe-form">
                        <input type="text" class="subscribe-form__it">
                        <button class="subscribe-form__btn"></button>

                        <input class="subscribe-form__check" type="checkbox" name="" id="">
                        <label class="subscribe-form__label-check"></label>
                    </form>


                </div>
            </div>

            <div class="footer__right">
                <ul class="footer-external">
                    <li class="footer-external__item"><strong class="footer-external__title"></strong></li>
                    <li class="footer-external__item"><a href="" class="footer-external__link"><i class="footer-external__icon"></i></a></li>
                    <li class="footer-external__item"><a href="" class="footer-external__link"><i class="footer-external__icon"></i></a></li>
                    <li class="footer-external__item"><a href="" class="footer-external__link"><i class="footer-external__icon"></i></a></li>
                </ul>

                <ul class="footer-external">
                    <li class="footer-external__item"><strong class="footer-external__title"></strong></li>
                    <li class="footer-external__item"><a href="" class="footer-external__link"><i class="footer-external__icon"></i></a></li>
                    <li class="footer-external__item"><a href="" class="footer-external__link"><i class="footer-external__icon"></i></a></li>
                </ul>
            </div>



            <footer>

            </footer>
        </div>
    </body>
</html>
