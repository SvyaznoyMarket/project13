<div class="block">
  <?php include_component('user', 'menu') ?>
</div>

<h1>Отложенные товары</h1>

<div class="block">
  <?php if (count($userDelayedProductList) > 0): ?>
    <?php include_component('userDelayedProduct', 'show', array('userDelayedProductList' => $userDelayedProductList)) ?>
    <?php echo link_to('очистить', '@userDelayedProduct_clear') ?>

  <?php else: ?>
    <p>нет отложенных товаров</p>

  <?php endif ?>
</div>
