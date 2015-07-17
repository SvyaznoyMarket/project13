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
                                            <div class="goods__name-inn">
                                                <a href="<?= $link['url'] ?>"><span class="underline">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</span></a>
                                            </div>
                                        </div>

                                        <div class="goods__cat-count">123 товара</div>
                                    </div>

                                    <div class="goods__item grid-4col__item">
                                        <a href="" class="goods__img">
                                            <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                        </a>

                                        <div class="goods__name">
                                            <div class="goods__name-inn">
                                                <a href="<?= $link['url'] ?>"><span class="underline">Чехол-книжка</span></a>
                                            </div>
                                        </div>

                                        <div class="goods__cat-count">123 товара</div>
                                    </div>

                                    <div class="goods__item grid-4col__item">
                                        <a href="" class="goods__img">
                                            <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                        </a>

                                        <div class="goods__name">
                                            <div class="goods__name-inn">
                                                <a href="<?= $link['url'] ?>"><span class="underline">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</span></a>
                                            </div>
                                        </div>

                                        <div class="goods__cat-count">123 товара</div>
                                    </div>

                                    <div class="goods__item grid-4col__item">
                                        <a href="" class="goods__img">
                                            <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                        </a>

                                        <div class="goods__name">
                                            <div class="goods__name-inn">
                                                <a href="<?= $link['url'] ?>"><span class="underline">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</span></a>
                                            </div>
                                        </div>

                                        <div class="goods__cat-count">123 товара</div>
                                    </div>

                                    <div class="goods__item grid-4col__item">
                                        <a href="" class="goods__img">
                                            <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                        </a>

                                        <div class="goods__name">
                                            <div class="goods__name-inn">
                                                <a href="<?= $link['url'] ?>"><span class="underline">Чехол-книжка</span></a>
                                            </div>
                                        </div>

                                        <div class="goods__cat-count">123 товара</div>
                                    </div>

                                    <div class="goods__item grid-4col__item">
                                        <a href="" class="goods__img">
                                            <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="goods__img-image">
                                        </a>

                                        <div class="goods__name">
                                            <div class="goods__name-inn">
                                                <a href="<?= $link['url'] ?>"><span class="underline">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</span></a>
                                            </div>
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

                    <?= $page->blockViewed() ?>

                </div>
            </main>
        </div>

        <aside class="left-bar">
            <?= $page->blockNavigation() ?>
        </aside>
    </div>
</div>

<hr class="hr-orange">

<?= $page->blockFooter() ?>

<?= $page->blockAuth() ?>

<?= $page->slotBodyJavascript() ?>

<?= $page->blockUserConfig() ?>

</body>
</html>
