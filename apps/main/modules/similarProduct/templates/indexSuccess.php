<div class="block">
  <?php include_component('productCard_', 'navigation', array('product' => $item)) ?>
</div>

<div class="block">
  <?php include_partial('product/name', array('product' => $item)) ?>
</div>

<div class="block">
  <?php include_component('similarProduct', 'list', array('product' => $item)) ?>
</div>