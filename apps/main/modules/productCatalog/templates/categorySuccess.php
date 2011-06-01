<h1><?php echo $productCategory ?></h1>

<?php include_component('productCatalog', 'filter', array('productCategory' => $productCategory)) ?>

<?php include_component('product', 'pagination', array('productPager' => $productPager)) ?>
<?php include_component('product', 'list', array('productPager' => $productPager)) ?>
<?php include_component('product', 'pagination', array('productPager' => $productPager)) ?>
