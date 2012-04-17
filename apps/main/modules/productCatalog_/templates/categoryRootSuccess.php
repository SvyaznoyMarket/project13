<?php
/**
 * @var ProductCategory $productCategory
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
<?php include_partial('productCatalog_/leftCategoryList_', $sf_data) ?>
<?php include_partial('productCatalog_/filter_', $sf_data) ?>
<?php include_partial('default/banner_left') ?>
<?php include_component('productCatalog_', 'article_seo', array('productCategory' => $productCategory)) ?>
<?php end_slot() ?>

<?php include_partial('productCatalog_/plugs/plug') ?>

<div class="clear"></div>

<!-- Goods -->
<div class="goodslist">
  <?php
  foreach ($categoryList as $category)
    include_partial('productCatalog_/category_preview_', array('category' => $category, 'rootCategory' => $rootCategory));
  ?>
</div>
<!-- /Goods -->

<?php slot('seo_counters_advance') ?>
<?php include_component('productCategory', 'seo_counters_advance', array('unitId' => $productCategory->root_id)) ?>
<?php end_slot() ?>

<?php if (false && 7 == $productCategory->id): ?>
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