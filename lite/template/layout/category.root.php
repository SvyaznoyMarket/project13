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
            <a href="/" class="logotype"></a>
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

    <!-- для внутренних страниц добавляется класс left-bar_transform -->
    <aside class="left-bar left-bar_transform">
        <?= $page->slotNavigation() ?>
    </aside>

    <!-- для внутренних страниц добавляется класс content_transform -->
    <main class="content content_transform">
        <div class="content__inner">
            <!-- баннер -->
            <div class="banner-section">
                <img src="http://content.adfox.ru/150713/adfox/176461/1346077.jpg" width="940" height="240" alt="" border="0">
            </div>
            <!--/ баннер -->

            <!-- категории товаров -->
            <div class="section">
                <div class="section__title">Детские товары</div>

                <div class="section__content">
                    <div class="slider-section">
                        <div class="goods goods_categories grid-4col">
                            <div class="goods__item grid-4col__item">
                                <a href="" class="goods__img">
                                    <img src="http://5.imgenter.ru/uploads/media/02/2a/5a/thumb_3c59_category_163x163.jpeg" alt="" class="goods__img-image">
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
                                    <img src="http://6.imgenter.ru/uploads/media/8c/f9/83/thumb_410a_category_163x163.jpeg" alt="" class="goods__img-image">
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
                    </div>
                </div>
            </div>
            <!--/ категории товаров -->

            <!-- вы смотерли - слайдер -->
            <div class="section section_bordered js-module-require" data-module="jquery.slick">
                <div class="section__title">Вы смотрели</div>

                <div class="section__content">
                    <div class="slider-section">
                        <button class="slider-section__btn slider-section__btn_prev js-goods-slider-btn-prev-watched"></button>
                        <div class="goods goods_images goods_list grid-9col js-slider-goods js-slider-goods-watched" data-slick-slider="watched" data-slick='{"slidesToShow": 9, "slidesToScroll": 9}'>
                            <div class="goods__item grid-9col__item">
                                <a href="" class="goods__img">
                                    <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                </a>
                            </div>

                            <div class="goods__item grid-9col__item">
                                <a href="" class="goods__img">
                                    <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                </a>
                            </div>

                            <div class="goods__item grid-9col__item">
                                <a href="" class="goods__img">
                                    <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                </a>
                            </div>

                            <div class="goods__item grid-9col__item">
                                <a href="" class="goods__img">
                                    <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                </a>
                            </div>

                            <div class="goods__item grid-9col__item">
                                <a href="" class="goods__img">
                                    <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                </a>
                            </div>

                            <div class="goods__item grid-9col__item">
                                <a href="" class="goods__img">
                                    <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                </a>
                            </div>

                            <div class="goods__item grid-9col__item">
                                <a href="" class="goods__img">
                                    <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                </a>
                            </div>

                            <div class="goods__item grid-9col__item">
                                <a href="" class="goods__img">
                                    <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                </a>
                            </div>

                            <div class="goods__item grid-9col__item">
                                <a href="" class="goods__img">
                                    <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                </a>
                            </div>

                            <div class="goods__item grid-9col__item">
                                <a href="" class="goods__img">
                                    <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                </a>
                            </div>

                            <div class="goods__item grid-9col__item">
                                <a href="" class="goods__img">
                                    <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                </a>
                            </div>

                            <div class="goods__item grid-9col__item">
                                <a href="" class="goods__img">
                                    <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                </a>
                            </div>
                        </div>
                        <button class="slider-section__btn slider-section__btn_next js-goods-slider-btn-next-watched"></button>
                    </div>
                </div>
            </div>
            <!--/ вы смотерли - слайдер -->

            <!-- SEO информация -->
            <div class="section section_bordered section_seo">
                <p>
                    Мебель играет важную роль в создании любого интерьера. Ведь как порой не просто подобрать ту или иную вещь, чтобы добиться единого и гармоничного пространства. Решение купить мебель в интернет-магазине с доставкой позволяет не только справиться с этой задачей, но и совершить покупку с максимальным комфортом.
                    Интернет-магазин мебели Enter, работающий на всей территории России, готов предоставить вашему <strong>вниманию самый</strong> разнообразный выбор в категории «Мебель».
                    Наши товары – это прямые поставки от российских и иностранных производителей и официальных дистрибьюторов европейских и американских фабрик. Отсутствие посредников позволяет нам предлагать клиентам лучшие цены. Вся импортная продукция ввезена в Россию официально. Особенно выгодным соотношением качества и цены отличается мебель под нашей собственной торговой маркой Anzoli. Весь товар сертифицирован, на него поддерживается гарантийный срок.
                </p>

                <ul>
                    <li>Мягкая мебель – яркие и уютные диваны, кресла, пуфики и декоративные подушки;</li>
                    <li>Гостиная – гарнитуры, TV-тумбы, журнальные столики, шкафы, стеллажи и комоды по самым приятным ценам;</li>
                    <li>Спальня – кровати, гарнитуры, ортопедические основания, матрасы, тумбы, комоды, платяные шкафы, туалетные столики, зеркала и банкетки;</li>
                    <li>Столовая и кухня – уголки и обеденные зоны, стулья, табуреты и барные стулья для обустройства самых разных интерьеров;</li>
                    <li>Шкафы-купе – множество моделей различных цветов и размеров;</li>
                    <li>Хранение вещей – шкафы разных видов, стеллажи, навесные полки, комоды, тумбы – любые системы хранения для вашего дома;</li>
                    <li>Офис и домашний кабинет – компьютерные столы и офисные кресла, стеллажи и навесные полки, мягкая мебель и костюмные вешалки – создавайте свой рабочий уголок по своему вкусу;</li>
                </ul>
            </div>
            <!--/ SEO информация -->
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
