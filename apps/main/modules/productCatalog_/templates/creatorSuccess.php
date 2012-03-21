<h1><?php echo $productCategory . ' ' . $creator ?></h1>

<div class="block">
    <?php include_component('productCatalog_', 'navigation', array('productCategory' => $productCategory, 'creator' => $creator)) ?>
</div>

<div class="block">
  <?php include_partial('filter_', $sf_data) ?>
</div>

<?php echo include_partial('productCatalog_/slot/default', $sf_data) ?>
