<?php slot('title', mb_ucfirst($tag)) ?>

<?php slot('navigation') ?>
  <?php include_component('tag', 'navigation', array('tag' => $tag)) ?>
<?php end_slot() ?>

<?php slot('left_column') ?>
  <?php //include_component('productCatalog', 'filter', array('productCategory' => $productCategory)) ?>
<?php end_slot() ?>

<?php echo include_partial('productCatalog/product_list', $sf_data) ?>
