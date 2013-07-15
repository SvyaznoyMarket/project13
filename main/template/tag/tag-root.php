<?php
/**
 * @var $page     \View\ProductCategory\RootPage
 * @var $category \Model\Product\Category\Entity
 * @var $tag      \Model\Tag\Entity
 */
?>

<div class="clear"></div>

<div class="goodslist clearfix">
  <? foreach (array_keys($sidebarCategoriesTree) as $categoryToken): ?>
      <?= $page->render('tag/_category_preview', array('tag' => $tag, 'category' => $categoriesByToken[$categoryToken], 'catalogJsonBulk' => $catalogJsonBulk, 'categoryProductCountsByToken' => $categoryProductCountsByToken)) ?>
  <? endforeach ?>
</div>
