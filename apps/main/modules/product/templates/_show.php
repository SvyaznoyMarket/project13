<?php echo $product->Creator ?>
<?php include_component('product', 'property_grouped', array('product' => $product)) ?>

<?php include_component('cart', 'buy_button', array('product' => $product, 'quantity' => 1)) ?><br />
<?php include_component('userDelayedProduct', 'add_button', array('product' => $product)) ?>