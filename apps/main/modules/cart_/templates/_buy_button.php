<?php
/**
 * @var $item ProductEntity
 * @var $disable
 * @var $quantity
 * @var $view
 * @var $text
 */
if(isset($disable)){
  $disable = (bool)$disable;
}else{
  $disable = $item->getIsBuyable() == false;
}
$quantity = isset($quantity) ? $quantity : 1;
$view = (isset($view) && $view === 'add') ? 'add' : 'default';
?>
<?php if( $item->isInCart() ): ?>
  <?php if($view == 'add'): ?>
    <a href="<?php echo url_for('order_new') ?>" class='link1 bOrangeButton active'><i></i><span><?php echo isset($text)? $text : 'В корзине'?></span></a>
  <?php else:?>
    <?php echo link_to(isset($text)? $text : '&nbsp;', '@order_new', array('class' => 'link1 cart cart-show'));?>
  <?php endif; ?>
<?php else:?>
  <?php if($view == 'add'): ?>
  <a href="<?php echo url_for('cart_add', array('product' => $item->getId(), 'quantity' => $quantity)) ?>"
     class='link1 bOrangeButton<?php if ($disable) echo ' disable' ?>'><i></i><span><?php echo isset($text)? $text : 'Положить в корзину'?></span></a>
  <?php else:?>
    <?php if ($disable): ?>
      <a href="#" class="link1 event-click cart cart-add disabled"><?php echo isset($text)? $text : '&nbsp;' ?></a>
    <?php else: ?>
      <?php
      $link = link_to(
          isset($text)? $text : '&nbsp;',
          'cart_add',
          array('product' => $item->getId(), 'quantity' => $quantity),
          array('class' => 'link1 event-click cart cart-add', 'data-event' => 'content.update')
      );
      echo $link; ?>
    <?php endif ?>
  <?php endif; ?>
<?php endif; ?>
