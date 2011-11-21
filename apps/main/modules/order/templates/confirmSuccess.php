<?php slot('title', 'Оформление заказа - Шаг 3') ?>

<?php slot('navigation') ?>
  <?php include_component('order', 'navigation', array('order' => $order)) ?>
<?php end_slot() ?>

<?php slot('step') ?>
<ul class="steplist steplist3">
  <li><a href="<?php echo url_for('order_login') ?>"><span>Шаг 1</span>Данные<br />покупателя</a></li>
  <li><a href="<?php echo url_for('order_new') ?>"><span>Шаг 2</span>Способ доставки<br />и оплаты</a></li>
  <li class="last"><a href="<?php echo url_for('order_confirm') ?>"><span>Шаг 3</span>Подтверждение<br />заказа</a></li>
</ul>
<?php end_slot() ?>

<?php include_component('order', 'show', array('order' => $order, 'view' => 'base')) ?>

<div class="line pb20"></div>

<?php if (isset($paymentForm)): ?>
  <div class="pl235">
    <form class="form" action="<?php echo $paymentForm->getUrl() ?>" method="post">
      <div class="pb10 pl20"><?php include_partial('order/field_agree') ?></div>
      <div class="pb10">Ваш заказ <?php echo $order->number ?>. Нажмите "Оплатить заказ" и Вы перейдете на страницу оплаты пластиковой картой.</div>
      <?php echo $paymentForm ?>
      <input id="pay-button" type="submit" class="button bigbutton mDisabled" value="Оплатить заказ" />
    </form>
  </div>
<?php else: ?>
  <div class="pl235">
    <form class="form" action="<?php echo url_for('order_confirm') ?>" method="post">
      <div class="pb10 pl20"><?php include_partial('order/field_agree') ?></div>
      <div class="pb10">Нажмите "Подтвердить заказ" и Ваш заказ будет принят к исполнению.</div>
      <input id="confirm-button" type="submit" class="button bigbutton mDisabled" value="Подтвердить заказ" />
    </form>
  </div>
<?php endif ?>

<!-- /Basket -->
<!--form action="<?php echo url_for('order_confirm') ?>" method="post">
<input type="submit" value="Подтвердить" />
</form-->