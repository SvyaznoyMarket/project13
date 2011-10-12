<?php slot('title', 'Оформление заказа - Шаг 3') ?>

<?php slot('navigation') ?>
  <?php include_component('order', 'navigation', array('order' => $order)) ?>
<?php end_slot() ?>

<?php slot('step') ?>
        <ul class="steplist steplist3">
            <li><a href=""><span>Шаг 1</span>Данные<br />покупателя</a></li>
            <li><a href=""><span>Шаг 2</span>Способ доставки<br />и оплаты</a></li>
            <li><a href=""><span>Шаг 3</span>Подтверждение<br />заказа</a></li>
        </ul>
<?php end_slot() ?>

  <?php include_component('order', 'show', array('order' => $order, 'view' => 'base')) ?>


        <div class="line pb20"></div>
        <div class="pl235"><div class="pb10">Нажмите "Подтвердить заказ" и Ваш заказ будет принят к исполнению.</div><form action="<?php echo url_for('order_confirm') ?>" method="post"><input type="submit" class="button bigbutton" value="Подтвердить заказ" /></form></div>
    <!-- /Basket -->
<!--form action="<?php echo url_for('order_confirm') ?>" method="post">
  <input type="submit" value="Подтвердить" />
</form-->