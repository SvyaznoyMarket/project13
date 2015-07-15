<?
/**
 * @var $page \View\Main\IndexPage
 */
?>

<!doctype html>
<html class="no-js" lang="">

<?= $page->blockHead() ?>

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

                        <div class="notice-dd notice-dd_compare" style="display: block">
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

    <hr class="hr-orange">

    <!-- для внутренних страниц добавляется класс middle_transform -->
    <div class="middle middle_transform">
        <div class="container">
            <main class="content">
                <div class="content__inner">
                    <!-- баннер -->
                    <div class="banner-section">
                        <img src="http://content.adfox.ru/150713/adfox/176461/1346077.jpg" width="940" height="240" alt="" border="0">
                    </div>
                    <!--/ баннер -->

                    <div class="section">
                        <ul class="bread-crumbs">
                            <li class="bread-crumbs__item"><a href="" class="bread-crumbs__link underline">Мебель</a></li>
                            <li class="bread-crumbs__item"><a href="" class="bread-crumbs__link underline">Мягкая мебель</a></li>
                            <li class="bread-crumbs__item">Кресла</li>
                        </ul>

                        <ul class="categories-grid grid-3col">
                            <li class="categories-grid__item grid-3col__item">
                                <a href="" class="categories-grid__link">
                                    <span class="categories-grid__img">
                                        <img src="http://a.imgenter.ru/uploads/media/ae/d3/e0/thumb_bcc6_product_160.jpeg" alt="" class="image">
                                    </span>

                                    <span class="categories-grid__text">Для климатической техники</span>
                                </a>
                            </li>

                            <li class="categories-grid__item grid-3col__item">
                                <a href="" class="categories-grid__link">
                                    <span class="categories-grid__img">
                                        <img src="http://a.imgenter.ru/uploads/media/ae/d3/e0/thumb_bcc6_product_160.jpeg" alt="" class="image">
                                    </span>

                                    <span class="categories-grid__text">Для пылесосов и пароочистителей</span>
                                </a>
                            </li>

                            <li class="categories-grid__item grid-3col__item">
                                <a href="" class="categories-grid__link">
                                    <span class="categories-grid__img">
                                        <img src="http://a.imgenter.ru/uploads/media/ae/d3/e0/thumb_bcc6_product_160.jpeg" alt="" class="image">
                                    </span>

                                    <span class="categories-grid__text">Для климатической техники</span>
                                </a>
                            </li>

                            <li class="categories-grid__item grid-3col__item">
                                <a href="" class="categories-grid__link">
                                    <span class="categories-grid__img">
                                        <img src="http://a.imgenter.ru/uploads/media/ae/d3/e0/thumb_bcc6_product_160.jpeg" alt="" class="image">
                                    </span>

                                    <span class="categories-grid__text">Для климатической техники</span>
                                </a>
                            </li>

                            <li class="categories-grid__item grid-3col__item">
                                <a href="" class="categories-grid__link">
                                    <span class="categories-grid__img">
                                        <img src="http://a.imgenter.ru/uploads/media/ae/d3/e0/thumb_bcc6_product_160.jpeg" alt="" class="image">
                                    </span>

                                    <span class="categories-grid__text">Батарейки, аккумуляторы и зарядные устройства</span>
                                </a>
                            </li>

                            <li class="categories-grid__item grid-3col__item">
                                <a href="" class="categories-grid__link">
                                    <span class="categories-grid__img">
                                        <img src="http://a.imgenter.ru/uploads/media/ae/d3/e0/thumb_bcc6_product_160.jpeg" alt="" class="image">
                                    </span>

                                    <span class="categories-grid__text">Для мультиварок</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <hr class="hr-orange">

                    <div class="fltrBtn fltrBtn-bt">
                        <form id="productCatalog-filter-form" class="js-category-filter" action="/catalog/electronics/telefoni-897" data-count-url="" method="GET">
                            <div class="fltrBtn_kit">
                                <div class="fltrBtn_tggl fltrBtn_kit_l opn">
                                    <span class="fltrBtn_tggl_tx">Бренд</span>
                                </div>

                                <div class="fltrBtn_kit_r ">
                                    <div class="fltrBtn_i bFilterValuesCol-gbox">
                                        <input class="custom-input customInput-btn jsCustomRadio js-customInput js-category-filter-brand js-category-v2-filter-brand" type="checkbox" id="id-productCategory-filter-brand-option-566" name="f-brand-panasonic" value="566" data-name="Panasonic">
                                        <label class="fltrBtn_btn" for="id-productCategory-filter-brand-option-566">
                                            <img class="fltrBtn_btn_img" src="http://8.imgenter.ru/uploads/media/ae/63/2d/393d44343d06f3ab2cd8564ca76d598c067e0a8f.png">
                                            <i class="fltrBtn_btn_clsr btn-closer1"></i>
                                        </label>
                                    </div>

                                    <div class="fltrBtn_i bFilterValuesCol-gbox">
                                        <input class="custom-input customInput-btn jsCustomRadio js-customInput js-category-filter-brand js-category-v2-filter-brand" type="checkbox" id="id-productCategory-filter-brand-option-566" name="f-brand-panasonic" value="566" data-name="Panasonic">
                                        <label class="fltrBtn_btn" for="id-productCategory-filter-brand-option-566">
                                            <img class="fltrBtn_btn_img" src="http://a.imgenter.ru/uploads/media/b4/fc/a6/63bd2f7a1be1eae1c0e67343ccc063dc6572efb1.png">
                                            <i class="fltrBtn_btn_clsr btn-closer1"></i>
                                        </label>
                                    </div>

                                    <a href="#" class="fltrBtn_btn fltrBtn_btn-mini fltrBtn_btn-btn js-category-v2-filter-otherBrandsOpener"><span class="fltrBtn_btn_tx">Ещё 26</span></a>

                                    <span class="js-category-v2-filter-otherBrands" style="display: inline;">
                                        <div class="fltrBtn_i ">
                                            <input class="custom-input customInput-btn jsCustomRadio js-customInput js-category-filter-brand js-category-v2-filter-brand" type="checkbox" id="id-productCategory-filter-brand-option-4298" name="f-brand-jinga" value="4298" data-name="Jinga">
                                            <label class="fltrBtn_btn" for="id-productCategory-filter-brand-option-4298">
                                                <span class="fltrBtn_btn_tx">Jinga</span>
                                            </label>
                                        </div>
                                    </span>
                                </div>
                            </div>

                            <div class="fltrBtn_kit fltrBtn_kit-box ">
                                <div class="fltrBtnBox fl-l js-category-v2-filter-dropBox js-category-v2-filter-dropBox-price">
                                    <div class="fltrBtnBox_tggl js-category-v2-filter-dropBox-opener">
                                        <span class="fltrBtnBox_tggl_tx">Цена</span>
                                        <i class="fltrBtnBox_tggl_corner"></i>
                                    </div>

                                    <div class="fltrBtnBox_dd fltrBtnBox_dd-l">
                                        <ul class="fltrBtnBox_dd_inn lstdotted js-category-v2-filter-dropBox-content">
                                            <li class="lstdotted_i">
                                                <a class="lstdotted_lk js-category-v2-filter-price-link" href="/catalog/electronics/telefoni-897?f-price-to=12800">
                                                    <span class="txmark1">до</span> 12&thinsp;800
                                                </a>
                                            </li>

                                            <li class="lstdotted_i">
                                                <a class="lstdotted_lk js-category-v2-filter-price-link" href="/catalog/electronics/telefoni-897?f-price-from=12801&amp;f-price-to=25200">
                                                    <span class="txmark1">от</span> 12&thinsp;801
                                                    <span class="txmark1">до</span> 25&thinsp;200
                                                </a>
                                            </li>

                                            <li class="lstdotted_i">
                                                <a class="lstdotted_lk js-category-v2-filter-price-link" href="/catalog/electronics/telefoni-897?f-price-from=50001">
                                                    <span class="txmark1">от</span> 50&thinsp;001
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="fltrBtn_kit fltrBtn_kit-box js-category-v2-filter-otherGroups">
                                <div class="fltrBtnBox  js-category-v2-filter-dropBox">
                                    <div class="fltrBtnBox_tggl js-category-v2-filter-dropBox-opener">
                                        <span class="fltrBtnBox_tggl_tx">В магазине</span>
                                        <i class="fltrBtnBox_tggl_corner"></i>
                                    </div>

                                    <div class="fltrBtnBox_dd js-category-v2-filter-dropBox-content">
                                        <div class="fltrBtnBox_dd_inn">
                                            <div class="fltrBtn_param">
                                                <div class="fltrBtn_ln ">
                                                    <input class="customInput customInput-defradio2 js-category-v2-filter-element-list-radio jsCustomRadio js-customInput  js-category-v2-filter-element-shop-input" type="radio" id="id-productCategory-filter-shop-option-2" name="shop" value="2">
                                                    <label class="customLabel customLabel-defradio2" for="id-productCategory-filter-shop-option-2">

                                                        <span class="customLabel_btx">ул. Орджоникидзе, д. 11, стр. 10</span>
                                                    </label>
                                                </div>

                                                <div class="fltrBtn_ln ">
                                                    <input class="customInput customInput-defradio2 js-category-v2-filter-element-list-radio jsCustomRadio js-customInput  js-category-v2-filter-element-shop-input" type="radio" id="id-productCategory-filter-shop-option-13" name="shop" value="13">
                                                    <label class="customLabel customLabel-defradio2" for="id-productCategory-filter-shop-option-13">

                                                        <span class="customLabel_btx">Волгоградский пр-т, д. 119а.</span>
                                                    </label>
                                                </div>

                                                <div class="fltrBtn_ln ">
                                                    <input class="customInput customInput-defradio2 js-category-v2-filter-element-list-radio jsCustomRadio js-customInput  js-category-v2-filter-element-shop-input" type="radio" id="id-productCategory-filter-shop-option-68" name="shop" value="68">
                                                    <label class="customLabel customLabel-defradio2" for="id-productCategory-filter-shop-option-68">

                                                        <span class="customLabel_btx">Свободный пр-кт, д. 33</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="fltrBtnBox  js-category-v2-filter-dropBox">
                                    <div class="fltrBtnBox_tggl js-category-v2-filter-dropBox-opener">
                                        <span class="fltrBtnBox_tggl_tx">Платформа</span>
                                        <i class="fltrBtnBox_tggl_corner"></i>
                                    </div>

                                    <div class="fltrBtnBox_dd js-category-v2-filter-dropBox-content">
                                        <div class="fltrBtnBox_dd_inn">
                                            <div class="fltrBtn_param">
                                                <div class="fltrBtn_ln ">
                                                    <input class="customInput customInput-defcheck2 js-category-v2-filter-element-list-checkbox jsCustomRadio js-customInput  " type="checkbox" id="id-productCategory-filter-prop3826-option-5337" name="f-prop3826-android" value="5337">
                                                    <label class="customLabel customLabel-defcheck2" for="id-productCategory-filter-prop3826-option-5337">

                                                        <span class="customLabel_btx">Android</span>
                                                    </label>
                                                </div>

                                                <div class="fltrBtn_ln ">
                                                    <input class="customInput customInput-defcheck2 js-category-v2-filter-element-list-checkbox jsCustomRadio js-customInput  " type="checkbox" id="id-productCategory-filter-prop3826-option-29929" name="f-prop3826-android_4_0_ics" value="29929">
                                                    <label class="customLabel customLabel-defcheck2" for="id-productCategory-filter-prop3826-option-29929">

                                                        <span class="customLabel_btx">Android 4.0 ICS</span>
                                                    </label>
                                                </div>

                                                <div class="fltrBtn_ln ">
                                                    <input class="customInput customInput-defcheck2 js-category-v2-filter-element-list-checkbox jsCustomRadio js-customInput  " type="checkbox" id="id-productCategory-filter-prop3826-option-29928" name="f-prop3826-android_4_1_jelly_bean" value="29928">
                                                    <label class="customLabel customLabel-defcheck2" for="id-productCategory-filter-prop3826-option-29928">

                                                        <span class="customLabel_btx">Android 4.1 Jelly Bean</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="fltrBtnBox  js-category-v2-filter-dropBox">
                                    <div class="fltrBtnBox_tggl js-category-v2-filter-dropBox-opener">
                                        <span class="fltrBtnBox_tggl_tx">Дисплей</span>
                                        <i class="fltrBtnBox_tggl_corner"></i>
                                    </div>

                                    <div class="fltrBtnBox_dd js-category-v2-filter-dropBox-content">
                                        <div class="fltrBtnBox_dd_inn">
                                            <div class="fltrBtn_param">
                                                <div class="fltrBtn_param_n">Диагональ экрана</div>

                                                <div class="fltrBtn_ln js-category-v2-filter-element-number">
                                                    <span class="fltrBtn_param_lbl txmark1">от</span> <input class="fltrBtn_param_it js-category-v2-filter-element-number-from" name="" value="" placeholder="1.4" type="text">
                                                    &ensp;<span class="fltrBtn_param_lbl txmark1">до</span> <input class="fltrBtn_param_it js-category-v2-filter-element-number-to" name="" value="" placeholder="6" type="text">
                                                    <span class="fltrBtn_param_lbl txmark1">"</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="fltrBtn_kit fltrBtn_kit-nborder">
                                <div class="js-category-filter-selected clearfix">
                                    <ul class="fltr_slctd">
                                        <li class="fltr_slctd_i fltr_slctd_i-n">В магазине:</li>

                                        <li class="fltr_slctd_i">
                                            <span>ул. Орджоникидзе, д. 11, стр. 10</span>
                                            <a class="btn-closer2 jsHistoryLink" href=""></a>
                                        </li>
                                    </ul>

                                    <ul class="fltr_slctd">
                                        <li class="fltr_slctd_i fltr_slctd_i-n">Память:</li>

                                        <li class="fltr_slctd_i">
                                            Встроенная память

                                            <span>от 5 ГБ</span>
                                            <a class="btn-closer2 jsHistoryLink" href=""></a>
                                        </li>
                                    </ul>

                                    <ul class="fltr_slctd">
                                        <li class="fltr_slctd_i fltr_slctd_i-n">Платформа:</li>

                                        <li class="fltr_slctd_i">
                                            <span>Android 4.1 Jelly Bean</span>
                                            <a class="btn-closer2 jsHistoryLink" href=""></a>
                                        </li>
                                    </ul>

                                    <a class="fltr_clsr jsHistoryLink" href="">
                                        <span class="btn-closer3"></span>
                                        <span class="fltr_clr_tx">Очистить все</span>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>

        <!-- для внутренних страниц добавляется класс left-bar_transform -->
        <aside class="left-bar left-bar_transform">
            <?= $page->slotNavigation() ?>
        </aside>
    </div>
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

        <div class="popup__desc">
            Выберите город, в котором собираетесь получать товары.<br/>
            От выбора зависит стоимость товаров и доставки.
        </div>

        <form class="form form-region search-bar">
            <i class="search-bar__icon i-controls i-controls--search"></i>
            <input type="text" class="form-region__it search-bar__it it" placeholder="Найти свой регион">
            <button class="form-region__btn btn-primary btn-primary_normal">Найти</button>

            <!-- саджест поиска региона -->
            <div class="region-suggest">
                <ul class="region-suggest-list">
                    <li class="region-suggest-list__item"><a href="" class="region-suggest-list__link">МО город Гороховец тер (Гороховецкий) (Владимирская обл)</a></li>
                    <li class="region-suggest-list__item"><a href="" class="region-suggest-list__link">Гороховец г (Гороховецкий) (Владимирская обл)</a></li>
                    <li class="region-suggest-list__item"><a href="" class="region-suggest-list__link">Городище рп (Городищенский) (Волгоградская обл)</a></li>
                </ul>
            </div>
            <!--/ саджест поиска региона -->
        </form>

        <ul class="region-subst">
            <li class="region-subst__item"><a href="" class="region-subst__link dotted">Москва</a></li>
            <li class="region-subst__item"><a href="" class="region-subst__link dotted">Санкт-Петербург</a></li>
            <li class="region-subst__item region-subst__item_toggle"><a href="" class="region-subst__link dotted">Еще города</a></li>
        </ul>

        <!-- Что бы показать слайдер регионов добавляем класс show -->
        <div class="region-slides slider-section">
            <button class="slider-section__btn slider-section__btn_prev js-goods-slider-btn-prev-region-list"></button>

            <div class="js-slider-goods js-slider-goods-region-list" data-slick-slider="region-list" data-slick='{"slidesToShow": 4, "slidesToScroll": 2}'>
                <div class="region-slides__item">
                    <ul class="region-list">
                        <li class="region-list__item"><a href="" class="region-list__link">Абакан</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Архангельск г</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Астрахань</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Абакан</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Архангельск г</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Астрахань</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Абакан</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Архангельск г</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Астрахань</a></li>
                    </ul>
                </div>

                <div class="region-slides__item">
                    <ul class="region-list">
                        <li class="region-list__item"><a href="" class="region-list__link">Абакан</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Архангельск г</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Астрахань</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Абакан</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Архангельск г</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Астрахань</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Абакан</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Архангельск г</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Астрахань</a></li>
                    </ul>
                </div>

                <div class="region-slides__item">
                    <ul class="region-list">
                        <li class="region-list__item"><a href="" class="region-list__link">Абакан</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Архангельск г</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Астрахань</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Городище рп (Городищенский) (Волгоградская обл)</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Архангельск г</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Астрахань</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Абакан</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Архангельск г</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Астрахань</a></li>
                    </ul>
                </div>

                <div class="region-slides__item">
                    <ul class="region-list">
                        <li class="region-list__item"><a href="" class="region-list__link">Абакан</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Архангельск г</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Астрахань</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Абакан</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Архангельск г</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Астрахань</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Абакан</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Архангельск г</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Астрахань</a></li>
                    </ul>
                </div>

                <div class="region-slides__item">
                    <ul class="region-list">
                        <li class="region-list__item"><a href="" class="region-list__link">Абакан</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Архангельск г</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Астрахань</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Абакан</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Архангельск г</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Астрахань</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Абакан</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Архангельск г</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Астрахань</a></li>
                    </ul>
                </div>

                <div class="region-slides__item">
                    <ul class="region-list">
                        <li class="region-list__item"><a href="" class="region-list__link">Абакан</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Архангельск г</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Астрахань</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Абакан</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Архангельск г</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Астрахань</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Абакан</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Архангельск г</a></li>
                        <li class="region-list__item"><a href="" class="region-list__link">Астрахань</a></li>
                    </ul>
                </div>
            </div>

            <button class="slider-section__btn slider-section__btn_next js-goods-slider-btn-next-region-list"></button>
        </div>
    </div>
</div>

<!--/ попап выбора региона -->

<?= $page->slotBodyJavascript() ?>

</body>
</html>
