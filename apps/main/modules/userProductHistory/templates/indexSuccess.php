<div class="block">
  <?php include_component('user', 'menu') ?>
</div>

<h1>История просмотра товаров</h1>

<div class="block">
  <?php if (count($productList) > 0): ?>
    <?php include_component('userProductHistory', 'show') ?>
    <p><?php echo link_to('очистить', 'userProductHistory_clear') ?></p>

  <?php else: ?>
    <p>нет товаров для просмотра</p>

  <?php endif ?>
</div>
