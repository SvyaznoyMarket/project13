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
 **/
?>

<?
$helper = new \Helper\TemplateHelper();
?>

<form class="bFilter clearfix hidden" action="<?= \App::request()->getRequestUri() ?>" method="GET"></form>

<div class="bCatalog">

    <?= $helper->render('product-category/__breadcrumbs', ['category' => $category]) // хлебные крошки ?>

    <h1 class="bTitlePage"><?= $slice->getName() ?></h1>

    <? if (!empty($promoContent)): ?>
        <?= $promoContent ?>
    <? else: ?>
        <?= $helper->render('product-category/__children', ['category' => $category]) // дочерние категории ?>
    <? endif ?>

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
        'bannerPlaceholder'      => !empty($catalogJson['bannerPlaceholder']) ? $catalogJson['bannerPlaceholder'] : [],
    ]) // листинг ?>

    <div class="bSortingLine mPagerBottom clearfix">
        <?= $helper->render('product/__pagination', ['pager' => $productPager]) // листалка ?>
    </div>

</div>