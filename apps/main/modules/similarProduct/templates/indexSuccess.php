<div class="block">
  <?php include_component('productCard', 'navigation', array('product' => $product)) ?>
</div>

<div class="block">
  <?php include_partial('product/name', array('product' => $product)) ?>
</div>

<div class="block">
  <?php include_component('similarProduct', 'list', array('product' => $product)) ?>
</div>