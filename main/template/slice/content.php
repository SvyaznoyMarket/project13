<?php
/**
 * @var $page             \View\Search\IndexPage
 * @var $request          \Http\Request
 * @var $heading          string
 * @var $productFilter    \Model\Product\Filter
 * @var $category         \Model\Product\Category\Entity
 * @var $slice            \Model\Slice\Entity
 * @var $productPager     \Iterator\EntityPager
 * @var $categories       \Model\Product\Category\Entity[]
 * @var $hasCategoryChildren bool
 * @var $seoContent       string
 * @var $hotlinks         array
 * @var array $listViewData
 **/
?>

<?
$helper = new \Helper\TemplateHelper();
?>

<?= $helper->render('slice/__data', ['slice' => $slice]) ?>

<div class="bCatalog js-slice js-catalog" id="bCatalog" data-lastpage="<?= $productPager->getLastPage() ?>" data-page="<?= $productPager->getPage() ?>">

    <?= $helper->renderWithMustache('_breadcrumbs', ['links' => $breadcrumbs]) ?>

    <h1 class="bTitlePage js-pageTitle"><?= $heading ?></h1>

    <? if ($productPager->getLastPage() > 1 && $hasCategoryChildren): // SITE-2644, SITE-3558 ?>
        <?= $helper->render('product-category/__children', ['category' => $category]) // дочерние категории ?>
    <? endif ?>

    <?= $helper->render('product-category/__filter', [
        'productFilter' => $productFilter,
        'openFilter'    => false,
        'productPager'  => $productPager,
    ]) ?>

    <?= $helper->render('product-category/v2/__listAction', [
        'pager'          => $productPager,
        'productSorting' => $productSorting,
        'category'       => $category,
    ]) ?>

    <?= $helper->render('product/__list', ['listViewData' => $listViewData]) ?>

    <div class="bSortingLine mPagerBottom clearfix js-category-sortingAndPagination">
        <?= $helper->render('product/__pagination', ['pager' => $productPager]) // листалка ?>
    </div>

    <? if (!empty($seoContent) || (bool)$hotlinks): ?>
        <div class="bSeoText">
            <?= $seoContent ?>
            <?= $helper->render('product-category/__hotlink', ['hotlinks' => $hotlinks]) ?>
        </div>
    <? endif ?>
</div>