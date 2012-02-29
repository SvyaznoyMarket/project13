<?php if ($disable): ?>
  <a href="#" class="link1 event-click cart cart-add disabled"><?php echo isset($value[0]) ? $value[0] : '&nbsp;' ?></a>
<?php else:
    //print_r($product);
    ?>
  <?php echo link_to((isset($value[0]) ? $value[0] : '&nbsp;'), 'cart_add', array('product' => $productPath, 'quantity' => $quantity), array('class' => 'link1 event-click cart cart-add', 'data-event' => 'content.update')) ?>
<?php endif ?>
