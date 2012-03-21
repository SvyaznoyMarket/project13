<?php slot('title', $productCategory . ($productFilter->getSingleCreator() ? (' ' . $productFilter->getSingleCreator()->name) : '')) ?>

<?php slot('navigation') ?>
<?php include_component('productCatalog_', 'navigation', array('productCategory' => $productCategory)) ?>
<?php end_slot() ?>

<?php slot('left_column') ?>
<?php include_component('productCatalog_', 'leftCategoryList', array('productCategory' => $productCategory)) ?>
<?php include_partial('filter_', $sf_data) ?>
<?php include_partial('default/banner_left') ?>
<?php include_component('productCatalog_', 'article_seo', array('productCategory' => $productCategory)) ?>
<?php end_slot() ?>

<?php echo include_partial('productCatalog_/product_list', $sf_data) ?>

<?php slot('seo_counters_advance') ?>
<?php include_component('productCategory', 'seo_counters_advance', array('unitId' => $productCategory->root_id)) ?>
<?php end_slot() ?>