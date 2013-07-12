<?php
/**
 * @var $page     \View\ProductCategory\RootPage
 * @var $category \Model\Product\Category\Entity
 * @var $tag      \Model\Tag\Entity
 */
?>

<? $childTokens = array_keys($sidebarCategoriesTree[$category->getToken()]) ?>

<div class="clear"></div>

<div class="goodslist clearfix">
  <? foreach ($childTokens as $childToken): ?>
      <?= $page->render('tag/_category_preview', array('tag' => $tag, 'category' => $categoriesByToken[$childToken], 'rootCategory' => $category, 'catalogJsonBulk' => $catalogJsonBulk, 'categoryProductCountsByToken' => $categoryProductCountsByToken)) ?>
  <? endforeach ?>
</div>
