<?php $i = 0; foreach ($list as $item): $i++; ?>
  <?php include_component('product', 'show', array('view' => $view, 'product' => $item)) ?>

  <?php if (!($i % 3)): ?>
    <div class="line"></div>
  <?php endif ?>

<?php endforeach ?>