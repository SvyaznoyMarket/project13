<h1>Корзина товаров</h1>

<div class="block">
  <?php if ($cart->count() > 0): ?>
    <ul>
      <?php foreach ($cart->getProducts() as $product): ?>
        <li><?php echo $product['name'].' x'.$product['cart']['quantity'].' '.link_to('удалить', 'cart_delete', array('product' => $product['token'])) ?></li>
      <?php endforeach ?>
    </ul>
    <?php echo link_to('очистить', '@cart_clear') ?>

  <?php else: ?>
    <p>в корзине нет товаров</p>

  <?php endif ?>
</div>