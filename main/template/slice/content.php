<?php
/**
 * @var $page             \View\Search\IndexPage
 * @var $request          \Http\Request
 * @var $productFilter    \Model\Product\Filter
 * @var $category         \Model\Product\Category\Entity
 * @var $slice            \Model\Slice\Entity
 * @var $productPager     \Iterator\EntityPager
 * @var $categories       \Model\Product\Category\Entity[]
 * @var $productView      string
 * @var $hasCategoryChildren bool
 * @var $cartButtonSender array
 **/
?>

<?
$helper = new \Helper\TemplateHelper();
?>

<?= $helper->render('slice/__data', ['slice' => $slice]) ?>

<div class="bCatalog js-slice" id="bCatalog" data-lastpage="<?= $productPager->getLastPage() ?>">

    <?= $helper->render('slice/__breadcrumbs', ['category' => $category, 'slice' => $slice]) // хлебные крошки ?>

    <h1 class="bTitlePage js-pageTitle"><?= $slice->getName() ?></h1>

    <? if ($productPager->getLastPage() > 1 && $hasCategoryChildren): // SITE-2644, SITE-3558 ?>
        <?= $helper->render('product-category/__children', ['category' => $category]) // дочерние категории ?>
    <? endif ?>

    <?= $helper->render('product-category/__filter', [
        'baseUrl'       => $helper->url(),
        'productFilter' => $productFilter,
        'openFilter'    => false,
        'productPager'  => $productPager,
    ]) ?>

    <?= $helper->render('product/__listAction', [
        'pager'          => $productPager,
        'productSorting' => $productSorting,
    ]) // сортировка, режим просмотра, режим листания ?>

    <?= $helper->render('product/__list', [
        'pager'            => $productPager,
        'category'         => $category,
        'view'             => $productView,
        'buyMethod'        => $slice->getProductBuyMethod(),
        'showState'        => $slice->getShowProductState(),
        'cartButtonSender' => $cartButtonSender,
    ]) // листинг ?>

    <div class="bSortingLine mPagerBottom clearfix js-category-sortingAndPagination">
        <?= $helper->render('product/__pagination', ['pager' => $productPager]) // листалка ?>
    </div>

    <? if(!empty($seoContent)): ?>
        <div class="bSeoText">
            <?= $seoContent ?>
        </div>
    <? endif ?>
</div>