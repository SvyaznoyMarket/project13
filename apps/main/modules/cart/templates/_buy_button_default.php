<?php if ($disable): ?>
<a href="#" class="link1 event-click cart cart-add disabled">&nbsp;</a>
<?php else: ?>
<?php echo link_to('&nbsp;', 'cart_add', array('product' => $product->token, 'quantity' => $quantity), array('class' => 'link1 event-click cart cart-add', 'data-event' => 'content.update')) ?>
<?php endif ?>
