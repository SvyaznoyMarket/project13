<?
/**
 * @var $page \View\Main\IndexPage
 * @var $links array
 * @var $category \Model\Product\Category\Entity
 */

$category = $page->getParam('category');
?>

<!doctype html>
<html class="no-js" lang="">

<?= $page->blockHead() ?>

<body>
<div class="wrapper">

    <?= $page->blockHeader() ?>

    <hr class="hr-orange">

    <!-- для внутренних страниц добавляется класс left-bar_transform -->
    <aside class="left-bar left-bar_transform">
        <?= $page->blockNavigation() ?>
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
                <div class="section__title"><?= $category->name ?></div>

                <div class="section__content">
                    <div class="slider-section">
                        <div class="goods goods_categories grid-4col">

                            <? foreach ($page->getParam('links', []) as $link) : ?>

                                <div class="goods__item grid-4col__item">
                                    <a href="<?= $link['url'] ?>" class="goods__img">
                                        <img src="<?= $link['image'] ?>" alt="<?= $link['name'] ?>" class="goods__img-image">
                                    </a>

                                    <div class="goods__name">
                                        <a class="underline" href=""><?= $link['name'] ?></a>
                                    </div>

                                    <div class="goods__cat-count"><?= $link['totalText'] ?></div>
                                </div>

                            <? endforeach ?>


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
                <?= $category->getSeoContent() ?>
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

<?= $page->blockAuth() ?>

<?= $page->slotBodyJavascript() ?>
<?= $page->blockUserConfig() ?>

</body>
</html>
