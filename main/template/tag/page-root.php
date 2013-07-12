<?php
/**
 * @var $page     \View\ProductCategory\RootPage
 * @var $category \Model\Product\Category\Entity
 */
?>

<?
$childTokens = array_keys($sidebarCategoriesTree[$category->getToken()]);
?>
<div class="clear"></div>

<?= $category->getToken() ?>
<br>
<?= json_encode($categoriesByToken) ?>
<br>
<br>
<?= $category->getProductCount() ?>
<br>
<?= $categoriesByToken[$category->getToken()]->getProductCount() ?>
<br>
<?= json_encode($sidebarCategoriesTree) ?>



<div class="goodslist clearfix">
  <? foreach ($childTokens as $childToken): ?>
      <?= $page->render('tag/_category_preview', array('category' => $categoriesByToken[$childToken], 'rootCategory' => $category, 'catalogJsonBulk' => $catalogJsonBulk)) ?>
  <? endforeach ?>
</div>
