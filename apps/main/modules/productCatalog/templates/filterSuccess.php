<h1><?php echo $productCategory ?></h1>

<div class="block">
  <?php include_component('productCatalog', 'navigation', array('productCategory' => $productCategory)) ?>
</div>

<div class="block">
  <?php include_component('productCatalog', 'filter', array('productCategory' => $productCategory, 'productFilter' => $productFilter)) ?>
</div>

<div class="block">
  <ul class="inline">
    <li><?php include_component('product', 'sorting') ?></li>
    <li><?php include_component('userProductCompare', 'button', array('productCategory' => $productCategory)) ?></li>
  </ul>
  <?php include_component('product', 'pagination', array('productPager' => $productPager)) ?>
</div>
<div class="block">
  <?php include_component('product', 'pager', array('productPager' => $productPager)) ?>
</div>
<div class="block">
  <?php include_component('product', 'pagination', array('productPager' => $productPager)) ?>
</div>
