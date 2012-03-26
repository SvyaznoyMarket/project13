<?php
/**
 * @var $productCategory ProductCategory
 * @var $productFilter ProductCoreFormFilterSimple
 * @var $filterList ProductCategoryFilterEntity
 * @var $productPager ProductCorePager
 * @var $sf_data mixed
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
<?php include_component('productCatalog_', 'leftCategoryList', array('productCategory' => $productCategory)) ?>
<?php include_partial('filter_', $sf_data) ?>
<?php include_partial('default/banner_left') ?>
<?php include_component('productCatalog_', 'article_seo', array('productCategory' => $productCategory, 'productPager' => $productPager)) ?>
<?php end_slot() ?>

<?php include_partial('product_list_', $sf_data) ?>

<?php slot('seo_counters_advance') ?>
<?php include_component('productCategory', 'seo_counters_advance', array('unitId' => $productCategory->root_id)) ?>
<?php end_slot() ?>
