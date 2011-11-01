<?php slot('title', $productCategory) ?>

<?php slot('navigation') ?>
  <?php include_component('productCatalog', 'navigation', array('productCategory' => $productCategory)) ?>
<?php end_slot() ?>

<?php slot('left_column') ?>
  <?php include_component('productCatalog', 'tag', array('productCategory' => $productCategory)) ?>
<?php end_slot() ?>

<?php include_partial('productCatalog/slot/default', $sf_data) ?>
