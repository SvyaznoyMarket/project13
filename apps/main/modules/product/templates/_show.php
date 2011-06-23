<?php echo $product->Creator ?>
<?php include_component('product', 'property_grouped', array('product' => $product)) ?>

<ul class="inline">
  <li><?php include_component('cart', 'buy_button', array('product' => $product, 'quantity' => 1)) ?></li>
  <li><?php include_component('userDelayedProduct', 'add_button', array('product' => $product)) ?></li>
  <li><?php include_component('userProductCompare', 'button', array('product' => $product)) ?></li>
</ul>

<?php include_component('userProductRating', 'show', array('product' => $product)) ?>