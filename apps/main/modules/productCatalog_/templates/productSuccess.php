<?php
/**
 * @var $productCategory ProductCategory
 * @var $productFilter ProductCoreFormFilterSimple
 * @var $filterList ProductCategoryFilterEntity
 * @var $productPager ProductCorePager
 * @var $categoryTree ProductCategoryEntity[]
 * @var $sf_data mixed
 */
?>
<?php slot('title', $productCategory) ?>

<?php slot('navigation') ?>
<?php include_component('productCatalog_', 'navigation', array('productCategory' => $productCategory, 'productPager' => $productPager)) ?>
<?php end_slot() ?>
<?php slot('navigation_seo') ?>
<?php include_component('productCatalog_', 'navigation_seo', array('productCategory' => $productCategory)) ?>
<?php end_slot() ?>

<?php slot('left_column') ?>
<?php require '_leftCategoryList_.php' ?>
<?php require '_filter_.php' ?>
<?php require APP_MAIN_MODULES_PATH.'/default/templates/_banner_left.php' ?>
<?php //include_component('productCatalog_', 'article_seo', array('productCategory' => $productCategory, 'productPager' => $productPager)) ?>
<?php end_slot() ?>

<?php require '_plugs/for_tag.php' ?>
<div class="clear" style="height: 20px;"></div>

<?php require '_product_list_.php' ?>

<?php slot('seo_counters_advance') ?>
<?php include_component('productCategory', 'seo_counters_advance', array('unitId' => $productCategory->root_id)) ?>
<?php end_slot() ?>
