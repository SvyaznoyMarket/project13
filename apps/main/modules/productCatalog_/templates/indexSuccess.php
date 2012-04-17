<?php slot('title', 'Каталог товаров') ?>

<?php slot('navigation') ?>
<?php include_component('productCatalog_', 'navigation') ?>
<?php end_slot() ?>

<?php include_component('productCatalog_', 'category_list', array('productCategoryList' => $productCategoryList)) ?>

<?php slot('seo_counters_advance') ?>
<?php include_component('productCategory', 'seo_counters_advance', array('unitId' => $productCategory->root_id)) ?>
<?php end_slot() ?>
