<?php
/**
 * @var $page             \View\Search\IndexPage
 * @var $request          \Http\Request
 * @var $searchQuery      string
 * @var $meanQuery        string
 * @var $forceMean        string
 * @var $productCount     int
 * @var $productPager     \Iterator\EntityPager
 * @var $categories       \Model\Product\Category\Entity[]
 * @var $selectedCategory \Model\Product\Category\Entity
 * @var $productView      string
 **/
?>

<?
    $helper = new \Helper\TemplateHelper();
?>

<div class="bCatalog">

    <? if ($selectedCategory): ?>
        <?= $helper->render('search/__breadcrumbs', [
            'searchQuery' => $searchQuery,
        ]) // хлебные крошки ?>
    <? endif ?>

    <?= $helper->render('search/__title', [
        'searchQuery' => $searchQuery,
        'meanQuery'   => $meanQuery,
        'forceMean'   => $forceMean,
        'count'       => $productCount,
        'category'    => $selectedCategory,
    ]) ?>

    <? if (!$selectedCategory): ?>
        <?= $helper->render('search/__category', [
            'searchQuery'      => $searchQuery,
            'categories'       => $categories,
            'selectedCategory' => $selectedCategory,
        ]) // категории товаров ?>
    <? endif ?>

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
    <div>

</div>