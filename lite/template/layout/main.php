<?
/**
 * @var $page \View\Main\IndexPage
 * @var $bannerData []
 */

$bannerSlickConfig = [
    'lazyLoad' => 'ondemand',
    'slidesToShow' => 1,
    'slidesToScroll' => 1,
    'infinite'=> true,
    'autoplay'=> true,
    'dots'=> true,
    'slider' => '.js-banners-slider'
];
?>

<!doctype html>
<html class="no-js" lang="">

<?= $page->blockHead() ?>

<body>

<?= $page->blockHeader() ?>

<div class="wrapper">
    <div class="middle">
        <div class="container">
            <main class="content">
                <div class="content__inner">
                    <div class="banner-section js-module-require" data-module="jquery.slick" data-slick-config='<?= json_encode($bannerSlickConfig) ?>'>
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

                    <?= $page->render('main/_slider.also') ?>

                    <?= $page->render('main/_brands.popular') ?>

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
