<?php
/**
 * @var ProductCategory $productCategoryEntity
 * @var ProductCategoryEntity[] $productCategoryTree
 * @var ProductCategoryEntity[] $categoryList
 * @var ProductCoreFormFilterSimple $productFilter
 * @var ProductCategoryEntity[] $rootCategory
 * @var $sf_data
 */
?>
<?php slot('title', $productCategory) ?>

<?php slot('navigation') ?>
<?php include_component('productCatalog_', 'navigation', array('productCategory' => $productCategory)) ?>
<?php end_slot() ?>
<?php slot('navigation_seo') ?>
<?php include_component('productCatalog_', 'navigation_seo', array('productCategory' => $productCategory)) ?>
<?php end_slot() ?>

<?php slot('left_column') ?>
<?php require '_leftCategoryList_.php'; ?>
<?php require '_filter_.php'; ?>
<?php require APP_MAIN_MODULES_PATH.'/default/templates/_banner_left.php' ?>
<?php include_component('productCatalog_', 'article_seo', array('productCategory' => $productCategory)) ?>
<?php end_slot() ?>

<?php require '_plugs/plug.php' ?>

<div class="clear"></div>

<!-- Goods -->

<?php if(count($categoryList) > 0): ?>
  <div class="goodslist">
    <?php foreach ($categoryList as $category): ?>
      <?php render_partial('productCatalog_/templates/_category_preview_.php', array('category' => $category, 'rootCategory' => $rootCategory));?>
    <?php endforeach ?>
  </div>
<?php else: ?>
  <p>Нет категорий</p>
<?php endif ?>

<!-- /Goods -->

<?php if (false && 7 == $productCategory->getId()): ?>
<?php slot('sport_sale_design') ?>
<a class='snow_link'
   href='<?php echo url_for('productCatalog_category', array('productCategory' => 'sport/zimnie-vidi-sporta-710')) ?>'></a>
<div class='snow_wrap'>
    <div class='snow_left'>
        <div class='snow_right'></div>
    </div>
</div>
<?php end_slot() ?>
<?php endif; ?>