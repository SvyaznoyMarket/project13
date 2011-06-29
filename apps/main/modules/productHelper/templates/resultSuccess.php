<h1><?php echo $productHelper->name ?></h1>

<div class="block">
  <?php include_component('productHelper', 'filter', array('productHelper' => $productHelper, 'productHelperFilter' => $productHelperFilter)) ?>
</div>

<div class="block">
  <?php include_component('product', 'pager', array('productPager' => $productPager)) ?>
</div>