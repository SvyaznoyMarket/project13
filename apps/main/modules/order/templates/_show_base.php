<div class="form">

  <div class="form-row">
    <label>Город</label>
    <div class="content"><?php echo $order->Region ?></div>
  </div>

  <div class="form-row">
    <label>Вы покупаете как</label>
    <div class="content"><?php echo ($order->is_legal ? 'юр. лицо' : 'частное лицо') ?></div>
  </div>

</div>


<?php include_component('cart', 'show') ?>

<p>Итого: <?php include_partial('default/sum', array('sum' => $item['sum'])) ?></p>