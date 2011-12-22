<?php slot('title', $productCategory) ?>

<?php slot('navigation') ?>
  <?php include_component('productCatalog', 'navigation', array('productCategory' => $productCategory)) ?>
<?php end_slot() ?>

<?php slot('left_column') ?>
  <?php include_component('productCatalog', 'filter', array('productCategory' => $productCategory, 'form' => $productFilter)) ?>
  <?php include_partial('default/banner_left') ?>
  <?php include_component('productCatalog', 'article_seo', array('productCategory' => $productCategory)) ?>
<?php end_slot() ?>

<?php echo include_partial('productCatalog/product_list', $sf_data) ?>

<?php slot('seo_counters_advance') ?>
  <?php include_component('productCategory', 'seo_counters_advance', array('unitId' => $productCategory->root_id)) ?>
<?php end_slot() ?>
