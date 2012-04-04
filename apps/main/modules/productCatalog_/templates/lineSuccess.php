<?php
/**
 * @var $productCategory
 * @var $sf_data
 */
?>
<?php slot('title', $productCategory) ?>

<?php slot('navigation') ?>
<?php include_component('productCatalog_', 'navigation', array('productCategory' => $productCategory)) ?>
<?php end_slot() ?>

<?php slot('left_column') ?>
<?php include_partial('leftCategoryList_', $sf_data) ?>
<?php include_partial('filter_', $sf_data) ?>
<?php include_partial('default/banner_left') ?>
<?php include_component('productCatalog_', 'article_seo', array('productCategory' => $productCategory)) ?>
<?php end_slot() ?>

<?php include_partial('product_list_', $sf_data) ?>
