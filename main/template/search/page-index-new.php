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
 * @var array $listViewData
 **/
?>

<?
    $helper = new \Helper\TemplateHelper();
?>

<div id="bCatalog js-catalog" class="bCatalog" data-lastpage="<?= $productPager->getLastPage() ?>" data-page="<?= $productPager->getPage() ?>">
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
        'productFilter'    => $productFilter,
        'productPager'     => $productPager,
        'categories'       => $categories,
        'openFilter'       => true,
    ]) // фильтры ?>

    <?= $helper->render('product-category/v2/__listAction', [
        'pager'          => $productPager,
        'productSorting' => $productSorting,
    ]) ?>

    <?= $helper->render('product/__list', ['listViewData' => $listViewData]) ?>

    <div class="bSortingLine mPagerBottom clearfix js-category-sortingAndPagination">
        <?= $helper->render('product/__pagination', ['pager' => $productPager]) // листалка ?>
    </div>

</div>