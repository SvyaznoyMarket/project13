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
<?php require '_leftCategoryList_.php' ?>
<?php require '_filter_.php' ?>
<?php require APP_MAIN_MODULES_PATH.'/default/templates/_banner_left.php' ?>
<?php include_component('productCatalog_', 'article_seo', array('productCategory' => $productCategory)) ?>
<?php end_slot() ?>

<?php require '_product_list_.php' ?>
