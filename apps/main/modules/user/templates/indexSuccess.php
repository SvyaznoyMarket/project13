<div class="block">
  <?php include_component('user', 'menu') ?>
</div>

<div class="block">
  <ul>
    <li><?php echo link_to('Корзина товаров', 'cart') ?></li>
    <li><?php echo link_to('История просмотра товаров', 'userProductHistory') ?></li>
    <li><?php echo link_to('Отложенные товары', 'userDelayedProduct') ?></li>
    <li><?php echo link_to('Сравнение товаров', 'userProductCompare') ?></li>
    <li><?php echo link_to('Пароль', 'user_changePassword') ?></li>
    <li><?php echo link_to('Выход', 'user_signout') ?></li>
  </ul>
</div>