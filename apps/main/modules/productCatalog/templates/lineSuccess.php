<?php slot('title', $productCategory) ?>

<?php slot('navigation') ?>
  <?php include_component('productCatalog', 'navigation', array('productCategory' => $productCategory)) ?>
<?php end_slot() ?>

<?php slot('left_column') ?>
  <?php include_component('productCatalog', 'leftCategoryList', array('productCategory' => $productCategory)) ?>
  <?php //include_component('productCatalog', 'filter', array('productCategory' => $productCategory)) ?>
<?php end_slot() ?>

<?php echo include_partial('productCatalog/product_list', $sf_data) ?>
