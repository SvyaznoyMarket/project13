<?php
/**
 * @var $page           \View\ProductCategory\LeafPage
 * @var $category       \Model\Product\Category\Entity
 * @var $productFilter  \Model\Product\Filter
 * @var $productPager   \Iterator\EntityPager
 * @var $productSorting \Model\Product\Sorting
 * @var $productView    string
 */
?>

<div class="adfoxWrapper" id="adfox683sub"></div>

<div class="clear"></div>

<?= $page->render('product/_pager', array(
    'request'        => $request,
    'pager'          => $productPager,
    'productFilter'  => $productFilter,
    'productSorting' => $productSorting,
    'hasListView'    => true,
    'category'       => $category,
    'view'           => $productView,
)) ?>