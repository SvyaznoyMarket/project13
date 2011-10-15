<?php slot('title', $productCategory) ?>

<?php slot('navigation') ?>
  <?php include_component('productCatalog', 'navigation', array('productCategory' => $productCategory)) ?>
<?php end_slot() ?>

<?php slot('left_column') ?>
  <?php include_component('productCatalog', 'tag', array('productCategory' => $productCategory)) ?>
<?php end_slot() ?>

  <?php include_component('product', 'pagination', array('pager' => $productPager)) ?>
  <?php include_component('product', 'list_view') ?>
  <div class="line"></div>
  <?php include_component('product', 'pager', array('pager' => $productPager)) ?>
  <div class="line pb10"></div>
  <?php include_component('product', 'pagination', array('pager' => $productPager)) ?>
  
<div class="clear"></div>
