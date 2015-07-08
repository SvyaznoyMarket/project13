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
        <link href='http://fonts.googleapis.com/css?family=Roboto&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
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

            <script>
                $(function(){
                     $('.js-banners-slider').slick({
                        slidesToShow: 1,
                        slidesToScroll: 1,
                        arrows: false,
                        vertical: true,
                        infinite: false,
                        centerMode: false,
                        asNavFor: '.js-banners-slider-nav'
                    });

                    $('.js-banners-slider-nav').slick({
                        slidesToShow: 4,
                        slidesToScroll: 1,
                        asNavFor: '.js-banners-slider',
                        dots: false,
                        vertical: true,
                        centerMode: false,
                        focusOnSelect: true,
                        infinite: false,
                        prevArrow: '.js-banners-slider-nav-btn-prev',
                        nextArrow: '.js-banners-slider-nav-btn-next'
                    });
                })
            </script>

            <div class="banner-section">
                <div class="banner-section-list js-banners-slider">
                    <div class="banner-section-list__item">
                        <a href="" class="banner-section-list__link">
                            <img class="banner-section-list__item" src="http://fs01.enter.ru/4/1/960x240/67/413291.jpg" alt="" class="banner-section-list__img">
                        </a>
                    </div>

                    <div class="banner-section-list__item">
                        <a href="" class="banner-section-list__link">
                            <img class="banner-section-list__item" src="http://fs01.enter.ru/4/1/960x240/a5/411502.jpg" alt="" class="banner-section-list__img">
                        </a>
                    </div>

                    <div class="banner-section-list__item">
                        <a href="" class="banner-section-list__link">
                            <img class="banner-section-list__item" src="http://fs01.enter.ru/4/1/960x240/48/408940.jpg" alt="" class="banner-section-list__img">
                        </a>
                    </div>

                    <div class="banner-section-list__item">
                        <a href="" class="banner-section-list__link">
                            <img class="banner-section-list__item" src="http://fs01.enter.ru/4/1/960x240/67/413291.jpg" alt="" class="banner-section-list__img">
                        </a>
                    </div>

                    <div class="banner-section-list__item">
                        <a href="" class="banner-section-list__link">
                            <img class="banner-section-list__item" src="http://fs01.enter.ru/4/1/960x240/a5/411502.jpg" alt="" class="banner-section-list__img">
                        </a>
                    </div>

                    <div class="banner-section-list__item">
                        <a href="" class="banner-section-list__link">
                            <img class="banner-section-list__item" src="http://fs01.enter.ru/4/1/960x240/48/408940.jpg" alt="" class="banner-section-list__img">
                        </a>
                    </div>

                    <div class="banner-section-list__item">
                        <a href="" class="banner-section-list__link">
                            <img class="banner-section-list__item" src="http://fs01.enter.ru/4/1/960x240/67/413291.jpg" alt="" class="banner-section-list__img">
                        </a>
                    </div>
                </div>

                <div class="banner-section-nav">
                    <div class="js-banners-slider-nav">
                        <div class="banner-section-nav__item"><img class="banner-section-nav__img" src="http://fs01.enter.ru/4/1/220x50/67/413291.jpg" alt=""></div>
                        <div class="banner-section-nav__item"><img class="banner-section-nav__img" src="http://fs01.enter.ru/4/1/220x50/a5/411502.jpg" alt=""></div>
                        <div class="banner-section-nav__item"><img class="banner-section-nav__img" src="http://fs01.enter.ru/4/1/220x50/48/408940.jpg" alt=""></div>
                        <div class="banner-section-nav__item"><img class="banner-section-nav__img" src="http://fs01.enter.ru/4/1/220x50/aa/411492.jpg" alt=""></div>
                        <div class="banner-section-nav__item"><img class="banner-section-nav__img" src="http://fs01.enter.ru/4/1/220x50/a0/411495.jpg" alt=""></div>
                        <div class="banner-section-nav__item"><img class="banner-section-nav__img" src="http://fs01.enter.ru/4/1/220x50/a5/411502.jpg" alt=""></div>
                        <div class="banner-section-nav__item"><img class="banner-section-nav__img" src="http://fs01.enter.ru/4/1/220x50/67/413291.jpg" alt=""></div>
                    </div>

                    <button class="banner-section-nav__btn banner-section-nav__btn_prev js-banners-slider-nav-btn-prev"></button>
                    <button class="banner-section-nav__btn banner-section-nav__btn_next js-banners-slider-nav-btn-next"></button>
                </div>
            </div>

            <aside class="left-bar">
                <nav class="site-menu">
                    <li class="site-menu__item">
                        <a href="" class="site-menu__link">
                            <span class="site-menu__icon"><i class="i-nav i-nav--furniture"></i></span>
                            <span class="site-menu__text">Мебель</span>
                        </a>
                    </li>
                    <li class="site-menu__item">
                        <a href="" class="site-menu__link">
                            <span class="site-menu__icon"><i class="i-nav i-nav--household"></i></span>
                            <span class="site-menu__text">Мебель</span>
                        </a>
                    </li>
                    <li class="site-menu__item">
                        <a href="" class="site-menu__link">
                            <span class="site-menu__icon"><i class="i-nav i-nav--appliances"></i></span>
                            <span class="site-menu__text">Мебель</span>
                        </a>
                    </li>
                    <li class="site-menu__item">
                        <a href="" class="site-menu__link">
                            <span class="site-menu__icon"><i class="i-nav i-nav--appliances2"></i></span>
                            <span class="site-menu__text">Мебель</span>
                        </a>
                    </li>
                    <li class="site-menu__item">
                        <a href="" class="site-menu__link">
                            <span class="site-menu__icon"><i class="i-nav i-nav--car"></i></span>
                            <span class="site-menu__text">Мебель</span>
                        </a>
                    </li>
                    <li class="site-menu__item">
                        <a href="" class="site-menu__link">
                            <span class="site-menu__icon"><i class="i-nav i-nav--diy"></i></span>
                            <span class="site-menu__text">Мебель</span>
                        </a>
                    </li>
                    <li class="site-menu__item">
                        <a href="" class="site-menu__link">
                            <span class="site-menu__icon"><i class="i-nav i-nav--garden"></i></span>
                            <span class="site-menu__text">Мебель</span>
                        </a>
                    </li>
                    <li class="site-menu__item">
                        <a href="" class="site-menu__link">
                            <span class="site-menu__icon"><i class="i-nav i-nav--gifts"></i></span>
                            <span class="site-menu__text">Мебель</span>
                        </a>
                    </li>
                    <li class="site-menu__item">
                        <a href="" class="site-menu__link">
                            <span class="site-menu__icon"><i class="i-nav i-nav--jewelry"></i></span>
                            <span class="site-menu__text">Мебель</span>
                        </a>
                    </li>
                    <li class="site-menu__item">
                        <a href="" class="site-menu__link">
                            <span class="site-menu__icon"><i class="i-nav i-nav--kids"></i></span>
                            <span class="site-menu__text">Мебель</span>
                        </a>
                    </li>
                    <li class="site-menu__item">
                        <a href="" class="site-menu__link">
                            <span class="site-menu__icon"><i class="i-nav i-nav--perfumery"></i></span>
                            <span class="site-menu__text">Мебель</span>
                        </a>
                    </li>
                    <li class="site-menu__item">
                        <a href="" class="site-menu__link">
                            <span class="site-menu__icon"><i class="i-nav i-nav--pets"></i></span>
                            <span class="site-menu__text">Мебель</span>
                        </a>
                    </li>
                    <li class="site-menu__item">
                        <a href="" class="site-menu__link">
                            <span class="site-menu__icon"><i class="i-nav i-nav--present"></i></span>
                            <span class="site-menu__text">Мебель</span>
                        </a>
                    </li>
                    <li class="site-menu__item">
                        <a href="" class="site-menu__link">
                            <span class="site-menu__icon"><i class="i-nav i-nav--sport"></i></span>
                            <span class="site-menu__text">Мебель</span>
                        </a>
                    </li>
                </nav>
            </aside>

            <main class="content">
                <div class="content__inner">
                    <ul class="shop-adv grid-4col">
                        <li class="shop-adv__item grid-4col__item">
                            <a href="" class="shop-adv__link">
                                <span class="shop-adv__title underline">Доставка</span>
                                <span class="shop-adv__text">Доставляем по всей России</span>
                            </a>
                        </li>

                        <li class="shop-adv__item grid-4col__item">
                            <a href="" class="shop-adv__link shop-adv__link_self-delivery">
                                <span class="shop-adv__title underline">Самовывоз</span>
                                <span class="shop-adv__text">Более 1300 точек выдачи</span>
                            </a>
                        </li>

                        <li class="shop-adv__item grid-4col__item">
                            <a href="" class="shop-adv__link shop-adv__link_payment">
                                <span class="shop-adv__title underline">Удобно платить</span>
                                <span class="shop-adv__text">Способ оплаты на твой вкус</span>
                            </a>
                        </li>

                        <li class="shop-adv__item grid-4col__item">
                            <a href="" class="shop-adv__link shop-adv__link_wow">
                                <span class="shop-adv__title underline">Акции</span>
                                <span class="shop-adv__text">Выгодные предложения</span>
                            </a>
                        </li>
                    </ul>

                    <div class="section">
                        <div class="section__title">Мы рекомендуем</div>

                        <script>
                            $(function(){
                                var
                                    $sliders = $('.js-slider-goods');

                                $sliders.each(function() {
                                    var
                                        current       = $(this).data('slick-slider'),
                                        responsToShow = $(this).data('respons-show');

                                    $('.js-slider-goods-' + current).slick({
                                        dots: false,
                                        infinite: false,
                                        prevArrow: '.js-goods-slider-btn-prev-' + current,
                                        nextArrow: '.js-goods-slider-btn-next-' + current
                                    })
                                });
                            })
                        </script>

                        <div class="section__content">
                            <div class="slider-section">
                                <button class="slider-section__btn slider-section__btn_prev js-goods-slider-btn-prev-recommendations"></button>
                                <div class="goods goods_grid grid-4col js-slider-goods js-slider-goods-recommendations" data-slick-slider="recommendations" data-slick='{"slidesToShow": 4, "slidesToScroll": 4}'>
                                    <div class="goods__item grid-4col__item">
                                        <div class="sticker"></div>

                                        <a href="" class="goods__img">
                                            <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="">
                                        </a>

                                        <a class="goods__name" href="">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</a>

                                        <div class="goods__price-old"><span class="line-through">45 990</span> ₽</div>
                                        <div class="goods__price-now">45 990 ₽</div>

                                        <a class="goods__btn btn-buy" href="">Купить</a>
                                    </div>

                                    <div class="goods__item grid-4col__item">
                                        <div class="sticker"></div>

                                        <a href="" class="goods__img">
                                            <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="">
                                        </a>

                                        <a class="goods__name" href="">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</a>

                                        <div class="goods__price-old"><span class="line-through">45 990</span> ₽</div>
                                        <div class="goods__price-now">45 990 ₽</div>

                                        <a class="goods__btn btn-buy" href="">Купить</a>
                                    </div>

                                    <div class="goods__item grid-4col__item">
                                        <div class="sticker"></div>

                                        <a href="" class="goods__img">
                                            <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="">
                                        </a>

                                        <a class="goods__name" href="">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</a>

                                        <div class="goods__price-old"><span class="line-through">45 990</span> ₽</div>
                                        <div class="goods__price-now">45 990 ₽</div>

                                        <a class="goods__btn btn-buy" href="">Купить</a>
                                    </div>

                                    <div class="goods__item grid-4col__item">
                                        <div class="sticker"></div>

                                        <a href="" class="goods__img">
                                            <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="">
                                        </a>

                                        <a class="goods__name" href="">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</a>

                                        <div class="goods__price-old"><span class="line-through">45 990</span> ₽</div>
                                        <div class="goods__price-now">45 990 ₽</div>

                                        <a class="goods__btn btn-buy" href="">Купить</a>
                                    </div>

                                    <div class="goods__item grid-4col__item">
                                        <div class="sticker"></div>

                                        <a href="" class="goods__img">
                                            <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="">
                                        </a>

                                        <a class="goods__name" href="">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</a>

                                        <div class="goods__price-old"><span class="line-through">45 990</span> ₽</div>
                                        <div class="goods__price-now">45 990 ₽</div>

                                        <a class="goods__btn btn-buy" href="">Купить</a>
                                    </div>

                                    <div class="goods__item grid-4col__item">
                                        <div class="sticker"></div>

                                        <a href="" class="goods__img">
                                            <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="">
                                        </a>

                                        <a class="goods__name" href="">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</a>

                                        <div class="goods__price-old"><span class="line-through">45 990</span> ₽</div>
                                        <div class="goods__price-now">45 990 ₽</div>

                                        <a class="goods__btn btn-buy" href="">Купить</a>
                                    </div>
                                </div>
                                <button class="slider-section__btn slider-section__btn_next js-goods-slider-btn-next-recommendations"></button>
                            </div>
                        </div>
                    </div>

                    <div class="section">
                        <div class="section__title">Популярные товары</div>

                        <div class="section__content">
                            <div class="slider-section">
                                <button class="slider-section__btn slider-section__btn_prev js-goods-slider-btn-prev-hits"></button>
                                <div class="goods goods_grid grid-4col js-slider-goods js-slider-goods-hits" data-slick-slider="hits" data-slick='{"slidesToShow": 4, "slidesToScroll": 4}'>
                                    <div class="goods__item grid-4col__item">
                                        <div class="sticker"></div>

                                        <a href="" class="goods__img">
                                            <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="">
                                        </a>

                                        <a class="goods__name" href="">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</a>

                                        <div class="goods__price-old"><span class="line-through">45 990</span> ₽</div>
                                        <div class="goods__price-now">45 990 ₽</div>

                                        <a class="goods__btn btn-buy" href="">Купить</a>
                                    </div>

                                    <div class="goods__item grid-4col__item">
                                        <div class="sticker"></div>

                                        <a href="" class="goods__img">
                                            <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="">
                                        </a>

                                        <a class="goods__name" href="">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</a>

                                        <div class="goods__price-old"><span class="line-through">45 990</span> ₽</div>
                                        <div class="goods__price-now">45 990 ₽</div>

                                        <a class="goods__btn btn-buy" href="">Купить</a>
                                    </div>

                                    <div class="goods__item grid-4col__item">
                                        <div class="sticker"></div>

                                        <a href="" class="goods__img">
                                            <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="">
                                        </a>

                                        <a class="goods__name" href="">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</a>

                                        <div class="goods__price-old"><span class="line-through">45 990</span> ₽</div>
                                        <div class="goods__price-now">45 990 ₽</div>

                                        <a class="goods__btn btn-buy" href="">Купить</a>
                                    </div>

                                    <div class="goods__item grid-4col__item">
                                        <div class="sticker"></div>

                                        <a href="" class="goods__img">
                                            <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="">
                                        </a>

                                        <a class="goods__name" href="">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</a>

                                        <div class="goods__price-old"><span class="line-through">45 990</span> ₽</div>
                                        <div class="goods__price-now">45 990 ₽</div>

                                        <a class="goods__btn btn-buy" href="">Купить</a>
                                    </div>

                                    <div class="goods__item grid-4col__item">
                                        <div class="sticker"></div>

                                        <a href="" class="goods__img">
                                            <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="">
                                        </a>

                                        <a class="goods__name" href="">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</a>

                                        <div class="goods__price-old"><span class="line-through">45 990</span> ₽</div>
                                        <div class="goods__price-now">45 990 ₽</div>

                                        <a class="goods__btn btn-buy" href="">Купить</a>
                                    </div>

                                    <div class="goods__item grid-4col__item">
                                        <div class="sticker"></div>

                                        <a href="" class="goods__img">
                                            <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="">
                                        </a>

                                        <a class="goods__name" href="">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</a>

                                        <div class="goods__price-old"><span class="line-through">45 990</span> ₽</div>
                                        <div class="goods__price-now">45 990 ₽</div>

                                        <a class="goods__btn btn-buy" href="">Купить</a>
                                    </div>
                                </div>
                                <button class="slider-section__btn slider-section__btn_next js-goods-slider-btn-next-hits"></button>
                            </div>
                        </div>
                    </div>

                    <div class="section">
                        <div class="section__title">Ещё у нас покупают</div>

                        <div class="section__content">
                            <div class="slider-section">
                                <button class="slider-section__btn slider-section__btn_prev js-goods-slider-btn-prev-buy-more"></button>
                                <div class="goods goods_list grid-4col js-slider-goods js-slider-goods-buy-more" data-slick-slider="buy-more" data-slick='{"slidesToShow": 4, "slidesToScroll": 4}'>
                                    <div class="goods__item grid-4col__item">
                                        <a href="" class="goods__img">
                                            <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="">
                                        </a>

                                        <a class="goods__name underline" href="">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</a>
                                        <div class="goods__cat-count">123 товара</div>
                                    </div>

                                    <div class="goods__item grid-4col__item">
                                        <a href="" class="goods__img">
                                            <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="">
                                        </a>

                                        <a class="goods__name underline" href="">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</a>
                                        <div class="goods__cat-count">123 товара</div>
                                    </div>

                                    <div class="goods__item grid-4col__item">
                                        <a href="" class="goods__img">
                                            <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="">
                                        </a>

                                        <a class="goods__name underline" href="">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</a>
                                        <div class="goods__cat-count">123 товара</div>
                                    </div>

                                    <div class="goods__item grid-4col__item">
                                        <a href="" class="goods__img">
                                            <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="">
                                        </a>

                                        <a class="goods__name underline" href="">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</a>
                                        <div class="goods__cat-count">123 товара</div>
                                    </div>

                                    <div class="goods__item grid-4col__item">
                                        <a href="" class="goods__img">
                                            <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="">
                                        </a>

                                        <a class="goods__name underline" href="">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</a>
                                        <div class="goods__cat-count">123 товара</div>
                                    </div>

                                    <div class="goods__item grid-4col__item">
                                        <a href="" class="goods__img">
                                            <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="">
                                        </a>

                                        <a class="goods__name underline" href="">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</a>
                                        <div class="goods__cat-count">123 товара</div>
                                    </div>
                                </div>
                                <button class="slider-section__btn slider-section__btn_next js-goods-slider-btn-next-buy-more"></button>
                            </div>
                        </div>
                    </div>

                    <div class="section">
                        <div class="section__title">Популярные бренды</div>

                        <div class="section__content">
                            <ul class="brand-list grid-10col">
                                <li class="brand-list__item grid-10col__item">
                                    <a class="brand-list___link" title="Bosch" href="/slices/brands-bosch">
                                        <img src="styles/mainpage/img/logo/bosch.jpg" alt="Bosch" class="lstitem_img">
                                    </a>
                                </li>
                                <li class="brand-list__item grid-10col__item">
                                    <a class="brand-list___link" title="LG" href="/slices/brands-lg">
                                        <img src="styles/mainpage/img/logo/lg.jpg" alt="LG" class="lstitem_img">
                                    </a>
                                </li>
                                <li class="brand-list__item grid-10col__item">
                                    <a class="brand-list___link" title="Samsung" href="/slices/brands-samsung">
                                        <img src="styles/mainpage/img/logo/samsung.jpg" alt="Samsung" class="lstitem_img">
                                    </a>
                                </li>
                                <li class="brand-list__item grid-10col__item">
                                    <a class="brand-list___link" title="Philips" href="/slices/brands-philips">
                                        <img src="styles/mainpage/img/logo/philips.jpg" alt="Philips" class="lstitem_img">
                                    </a>
                                </li>
                                <li class="brand-list__item grid-10col__item">
                                    <a class="brand-list___link" title="Electrolux" href="/slices/brands-electrolux">
                                        <img src="styles/mainpage/img/logo/electrolux.jpg" alt="Electrolux" class="lstitem_img">
                                    </a>
                                </li>
                                <li class="brand-list__item grid-10col__item">
                                    <a class="brand-list___link" title="Sony" href="/slices/brands-sony">
                                        <img src="styles/mainpage/img/logo/sony.jpg" alt="Sony" class="lstitem_img">
                                    </a>
                                </li>
                                <li class="brand-list__item grid-10col__item">
                                    <a class="brand-list___link" title="Apple" href="/slices/brands-apple">
                                        <img src="styles/mainpage/img/logo/apple.jpg" alt="Apple" class="lstitem_img">
                                    </a>
                                </li>
                                <li class="brand-list__item grid-10col__item">
                                    <a class="brand-list___link" title="HP" href="/slices/brands-hp">
                                        <img src="styles/mainpage/img/logo/HP.jpg" alt="HP" class="lstitem_img">
                                    </a>
                                </li>
                                <li class="brand-list__item grid-10col__item">
                                    <a class="brand-list___link" title="Lenovo" href="/slices/brands-lenovo">
                                        <img src="styles/mainpage/img/logo/lenovo.jpg" alt="Lenovo" class="lstitem_img">
                                    </a>
                                </li>
                                <li class="brand-list__item grid-10col__item">
                                    <a class="brand-list___link" title="Hasbro" href="/search?q=hasbro">
                                        <img src="styles/mainpage/img/logo/hasHasbrobro.jpg" alt="Hasbro" class="lstitem_img">
                                    </a>
                                </li>
                                <li class="brand-list__item grid-10col__item">
                                    <a class="brand-list___link" title="Sylvanian Families" href="/slices/brands-sylvanian-families">
                                        <img src="styles/mainpage/img/logo/Sylvanian-Families.jpg" alt="Sylvanian Families" class="lstitem_img">
                                    </a>
                                </li>
                                <li class="brand-list__item grid-10col__item">
                                    <a class="brand-list___link" title="LEGO" href="/slices/brands-lego">
                                        <img src="styles/mainpage/img/logo/lego.jpg" alt="LEGO" class="lstitem_img">
                                    </a>
                                </li>
                                <li class="brand-list__item grid-10col__item">
                                    <a class="brand-list___link" title="Анзоли" href="/slices/brands-anzoli">
                                        <img src="styles/mainpage/img/logo/anzoli.jpg" alt="Anzoli" class="lstitem_img">
                                    </a>
                                </li>
                                <li class="brand-list__item grid-10col__item">
                                    <a class="brand-list___link" title="Шатура" href="/slices/brands-shatura">
                                        <img src="styles/mainpage/img/logo/shatura.jpg" alt="Шатура" class="lstitem_img">
                                    </a>
                                </li>
                                <li class="brand-list__item grid-10col__item">
                                    <a class="brand-list___link" title="Vision Fitness" href="/slices/brands-vision">
                                        <img src="styles/mainpage/img/logo/visionfitnes.jpg" alt="Vision Fitness" class="lstitem_img">
                                    </a>
                                </li>
                                <li class="brand-list__item grid-10col__item">
                                    <a class="brand-list___link" title="Makita" href="/slices/brands-makita">
                                        <img src="styles/mainpage/img/logo/Makita.jpg" alt="Makita" class="lstitem_img">
                                    </a>
                                </li>
                                <li class="brand-list__item grid-10col__item">
                                    <a class="brand-list___link" title="Аскона" href="/slices/brands-askona">
                                        <img src="styles/mainpage/img/logo/askona.jpg" alt="Askona" class="lstitem_img">
                                    </a>
                                </li>
                                <li class="brand-list__item grid-10col__item">
                                    <a class="brand-list___link" title="Tefal" href="/slices/brands-tefal">
                                        <img src="styles/mainpage/img/logo/tefal.jpg" alt="Tefal" class="lstitem_img">
                                    </a>
                                </li>
                                <li class="brand-list__item grid-10col__item">
                                    <a class="brand-list___link" title="PANDORA" href="/slices/brands-pandora">
                                        <img src="styles/mainpage/img/logo/pandora.jpg" alt="PANDORA" class="lstitem_img">
                                    </a>
                                </li>
                                <li class="brand-list__item grid-10col__item">
                                    <a class="brand-list___link" title="GUESS" href="/slices/brands-guess">
                                        <img src="styles/mainpage/img/logo/guess.jpg" alt="GUESS" class="lstitem_img">
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="section">
                        <div class="section__title">Вы смотрели</div>

                        <div class="section__content">
                            <div class="slider-section">
                                <button class="slider-section__btn slider-section__btn_prev js-goods-slider-btn-prev-watched"></button>
                                <div class="goods goods_list grid-8col js-slider-goods js-slider-goods-watched" data-slick-slider="watched" data-slick='{"slidesToShow": 8, "slidesToScroll": 8}'>
                                    <div class="goods__item grid-8col__item">
                                        <a href="" class="goods__img">
                                            <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="">
                                        </a>
                                    </div>

                                    <div class="goods__item grid-8col__item">
                                        <a href="" class="goods__img">
                                            <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="">
                                        </a>
                                    </div>

                                    <div class="goods__item grid-8col__item">
                                        <a href="" class="goods__img">
                                            <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="">
                                        </a>
                                    </div>

                                    <div class="goods__item grid-8col__item">
                                        <a href="" class="goods__img">
                                            <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="">
                                        </a>
                                    </div>

                                    <div class="goods__item grid-8col__item">
                                        <a href="" class="goods__img">
                                            <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="">
                                        </a>
                                    </div>

                                    <div class="goods__item grid-8col__item">
                                        <a href="" class="goods__img">
                                            <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="">
                                        </a>
                                    </div>

                                    <div class="goods__item grid-8col__item">
                                        <a href="" class="goods__img">
                                            <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="">
                                        </a>
                                    </div>

                                    <div class="goods__item grid-8col__item">
                                        <a href="" class="goods__img">
                                            <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="">
                                        </a>
                                    </div>

                                    <div class="goods__item grid-8col__item">
                                        <a href="" class="goods__img">
                                            <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="">
                                        </a>
                                    </div>

                                    <div class="goods__item grid-8col__item">
                                        <a href="" class="goods__img">
                                            <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="">
                                        </a>
                                    </div>

                                    <div class="goods__item grid-8col__item">
                                        <a href="" class="goods__img">
                                            <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="">
                                        </a>
                                    </div>

                                    <div class="goods__item grid-8col__item">
                                        <a href="" class="goods__img">
                                            <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="">
                                        </a>
                                    </div>
                                </div>
                                <button class="slider-section__btn slider-section__btn_next js-goods-slider-btn-next-watched"></button>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>

        <hr class="hr-orange">

        <div class="footer">
            <div class="footer__right">
                <ul class="footer-external">
                    <li class="footer-external__item footer-external__item_title">Оставайтесь на связи</li>
                    <li class="footer-external__item"><a href="" class="footer-external__link"><i class="i-soc-net i-soc-net_fb"></i></a></li>
                    <li class="footer-external__item"><a href="" class="footer-external__link"><i class="i-soc-net i-soc-net_od"></i></a></li>
                    <li class="footer-external__item"><a href="" class="footer-external__link"><i class="i-soc-net i-soc-net_tw"></i></a></li>
                    <li class="footer-external__item"><a href="" class="footer-external__link"><i class="i-soc-net i-soc-net_vk"></i></a></li>
                    <li class="footer-external__item"><a href="" class="footer-external__link"><i class="i-soc-net i-soc-net_yt"></i></a></li>
                </ul>

                <ul class="footer-external">
                    <li class="footer-external__item footer-external__item_title">Мобильные приложения</li>

                    <li class="footer-external__item">
                        <a href="" class="footer-external__link">
                            <span class="app-box app-box_apple">Загрузите<br/> в App Store</span>
                        </a>
                    </li>

                    <li class="footer-external__item">
                        <a href="" class="footer-external__link">
                            <span class="app-box app-box_android">Загрузите<br/> на Google Play</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="footer__left">
                <ul class="footer-list grid-4col">
                    <li class="footer-list__item grid-4col__item"><a href="" class="footer-list__link underline">О компании</a></li>
                    <li class="footer-list__item grid-4col__item"><a href="" class="footer-list__link underline">Работа у нас</a></li>
                    <li class="footer-list__item grid-4col__item"><a href="" class="footer-list__link underline">Правовая информация</a></li>
                    <li class="footer-list__item grid-4col__item"><a href="" class="footer-list__link underline">Уцененные товары оптом</a></li>
                    <li class="footer-list__item grid-4col__item"><a href="" class="footer-list__link underline">Способы оплаты</a></li>
                    <li class="footer-list__item grid-4col__item"><a href="" class="footer-list__link underline">Обратная связь</a></li>
                    <li class="footer-list__item grid-4col__item"><a href="" class="footer-list__link underline">Условия продажи</a></li>
                    <li class="footer-list__item grid-4col__item"><a href="" class="footer-list__link underline">Рекламные возможности</a></li>
                    <li class="footer-list__item grid-4col__item"><a href="" class="footer-list__link underline">Покупка в кредит</a></li>
                    <li class="footer-list__item grid-4col__item"><a href="" class="footer-list__link underline">ЦСИ</a></li>
                    <li class="footer-list__item grid-4col__item"><a href="" class="footer-list__link underline">Информация о СМИ</a></li>
                    <li class="footer-list__item grid-4col__item"><a href="" class="footer-list__link underline">Партнерам</a></li>
                </ul>

                <form action="" class="subscribe-form">
                    <div class="subscribe-form__title">Подписаться на рассылку и получить 300₽ на следующую покупку</div>
                    <input type="text" class="subscribe-form__it it" placeholder="Ваш email">
                    <button class="subscribe-form__btn btn-normal">Подписаться</button>
                </form>

                <div class="footer-hint">Указанная стоимость товаров и условия их приобретения действительны по состоянию на текущую дату.</div>

                <ul class="footer-external footer-external_fl-r">
                    <li class="footer-external__item"><a href="" class="footer-external__link"><i class="i-partner i-partner_mnogoru"></i></a></li>
                    <li class="footer-external__item"><a href="" class="footer-external__link"><i class="i-partner i-partner_sb"></i></a></li>
                </ul>
            </div>
        </div>

        <footer class="copy">
            <div class="inner">
                <div class="copy__left">&copy; ООО «Энтер» 2011–2014. ENTER® ЕНТЕР® Enter®. Все права защищены.</div>
                <div class="copy__right"><a href="">Сообщить об ошибке</a></div>
                <div class="copy__center"><a href="">Мобильная версия сайта</a></div>
            </div>
        </footer>
    </body>
</html>
