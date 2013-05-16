<?php
/**
 * @var $page             \View\Search\IndexPage
 * @var $request          \Http\Request
 * @var $productPager     \Iterator\EntityPager
 * @var $categories       \Model\Product\Category\Entity[]
 * @var $selectedCategory \Model\Product\Category\Entity
 * @var $productView      string
 **/
?>

<?= $page->render('product/_pager', array(
    'request'     => $request,
    'pager'       => $productPager,
    'hasListView' => true,
    'category'    => $selectedCategory,
    'view'        => $productView,
    'isAddInfo'    => true,
)) ?>
