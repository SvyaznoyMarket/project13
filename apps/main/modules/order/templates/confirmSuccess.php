<h1>Подтверждение заказа</h1>

<div class="block">
  <?php include_component('order', 'navigation', array('order' => $order)) ?>
</div>

<div class="block">
  <?php include_component('order', 'show', array('order' => $order, 'view' => 'base')) ?>
</div>

<form action="<?php echo url_for('order_confirm') ?>" method="post">
  <input type="submit" value="Подтвердить" />
</form>