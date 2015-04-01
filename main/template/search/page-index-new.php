<?php
/**
 * @var $page               \View\Search\IndexPage
 * @var $request            \Http\Request
 * @var $productFilter      \Model\Product\Filter
 * @var $searchQuery        string
 * @var $meanQuery          string
 * @var $forceMean          string
 * @var $productCount       int
 * @var $productPager       \Iterator\EntityPager
 * @var $categories         \Model\Product\Category\Entity[]
 * @var $selectedCategory   \Model\Product\Category\Entity
 * @var $productView        string
 * @var $bannerPlaceholder  array
 **/
?>

<?
    $helper = new \Helper\TemplateHelper();
?>

<div id="bCatalog" class="bCatalog" data-lastpage="<?= $productPager->getLastPage() ?>">

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

    <!--    --><?// if (!$selectedCategory): ?>
    <div class="bSearchCategoryRoot">
        <?= $helper->render('search/__category', [
            'categories'  => $categoriesFound,
            'searchQuery' => $searchQuery,
        ]) // категории товаров в названиях которых есть вхождение искомого слова  ?>
    </div>
    <!--    --><?// endif ?>

    <?= $helper->render('product-category/__filter', [
        'baseUrl'          => $helper->url('search', ['q' => $searchQuery]),
        'countUrl'         => null,
        'productFilter'    => $productFilter,
        'categories'       => $categories,
        'openFilter'       => true,
    ]) // фильтры ?>

    <?= $helper->render('product/__listAction', [
        'pager'          => $productPager,
        'productSorting' => $productSorting,
    ]) // сортировка, режим просмотра, режим листания ?>

    <?= $helper->render('product/__list', [
        'pager'                  => $productPager,
        'view'                   => $productView,
        'bannerPlaceholder'      => !empty($bannerPlaceholder) ? $bannerPlaceholder : [],
    ]) // листинг ?>

    <div class="bSortingLine mPagerBottom clearfix js-category-sortingAndPagination">
        <?= $helper->render('product/__pagination', ['pager' => $productPager]) // листалка ?>
    </div>

</div>