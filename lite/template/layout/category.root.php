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

<?= $page->blockHeader() ?>

<div class="wrapper wrapper-content">
    <!-- для внутренних страниц добавляется класс middle_transform -->
    <main class="content">
            <!-- баннер -->
            <!--<div class="banner-section">
                <img src="" width="940" height="240" alt="" border="0">
            </div>-->
            <!--/ баннер -->

            <!-- категории товаров -->
            <div class="section">
                <div class="section__title section__title_h1"><?= $category->name ?></div>

                <div class="section__content">
                    <div class="goods goods_categories grid-3col">

                        <? foreach ($page->getParam('links', []) as $link) : ?>

                            <div class="goods__item grid-3col__item">
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
            <!--/ категории товаров -->

            <?= $page->blockViewed() ?>

            <? if (false) : ?>
                <!-- SEO информация -->
                <div class="section section_bordered section_seo">
                    <?= $category->getSeoContent() ?>
                </div>
                <!--/ SEO информация -->
            <? endif ?>
    </main>
</div>

<hr class="hr-orange">

<?= $page->blockFooter() ?>

<?= $page->slotBodyJavascript() ?>

<?= $page->blockUserConfig() ?>

<?= $page->blockPopupTemplates() ?>

</body>
</html>