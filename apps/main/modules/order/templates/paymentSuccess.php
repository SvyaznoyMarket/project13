<?php slot('title', 'Оплата заказа') ?>

<?php slot('navigation') ?>
  <?php include_component('order', 'navigation', array('order' => $order)) ?>
<?php end_slot() ?>

<?php include_component('order', 'show', array('order' => $order, 'view' => 'base')) ?>

<div class="line pb20"></div>

<div class="pl235">
  <form class="form" action="<?php echo $paymentForm->getUrl() ?>" method="post">
    <div class="pb10">Ваш заказ <?php echo $order->number ?>. Нажмите "Оплатить заказ" и Вы перейдете на страницу оплаты пластиковой картой.</div>
    <?php echo $paymentForm ?>
    <input id="pay-button" type="submit" class="button bigbutton" value="Оплатить заказ" />
  </form>
</div>