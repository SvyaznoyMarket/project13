<h1><?php echo $productCategory.' '.$creator ?></h1>

<div class="block">
  <?php include_component('productCatalog', 'navigation', array('productCategory' => $productCategory, 'creator' => $creator)) ?>
</div>

<div class="block">
  <?php include_component('productCatalog', 'filter', array('productCategory' => $productCategory)) ?>
</div>

<div class="block">
  <?php include_component('product', 'pagination', array('productPager' => $productPager)) ?>
</div>

<div class="block">
  <?php include_component('product', 'list', array('productPager' => $productPager)) ?>
</div>

<div class="block">
  <?php include_component('product', 'pagination', array('productPager' => $productPager)) ?>
</div>