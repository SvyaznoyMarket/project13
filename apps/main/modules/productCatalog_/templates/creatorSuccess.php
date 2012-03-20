<h1><?php echo $productCategory . ' ' . $creator ?></h1>

<div class="block">
    <?php include_component('productCatalog_', 'navigation', array('productCategory' => $productCategory, 'creator' => $creator)) ?>
</div>

<div class="block">
    <?php include_component('productCatalog_', 'filter', array('productCategory' => $productCategory, 'creator' => $creator)) ?>
</div>

<?php echo include_partial('productCatalog_/slot/default', $sf_data) ?>
