<?php slot('title', $productCategory . ($productTagFilter->getSingleCreator() ? (' ' . $productTagFilter->getSingleCreator()->name) : '')) ?>

<?php slot('navigation') ?>
<?php include_component('productCatalog_', 'navigation', array('productCategory' => $productCategory)) ?>
<?php end_slot() ?>

<?php slot('left_column') ?>
<?php include_component('productCatalog_', 'leftCategoryList', array('productCategory' => $productCategory)) ?>
<?php include_component('productCatalog_', 'tag', array('productCategory' => $productCategory, 'form' => $productTagFilter,)) ?>
<?php include_partial('default/banner_left') ?>
<?php include_component('productCatalog_', 'article_seo', array('productCategory' => $productCategory)) ?>
<?php end_slot() ?>

<?php include_partial('product_list_', $sf_data) ?>

<div class="clear"></div>

<?php slot('seo_counters_advance') ?>
<?php include_component('productCategory', 'seo_counters_advance', array('unitId' => $productCategory->root_id)) ?>
<?php end_slot() ?>
