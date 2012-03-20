<?php slot('title', $productCategory) ?>

<?php slot('navigation') ?>
<?php include_component('productCatalog_', 'navigation', array('productCategory' => $productCategory)) ?>
<?php end_slot() ?>

<?php slot('left_column') ?>
<?php include_component('productCatalog_', 'leftCategoryList', array('productCategory' => $productCategory)) ?>
<?php include_component('productCatalog_', 'filter', array('productCategory' => $productCategory)) ?>
<?php include_partial('default/banner_left') ?>
<?php include_component('productCatalog_', 'article_seo', array('productCategory' => $productCategory)) ?>
<?php end_slot() ?>

<?php echo include_partial('productCatalog_/product_list', $sf_data) ?>
