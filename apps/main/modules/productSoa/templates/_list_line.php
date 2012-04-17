<div class="goodslist">
  <?php $i = 0; foreach ($list as $item): $i++; ?>
  <?php include_component('productSoa', 'show', array('view' => 'line', 'product' => $item)) ?>

  <?php if (!($i % 3)): ?>
    <div class="line"></div>
    <?php endif ?>

  <?php endforeach ?>
</div>