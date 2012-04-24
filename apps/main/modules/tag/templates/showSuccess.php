<?php slot('title', mb_ucfirst($tag)) ?>
<?php include_component('default', 'cache', array('record' => $tag)) ?>

<?php slot('navigation') ?>
  <?php include_component('tag', 'navigation', array('tag' => $tag)) ?>
<?php end_slot() ?>

<?php slot('left_column') ?>
  <?php include_component('tag', 'filter_productType', array('tag' => $tag, 'productTypeList' => $productTypeList, 'productType' => $productType)) ?>
<?php end_slot() ?>

<?php echo include_partial('product/product_list', $sf_data) ?>
