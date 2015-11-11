<?php

use Model\Product\Category\Entity as Category;
/**
 * @var $page               \View\DefaultLayout
 * @var $rootCategoryInMenu \Model\Product\Category\TreeEntity
 * @var $catalogCategories  \Model\Product\Category\TreeEntity[]
 * @var $catalogConfig      array
 * @var $slideData          array
 * @var $bannerBottom       string
 * @var $promoContent       string
 * @var $categoryWithChilds Category
 */


$helper = new \Helper\TemplateHelper();

$siblingCategories = $rootCategoryInMenu ? $rootCategoryInMenu->getChild() : [];

$isCategoriesOddCount = (bool)(count($catalogCategories) % 2 == 1);

if ((bool)$siblingCategories) {
    /* <!-- TCHIBO - слайдер-меню разделов Чибо --> */
    echo $helper->render('product-category/__sibling-list',
        [
            'categories'        => $siblingCategories, // категории-соседи
            'catalogConfig'     => isset($catalogConfig) ? $catalogConfig : null,
            'currentCategory'   => $categoryWithChilds
        ]);
    /* <!--/ TCHIBO - слайдер-меню разделов Чибо -->*/
}

// background image для title в Чибе
$titleBackgroundStyle = '';
if ($category->isTchibo()) {
    $titleBackgroundStyle = $category->getMediaSource('category_wide_940x150', 'category_wide')->url
        ? sprintf("background-image: url('%s')",
            $category->getMediaSource('category_wide_940x150', 'category_wide')->url)
        : '';
}

?>

    <? if ($titleBackgroundStyle) : ?>
        <h1 class="bTitlePage_tchibo js-pageTitle" style="<?= $titleBackgroundStyle ?>"><?= $title ?></h1>
    <? endif ?>

<?

/** @var Category[] $categoryByUi */
$categoryByUi = [];
if ($categoryWithChilds) {
    foreach ($categoryWithChilds->getChild() as $ct) {
        $categoryByUi[$ct->getUi()] = $ct;
    }
}

?>

<!-- Разделы -->
<div class="s-sales-grid">
    <div class="s-sales-grid__row grid-2cell cell-h-340">
        <? foreach($catalogCategories as $key => $catalogCategory): ?>
            <?
            /** @var \Model\Product\Category\TreeEntity $catalogCategory */

            if (array_key_exists($catalogCategory->getUi(), $categoryByUi)) {
                $imgSrc = $categoryByUi[$catalogCategory->getUi()]->getMediaSource('category_grid_366x488', 'category_grid')->url;
            }

            if (empty($imgSrc)) {
                $imgSrc = $catalogCategory->getImageUrl(3);
            }
            ?>

            <div class="s-sales-grid__cell">
                <a class="s-sales-grid__link" href="<?= $catalogCategory->getLink() ?>">
                    <img src="<?= $imgSrc ?>" alt="<?= $catalogCategory->getName() ?>" class="s-sales-grid__img">
                    <span class="s-sales-grid-desc">
                        <span class="s-sales-grid-desc__title">
                            <?= $catalogCategory->getName() ?>
                        </span>
                    </span>
                </a>
            </div>
        <? endforeach; ?>

        <? if (count($catalogCategories) % 2 === 1) : ?>
            <!-- Дополняем до четного количества баннером -->
            <div class="s-sales-grid__cell">
                <a class="s-sales-grid__link" href="">
                    <img src='//www.imgenter.ru/uploads/media/c5/30/4c/036ebabd7dadfefba83f9d5a24a6c40099f8ac8a.jpeg' class="s-sales-grid__img">
                </a>
            </div>
        <? endif ?>
    </div>
<!-- END Разделы -->

<? if ($slideData) : ?>
    <div class="slider2">
        <?= $helper->render('tchibo/promo-catalog', ['slideData' => $slideData, 'categoryToken' => 'tchibo']); ?>
    </div>
<? endif ?>

<div style="margin: 0 0 30px;">
    <? if (\App::config()->product['pullRecommendation'] && \App::config()->product['viewedEnabled']): ?>
        <?= $helper->render('product/__slider', [
            'type'      => 'viewed',
            'title'     => 'Вы смотрели',
            'products'  => [],
            'limit'     => \App::config()->product['itemsInSlider'],
            'page'      => 1,
            'url'       => $page->url('product.recommended'),
            'sender'    => [
                'name'     => 'enter',
                'position' => 'Viewed',
                'from'     => 'categoryPage',
            ],
        ]) ?>
    <? endif ?>
</div>