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

<? if(!$selectedCategory) { ?>
  <?= $page->render('product-category/_twoColumnList', array(
      'categories'  => $categoriesFound,
      'searchQuery' => $searchQuery,
  )) ?>
<? } ?>

<?= $page->render('search/_searchboxValue', array(
    'searchQuery' => $searchQuery,
)) ?>

<?= $page->render('product/_pager', array(
    'request'     => $request,
    'pager'       => $productPager,
    'hasListView' => true,
    'category'    => $selectedCategory,
    'view'        => $productView,
    'showPagerHeader' => true,
)) ?>
