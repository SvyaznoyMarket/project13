<h1><?php echo $productCategory.' '.$creator ?></h1>

<div class="block">
  <?php include_component('productCatalog', 'navigation', array('productCategory' => $productCategory, 'creator' => $creator)) ?>
</div>

<div class="block">
  <?php include_component('productCatalog', 'filter', array('productCategory' => $productCategory, 'creator' => $creator)) ?>
</div>

<?php echo include_partial('productCatalog/slot/default', $sf_data) ?>
