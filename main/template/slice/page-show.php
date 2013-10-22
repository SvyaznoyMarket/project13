<?php
/**
 * @var $page             \View\Search\IndexPage
 * @var $request          \Http\Request
 * @var $productFilter    \Model\Product\Filter
 * @var $slice            \Model\Slice\Entity
 * @var $productPager     \Iterator\EntityPager
 * @var $categories       \Model\Product\Category\Entity[]
 * @var $productView      string
 **/
?>

<?
$helper = new \Helper\TemplateHelper();
?>

<div class="bCatalog">

    <? if (false): ?>
        <?= $helper->render('slice/__breadcrumbs', [
            'slice' => $slice,
        ]) // хлебные крошки ?>
    <? endif ?>

    <h1><?= $slice->getName() ?></h1>

    <?/*= $helper->render('product-category/__filter', [
        'baseUrl'          => $helper->url('slice.show', ['sliceToken' => $slice->getToken()]),
        'countUrl'         => null,
        'productFilter'    => $productFilter,
        'categories'       => $categories,
    ])*/ // фильтры ?>

    <?= $helper->render('product/__listAction', [
        'pager'          => $productPager,
        'productSorting' => $productSorting,
    ]) // сортировка, режим просмотра, режим листания ?>

    <?= $helper->render('product/__list', [
        'pager'                  => $productPager,
        'view'                   => $productView,
        'productVideosByProduct' => [], //$productVideosByProduct,
    ]) // листинг ?>

    <div class="bSortingLine mPagerBottom clearfix">
        <?= $helper->render('product/__pagination', ['pager' => $productPager]) // листалка ?>
    </div>

</div>