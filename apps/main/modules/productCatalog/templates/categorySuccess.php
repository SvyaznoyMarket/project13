<h1><?php echo $productCategory ?></h1>

<div class="block">
  <?php include_component('productCatalog', 'navigation', array('productCategory' => $productCategory)) ?>
</div>

<div class="block">
  <?php include_component('productCatalog', 'filter', array('productCategory' => $productCategory)) ?>
</div>

<?php echo include_partial('productCatalog/slot/default', $sf_data) ?>
