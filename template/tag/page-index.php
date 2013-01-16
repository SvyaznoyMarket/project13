<?php
/**
 * @var $page           \View\Tag\IndexPage
 * @var $productPager   \Iterator\EntityPager
 * @var $productSorting \Model\Product\Sorting
 * @var $productView    string
 * @var $category       \Model\Product\Category\Entity
 * @var $categories     \Model\Product\Category\Entity[]
 */
?>

<div class="clear"></div>

<?= $page->render('product/_pager', array(
    'request'        => $request,
    'pager'          => $productPager,
    'productSorting' => $productSorting,
    'hasListView'    => true,
    'category'       => $category,
    'view'           => $productView,
)) ?>