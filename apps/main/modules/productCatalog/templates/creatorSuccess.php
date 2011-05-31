<h1><?php echo $productCategory.' '.$creator ?></h1>

<?php include_component('product', 'pagination', array('productPager' => $productPager)) ?>
<?php include_component('product', 'list', array('productPager' => $productPager)) ?>
<?php include_component('product', 'pagination', array('productPager' => $productPager)) ?>