<h1><?php echo $productCategory ?></h1>

<div class="block">
  <?php include_component('productCatalog', 'navigation', array('productCategory' => $productCategory)) ?>
</div>

<div class="block">
  <?php include_component('productCatalog', 'filter', array('productCategory' => $productCategory)) ?>
</div>

<div class="block">
  <?php include_component('product', 'pagination', array('productPager' => $productPager)) ?>
</div>
<div class="block">
  <?php include_component('product', 'pager', array('productPager' => $productPager)) ?>
</div>
<div class="block">
  <?php include_component('product', 'pagination', array('productPager' => $productPager)) ?>
</div>
