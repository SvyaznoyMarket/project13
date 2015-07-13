<?
/**
 * @var $page \View\Main\IndexPage
 */
?>

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
    <link href='http://fonts.googleapis.com/css?family=Roboto:500,100,300,700,400&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
</head>

<body>
<div class="wrapper">
    <div class="header table">
        <div class="header__side header__logotype table-cell">
            <a href="" class="logotype"></a>
        </div>

        <div class="header__center table-cell">
            <div class="header__line header__line--top">
                <a href="" class="location dotted js-popup-show" data-popup="region">Набережные челны</a>

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

                    <div class="search-suggest">
                        <ul class="search-suggest-list search-suggest-list_categories">
                            <li class="search-suggest-list__title">
                                <span class="search-suggest-list__title-text">Категории</span>
                            </li>

                            <li class="search-suggest-list__item">
                                <a class="search-suggest-list__link" href="">Художественные краски <span class="search-suggest-list__count"> &#8230;&#8230; 45</span></a>
                            </li>

                            <li class="search-suggest-list__item">
                                <a class="search-suggest-list__link" href="">Краски <span class="search-suggest-list__count"> &#8230;&#8230; 45</span></a>
                            </li>

                            <li class="search-suggest-list__item">
                                <a class="search-suggest-list__link" href="">Художественные краски <span class="search-suggest-list__count"> &#8230;&#8230; 45</span></a>
                            </li>
                        </ul>

                        <ul class="search-suggest-list">
                            <li class="search-suggest-list__title">
                                <span class="search-suggest-list__title-text">Товары</span>
                            </li>

                            <li class="search-suggest-list__item">
                                <a class="search-suggest-list__link" href="">
                                    <span class="search-suggest-list__img">
                                        <img class="image" src="http://0.imgenter.ru/uploads/media/0a/09/a8/thumb_0201_product_60.jpeg" alt="">
                                    </span>

                                    <span class="search-suggest-list__text">Турник Torres Deluxe</span>
                                </a>
                            </li>

                            <li class="search-suggest-list__item">
                                <a class="search-suggest-list__link" href="">
                                    <span class="search-suggest-list__img">
                                        <img class="image" src="http://0.imgenter.ru/uploads/media/0a/09/a8/thumb_0201_product_60.jpeg" alt="">
                                    </span>

                                    <span class="search-suggest-list__text">Портативная акустическая система Promate Mulotov красная</span>
                                </a>
                            </li>

                            <li class="search-suggest-list__item">
                                <a class="search-suggest-list__link" href="">
                                    <span class="search-suggest-list__img">
                                        <img class="image" src="http://0.imgenter.ru/uploads/media/0a/09/a8/thumb_0201_product_60.jpeg" alt="">
                                    </span>

                                    <span class="search-suggest-list__text">Чехол для Apple iPhone6 XtremeMac Microshield Accent (IPP-MA6-73), красный</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </form>

                <ul class="user-controls">
                    <li class="user-controls__item">
                        <a href="" class="user-controls__link">
                            <span class="user-controls__icon"><i class="i-controls i-controls--compare"></i></span>
                            <span class="user-controls__text">Сравнение</span>
                        </a>
                    </li>
                    <li class="user-controls__item">
                        <a href="" class="user-controls__link js-popup-show" data-popup="login">
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
        <?= $page->slotNavigation() ?>
    </aside>

    <main class="content">
        <div class="content__inner">
            <div class="banner-section">
                <div class="banner-section-list js-banners-slider">
                    <div class="banner-section-list__item">
                        <a href="" class="banner-section-list__link">
                            <img class="banner-section-list__item" data-lazy="http://fs01.enter.ru/4/1/960x240/67/413291.jpg" src="http://fs01.enter.ru/4/1/960x240/67/413291.jpg" alt="" class="banner-section-list__img">
                        </a>
                    </div>

                    <div class="banner-section-list__item">
                        <a href="" class="banner-section-list__link">
                            <img class="banner-section-list__item" src="" data-lazy="http://fs01.enter.ru/4/1/960x240/a5/411502.jpg" alt="" class="banner-section-list__img">
                        </a>
                    </div>

                    <div class="banner-section-list__item">
                        <a href="" class="banner-section-list__link">
                            <img class="banner-section-list__item" src="" data-lazy="http://fs01.enter.ru/4/1/960x240/48/408940.jpg" alt="" class="banner-section-list__img">
                        </a>
                    </div>

                    <div class="banner-section-list__item">
                        <a href="" class="banner-section-list__link">
                            <img class="banner-section-list__item" src="" data-lazy="http://fs01.enter.ru/4/1/960x240/67/413291.jpg" alt="" class="banner-section-list__img">
                        </a>
                    </div>

                    <div class="banner-section-list__item">
                        <a href="" class="banner-section-list__link">
                            <img class="banner-section-list__item" src="" data-lazy="http://fs01.enter.ru/4/1/960x240/a5/411502.jpg" alt="" class="banner-section-list__img">
                        </a>
                    </div>

                    <div class="banner-section-list__item">
                        <a href="" class="banner-section-list__link">
                            <img class="banner-section-list__item" src="" data-lazy="http://fs01.enter.ru/4/1/960x240/48/408940.jpg" alt="" class="banner-section-list__img">
                        </a>
                    </div>

                    <div class="banner-section-list__item">
                        <a href="" class="banner-section-list__link">
                            <img class="banner-section-list__item" src="" data-lazy="http://fs01.enter.ru/4/1/960x240/67/413291.jpg" alt="" class="banner-section-list__img">
                        </a>
                    </div>
                </div>
            </div>

            <div class="section">
                <ul class="shop-adv">
                    <li class="shop-adv__item">
                        <a href="" class="shop-adv__link">
                            <span class="shop-adv__title underline">Доставляем по всей России</span>
                        </a>
                    </li>

                    <li class="shop-adv__item">
                        <a href="" class="shop-adv__link shop-adv__link_self-delivery">
                            <span class="shop-adv__title underline">Более 1300 точек выдачи</span>
                        </a>
                    </li>

                    <li class="shop-adv__item">
                        <a href="" class="shop-adv__link shop-adv__link_payment">
                            <span class="shop-adv__title underline">Удобно платить</span>
                        </a>
                    </li>

                    <li class="shop-adv__item">
                        <a href="" class="shop-adv__link shop-adv__link_wow">
                            <span class="shop-adv__title underline">Акции</span>
                        </a>
                    </li>
                </ul>
            </div>

            <?= $page->blockRecommendations() ?>

            <div class="section js-module-require" data-module="jquery.slick">
                <div class="section__title">Ещё у нас покупают</div>

                <div class="section__content">
                    <div class="slider-section">
                        <button class="slider-section__btn slider-section__btn_prev js-goods-slider-btn-prev-buy-more"></button>
                        <div class="goods goods_list grid-4col js-slider-goods js-slider-goods-buy-more" data-slick-slider="buy-more" data-slick='{"slidesToShow": 4, "slidesToScroll": 4}'>
                            <div class="goods__item grid-4col__item">
                                <a href="" class="goods__img">
                                    <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                </a>

                                <div class="goods__name">
                                    <a class="underline" href="">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</a>
                                </div>

                                <div class="goods__cat-count">123 товара</div>
                            </div>

                            <div class="goods__item grid-4col__item">
                                <a href="" class="goods__img">
                                    <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                </a>

                                <div class="goods__name">
                                    <a class="underline" href="">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</a>
                                </div>

                                <div class="goods__cat-count">123 товара</div>
                            </div>

                            <div class="goods__item grid-4col__item">
                                <a href="" class="goods__img">
                                    <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                </a>

                                <div class="goods__name">
                                    <a class="underline" href="">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</a>
                                </div>

                                <div class="goods__cat-count">123 товара</div>
                            </div>

                            <div class="goods__item grid-4col__item">
                                <a href="" class="goods__img">
                                    <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                </a>

                                <div class="goods__name">
                                    <a class="underline" href="">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</a>
                                </div>

                                <div class="goods__cat-count">123 товара</div>
                            </div>

                            <div class="goods__item grid-4col__item">
                                <a href="" class="goods__img">
                                    <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                </a>

                                <div class="goods__name">
                                    <a class="underline" href="">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</a>
                                </div>

                                <div class="goods__cat-count">123 товара</div>
                            </div>

                            <div class="goods__item grid-4col__item">
                                <a href="" class="goods__img">
                                    <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                </a>

                                <div class="goods__name">
                                    <a class="underline" href="">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</a>
                                </div>

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
                    <ul class="brand-list grid-9col">
                        <li class="brand-list__item grid-9col__item">
                            <a class="brand-list___link" title="Bosch" href="/slices/brands-bosch">
                                <img src="styles/mainpage/img/logo/bosch.jpg" alt="Bosch" class="lstitem_img">
                            </a>
                        </li>
                        <li class="brand-list__item grid-9col__item">
                            <a class="brand-list___link" title="LG" href="/slices/brands-lg">
                                <img src="styles/mainpage/img/logo/lg.jpg" alt="LG" class="lstitem_img">
                            </a>
                        </li>
                        <li class="brand-list__item grid-9col__item">
                            <a class="brand-list___link" title="Samsung" href="/slices/brands-samsung">
                                <img src="styles/mainpage/img/logo/samsung.jpg" alt="Samsung" class="lstitem_img">
                            </a>
                        </li>
                        <li class="brand-list__item grid-9col__item">
                            <a class="brand-list___link" title="Philips" href="/slices/brands-philips">
                                <img src="styles/mainpage/img/logo/philips.jpg" alt="Philips" class="lstitem_img">
                            </a>
                        </li>
                        <li class="brand-list__item grid-9col__item">
                            <a class="brand-list___link" title="Electrolux" href="/slices/brands-electrolux">
                                <img src="styles/mainpage/img/logo/electrolux.jpg" alt="Electrolux" class="lstitem_img">
                            </a>
                        </li>
                        <li class="brand-list__item grid-9col__item">
                            <a class="brand-list___link" title="Sony" href="/slices/brands-sony">
                                <img src="styles/mainpage/img/logo/sony.jpg" alt="Sony" class="lstitem_img">
                            </a>
                        </li>
                        <li class="brand-list__item grid-9col__item">
                            <a class="brand-list___link" title="Apple" href="/slices/brands-apple">
                                <img src="styles/mainpage/img/logo/apple.jpg" alt="Apple" class="lstitem_img">
                            </a>
                        </li>
                        <li class="brand-list__item grid-9col__item">
                            <a class="brand-list___link" title="HP" href="/slices/brands-hp">
                                <img src="styles/mainpage/img/logo/HP.jpg" alt="HP" class="lstitem_img">
                            </a>
                        </li>
                        <li class="brand-list__item grid-9col__item">
                            <a class="brand-list___link" title="Lenovo" href="/slices/brands-lenovo">
                                <img src="styles/mainpage/img/logo/lenovo.jpg" alt="Lenovo" class="lstitem_img">
                            </a>
                        </li>
                        <li class="brand-list__item grid-9col__item">
                            <a class="brand-list___link" title="Hasbro" href="/search?q=hasbro">
                                <img src="styles/mainpage/img/logo/hasHasbrobro.jpg" alt="Hasbro" class="lstitem_img">
                            </a>
                        </li>
                        <li class="brand-list__item grid-9col__item">
                            <a class="brand-list___link" title="Sylvanian Families" href="/slices/brands-sylvanian-families">
                                <img src="styles/mainpage/img/logo/Sylvanian-Families.jpg" alt="Sylvanian Families" class="lstitem_img">
                            </a>
                        </li>
                        <li class="brand-list__item grid-9col__item">
                            <a class="brand-list___link" title="LEGO" href="/slices/brands-lego">
                                <img src="styles/mainpage/img/logo/lego.jpg" alt="LEGO" class="lstitem_img">
                            </a>
                        </li>
                        <li class="brand-list__item grid-9col__item">
                            <a class="brand-list___link" title="Анзоли" href="/slices/brands-anzoli">
                                <img src="styles/mainpage/img/logo/anzoli.jpg" alt="Anzoli" class="lstitem_img">
                            </a>
                        </li>
                        <li class="brand-list__item grid-9col__item">
                            <a class="brand-list___link" title="Шатура" href="/slices/brands-shatura">
                                <img src="styles/mainpage/img/logo/shatura.jpg" alt="Шатура" class="lstitem_img">
                            </a>
                        </li>
                        <li class="brand-list__item grid-9col__item">
                            <a class="brand-list___link" title="Vision Fitness" href="/slices/brands-vision">
                                <img src="styles/mainpage/img/logo/visionfitnes.jpg" alt="Vision Fitness" class="lstitem_img">
                            </a>
                        </li>
                        <li class="brand-list__item grid-9col__item">
                            <a class="brand-list___link" title="Makita" href="/slices/brands-makita">
                                <img src="styles/mainpage/img/logo/Makita.jpg" alt="Makita" class="lstitem_img">
                            </a>
                        </li>
                        <li class="brand-list__item grid-9col__item">
                            <a class="brand-list___link" title="Аскона" href="/slices/brands-askona">
                                <img src="styles/mainpage/img/logo/askona.jpg" alt="Askona" class="lstitem_img">
                            </a>
                        </li>
                        <li class="brand-list__item grid-9col__item">
                            <a class="brand-list___link" title="Tefal" href="/slices/brands-tefal">
                                <img src="styles/mainpage/img/logo/tefal.jpg" alt="Tefal" class="lstitem_img">
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
                        <div class="goods goods_images goods_list grid-8col js-slider-goods js-slider-goods-watched" data-slick-slider="watched" data-slick='{"slidesToShow": 8, "slidesToScroll": 8}'>
                            <div class="goods__item grid-8col__item">
                                <a href="" class="goods__img">
                                    <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                </a>
                            </div>

                            <div class="goods__item grid-8col__item">
                                <a href="" class="goods__img">
                                    <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                </a>
                            </div>

                            <div class="goods__item grid-8col__item">
                                <a href="" class="goods__img">
                                    <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                </a>
                            </div>

                            <div class="goods__item grid-8col__item">
                                <a href="" class="goods__img">
                                    <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                </a>
                            </div>

                            <div class="goods__item grid-8col__item">
                                <a href="" class="goods__img">
                                    <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                </a>
                            </div>

                            <div class="goods__item grid-8col__item">
                                <a href="" class="goods__img">
                                    <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                </a>
                            </div>

                            <div class="goods__item grid-8col__item">
                                <a href="" class="goods__img">
                                    <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                </a>
                            </div>

                            <div class="goods__item grid-8col__item">
                                <a href="" class="goods__img">
                                    <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                </a>
                            </div>

                            <div class="goods__item grid-8col__item">
                                <a href="" class="goods__img">
                                    <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                </a>
                            </div>

                            <div class="goods__item grid-8col__item">
                                <a href="" class="goods__img">
                                    <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                </a>
                            </div>

                            <div class="goods__item grid-8col__item">
                                <a href="" class="goods__img">
                                    <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                </a>
                            </div>

                            <div class="goods__item grid-8col__item">
                                <a href="" class="goods__img">
                                    <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
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

<!-- попап авторизации/регистрации -->
<div class="popup popup_log js-popup-login">
    <div class="popup__close js-popup-close">&#215;</div>

    <div class="popup__content">
        <!--
            по умолчанию login_auth - окно "Войти"
            если нажали "Регистрация" то login_auth - меняем на login_reg
            если нажали на "забыли?(пароль)" - меняем класс на login_hint
            если действие совершено успешно, то !добавляем! класс login_success
        -->
        <div class="login login_auth">
            <!-- авторизация -->
            <form class="form form_auth" action="" method="">
                <div class="popup__title">Вход в Enter</div>

                <div class="form__field">
                    <!--
                        если поле заполнено символами, то добавлем класс valid
                        если ошибка - error
                    -->
                    <input type="text" class="form__it it error" value="">
                    <label class="form__placeholder placeholder">Email или телефон</label>
                </div>

                <div class="form__field">
                    <input type="text" class="form__it it" value="">
                    <label class="form__placeholder placeholder">Пароль</label>

                    <a href="" class="form__it-btn">забыли?</a>
                </div>

                <div class="form__field">
                    <button class="form__btn-log btn-primary btn-primary_bigger" type="submit">Войти</button>
                </div>

                <div class="form__title">Войти через</div>

                <ul class="login-external">
                    <li class="login-external__item"><a href="" class="login-external__link login-external__link_fb"></a></li>
                    <li class="login-external__item"><a href="" class="login-external__link login-external__link_vk"></a></li>
                    <li class="login-external__item"><a href="" class="login-external__link login-external__link_od"></a></li>
                </ul>
            </form>
            <!--/ авторизация -->

            <!-- регистрация -->
            <form class="form form_reg" action="" method="">
                <div class="popup__title">Регистрация</div>

                <fieldset class="form__content">
                    <div class="form__it-name">Как к вам обращаться?</div>

                    <div class="form__field">
                        <input type="text" class="form__it it" value="">
                        <label class="form__placeholder placeholder">Имя</label>
                    </div>

                    <div class="form__field">
                        <input type="text" class="form__it it" value="">
                        <label class="form__placeholder placeholder">Email</label>
                    </div>

                    <div class="form__field">
                        <input type="text" class="form__it it" value="">
                        <label class="form__placeholder placeholder">Телефон</label>
                    </div>

                    <div class="login-subscribe">
                        <input class="custom-input custom-input_check" type="checkbox" name="subscribe" id="subscribe" checked="checked">
                        <label class="custom-label" for="subscribe">Подписаться на email-рассылку,<br> получить скидку 300 рублей</label>
                    </div>

                    <div class="form__field">
                        <button class="form__btn-log btn-primary btn-primary_bigger" type="submit">Регистрация</button>
                    </div>

                    <div class="login-hint">
                        Нажимая кнопку «Регистрация», я подтверждаю свое согласие с <a href="" class="underline">Условиями продажи</a>.
                    </div>

                    <div class="form__title">Войти через</div>

                    <ul class="login-external">
                        <li class="login-external__item"><a href="" class="login-external__link login-external__link_fb"></a></li>
                        <li class="login-external__item"><a href="" class="login-external__link login-external__link_vk"></a></li>
                        <li class="login-external__item"><a href="" class="login-external__link login-external__link_od"></a></li>
                    </ul>
                </fieldset>

                <!-- сообщение об успешной регистрации -->
                <div class="form__success-text">Пароль отправлен на email<br>и на мобильный телефон.</div>
                <!--/ сообщение об успешной регистрации -->
            </form>
            <!--/ регистрация -->

            <!-- восстановление пароля -->
            <form class="form form_hint" action="" method="">
                <div class="popup__title">Восстановление пароля</div>

                <fieldset class="form__content">
                    <div class="form__field">
                        <input type="text" class="form__it it" value="">
                        <label class="form__placeholder placeholder">Email или телефон</label>
                    </div>

                    <div class="form__field">
                        <button class="form__btn-log btn-primary btn-primary_bigger" type="submit">Отправить</button>
                    </div>
                </fieldset>

                <!-- сообщение об успешной отправке пароля -->
                <div class="form__success-text">Новый пароль отправлен<br>на мобильный телефон.</div>
                <!--/ сообщение об успешной отправке пароля -->
            </form>
            <!-- восстановление пароля -->
        </div>

        <div class="login-switch"><a href="" class="dotted">Регистрация</a></div>
    </div>
</div>
<!--/ попап авторизации/регистрации -->

<!-- попап выбора региона -->
    <div class="popup popup_region js-popup-region">
        <div class="popup__close js-popup-close">&#215;</div>

        <div class="popup__content">
            <div class="popup__title">Ваш город</div>
        </div>
    </div>

<!--/ попап выбора региона -->

<?= $page->slotBodyJavascript() ?>

</body>
</html>
