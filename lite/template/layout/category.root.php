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
                                                <div class="goods__name-inn">
                                                    <a href="<?= $link['url'] ?>"><span class="underline"><?= $link['name'] ?></span></a>
                                                </div>
                                            </div>

                                            <div class="goods__cat-count"><?= $link['totalText'] ?></div>
                                        </div>

                                    <? endforeach ?>

                                </div>
                            </div>
                        </div>
                    </div>
                    <!--/ категории товаров -->

                    <?= $page->blockViewed() ?>

                    <!-- SEO информация -->
                    <div class="section section_bordered section_seo">
                        <?= $category->getSeoContent() ?>
                    </div>
                    <!--/ SEO информация -->
                </div>
            </main>
        </div>

        <aside class="left-bar left-bar_transform">
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