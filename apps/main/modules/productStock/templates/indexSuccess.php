<div class="block">
  <?php include_component('productCard', 'navigation', array('product' => $product)) ?>
</div>

<div class="block">
  <?php include_partial('product/name', array('product' => $product)) ?>
  <?php include_component('productStock', 'show', array('product' => $product)) ?>
</div>