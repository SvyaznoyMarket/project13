<?php
/**
 * @var $page             \View\Search\IndexPage
 * @var $request          \Http\Request
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

    <?= $helper->render('search/__title', [
        'searchQuery' => $searchQuery,
        'meanQuery'   => $meanQuery,
        'forceMean'   => $forceMean,
        'count'       => $productCount,
    ]) ?>

    <?= $helper->render('search/__category', [
        'searchQuery'      => $searchQuery,
        'categories'       => $categories,
        'selectedCategory' => $selectedCategory,
    ]) // категории товаров ?>

    <?= $helper->render('product/__listAction', [
        'pager'          => $productPager,
        'productSorting' => $productSorting,
    ]) // сортировка, режим просмотра, режим листания ?>

    <?= $helper->render('product/__list', [
        'pager'                  => $productPager,
        'view'                   => $productView,
        'productVideosByProduct' => [], //$productVideosByProduct,
    ]) // листинг ?>

    <?= $helper->render('product/__pagination', ['pager' => $productPager]) // листалка ?>

</div>