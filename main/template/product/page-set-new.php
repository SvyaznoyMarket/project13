<?php
/**
 * @var $page           \View\Product\SetPage
 * @var $pager          \Iterator\EntityPager
 * @var $categoriesById \Model\Product\Category\Entity[]
 * @var array $listViewData
 */

$helper = new \Helper\TemplateHelper();
/* // SITE-2886 — В подборках не выводить список категорий товаров
$categoriesLinks = [];

// подкатегории:
foreach ( $categoriesById as $subCategory ) {
    // @var $subCategory \Model\Product\Category\Entity
    $categoriesLinks[] = [
        'name'      => $subCategory->getName(),
        'url'       => $page->url('product.category', ['categoryPath' => $subCategory->getPath()]),
        'image'     => $subCategory->getImageUrl(),
        //'active'    => false,
    ];
}*/

?>
<div class="bCatalog js-catalog" id="bCatalog" data-lastpage="<?= $productPager->getLastPage() ?>" data-page="<?= $productPager->getPage() ?>">

    <? /*if (\App::config()->adFox['enabled']): ?>
        <!-- Баннер --><div id="adfox683sub" class="adfoxWrapper bBannerBox"></div><!--/ Баннер -->
    <? endif */?>

    <? /*if ( !empty($categoriesLinks) ) { ?>
        <p class="bTitlePage">Похожие товары можно найти в категориях:</p>
        <? echo $helper->renderWithMustache('product-category/_listInFilter', ['links' => $categoriesLinks]); // категории ?>
    <? }*/ ?>


    <? if (!empty($pageTitle)): ?>
        <h1 class="bTitlePage js-pageTitle"><?= $pageTitle ?></h1>
    <? endif; ?>

    <?= $helper->render( 'product/__listAction', [
        'pager' => $productPager,
        'productSorting' => $productSorting,
    ] ) // сортировка, режим просмотра, режим листания ?>

    <?= $helper->render('product/__list', ['listViewData' => $listViewData]) ?>

    <div class="bSortingLine mPagerBottom clearfix js-category-sortingAndPagination">
        <?= $helper->render('product/__pagination', ['pager' => $productPager]) // листалка ?>
    </div>

    <? if(!empty($seoContent)): ?>
        <div class="bSeoText">
            <?= $seoContent ?>
        </div>
    <? endif ?>
</div>