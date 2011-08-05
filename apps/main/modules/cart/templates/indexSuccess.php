<div class="block wide">

  <?php include_component('user', 'menu') ?>

  <h1>Корзина товаров</h1>

  <div class="block">
    <?php if ($cart->count() > 0): ?>
      <?php include_component('cart', 'list') ?>
      <?php echo link_to('очистить', '@cart_clear', array('class' => 'cart cart-clear')) ?>

    <?php else: ?>
      <p>в корзине нет товаров</p>

    <?php endif ?>
  </div>

</div>