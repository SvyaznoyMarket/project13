<?php echo $product->Creator ?>
<?php include_component('product', 'property_grouped', array('product' => $product)) ?>
<?php include_component('product', 'product_group', array('product' => $product, )) ?>
<ul class="inline">
  <li><?php include_component('cart', 'buy_button', array('product' => $product, 'quantity' => 1)) ?></li>
  <li><?php include_component('userDelayedProduct', 'add_button', array('product' => $product)) ?></li>
  <li><?php include_component('userProductCompare', 'button', array('product' => $product)) ?></li>
</ul>

<div class="inline">
  <?php include_component('userProductRating', 'show', array('product' => $product)) ?>
</div>

<div class="inline">
  <?php include_component('userTag', 'product_link', array('product' => $product)) ?>
</div>