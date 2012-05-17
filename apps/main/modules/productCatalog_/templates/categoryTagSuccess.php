<?php
/**
 * @var $productCategory ProductCategory
 * @var $sf_data
 * @var $categoryTree
 * @var $categoryTagList
 * @var $productFilter
 * @var $maxPerPage
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
<?php require '_leftCategoryList_.php' ?>
<?php require '_filter_.php' ?>
<?php require APP_MAIN_MODULES_PATH.'/default/templates/_banner_left.php' ?>
<?php include_component('productCatalog_', 'article_seo', array('productCategory' => $productCategory)) ?>
<?php end_slot() ?>

<?php require '_plugs/plug.php' ?>
<div class="clear"></div>

<?php require APP_MAIN_MODULES_PATH.'/productCategory_/templates/_list_carousel.php' ?>
<div class="clear"></div>

<?php slot('seo_counters_advance') ?>
<?php include_component('productCategory', 'seo_counters_advance', array('unitId' => $productCategory->root_id)) ?>
<?php end_slot() ?>