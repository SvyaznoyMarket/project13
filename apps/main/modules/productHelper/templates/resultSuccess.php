<h1><?php echo $productHelper->name ?></h1>

<?php include_component('productHelper', 'filter', array('productHelper' => $productHelper, 'productHelperFilter' => $productHelperFilter)) ?>

<?php include_component('product', 'pager', array('productPager' => $productPager)) ?>