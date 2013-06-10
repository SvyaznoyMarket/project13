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

<? if (!$selectedCategory): ?>
  <?= $page->render('product-category/_twoColumnList', [
      'categories'  => $categoriesFound,
      'searchQuery' => $searchQuery,
  ]) ?>
<? endif ?>

<?= $page->render('search/_searchboxValue', [
    'searchQuery' => $searchQuery,
]) ?>

<?= $page->render('product/_pager', [
    'request'         => $request,
    'pager'           => $productPager,
    'hasListView'     => true,
    'category'        => $selectedCategory,
    'view'            => $productView,
    'isAddInfo'       => true,
    'showPagerHeader' => true,
]) ?>
