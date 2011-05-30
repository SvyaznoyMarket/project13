<h1><?php echo $productCategory ?></h1>

<?php include_component('productCatalog', 'filter_creator', array('productCategory' => $productCategory)) ?>
<?php include_component('product', 'list', array('productList' => $productList)) ?>
