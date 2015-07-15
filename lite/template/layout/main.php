<?
/**
 * @var $page \View\Main\IndexPage
 * @var $bannerData []
 */
?>

<!doctype html>
<html class="no-js" lang="">

<?= $page->blockHead() ?>

<body>
<div class="wrapper">

    <?= $page->blockHeader() ?>

    <hr class="hr-orange">

    <div class="middle">
        <div class="container">
            <main class="content">
                <div class="content__inner">
                    <div class="banner-section">
                        <div class="banner-section-list js-banners-slider">

                        <? foreach ($page->getParam('bannerData', []) as $i => $banner) : ?>

                            <div class="banner-section-list__item">
                                <a href="<?= @$banner['url'] ?>" class="banner-section-list__link">
                                    <img class="banner-section-list__item" data-lazy="<?= $banner['imgb'] ?>" src="<?= $i == 0 ? $banner['imgb'] : '' ?>" alt="" >
                                </a>
                            </div>

                        <? endforeach ?>

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

                    <div class="section section_bordered">
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

        <aside class="left-bar">
            <?= $page->blockNavigation() ?>
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

<?= $page->blockAuth() ?>

<?= $page->slotBodyJavascript() ?>
<?= $page->blockUserConfig() ?>

</body>
</html>
