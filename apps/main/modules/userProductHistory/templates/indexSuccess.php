<div class="block">
  <?php include_component('user', 'menu') ?>
</div>

<h1>История просмотра товаров</h1>

<div class="block">
  <?php include_component('userProductHistory', 'list') ?>
</div>

<p><?php echo link_to('Очистить историю', 'userProductHistory_clear') ?></p>