<?php include_partial('order_/header', array('title' => 'Ваш заказ принят, спасибо!')) ?>

<?php foreach ($orders as $order): ?>
  <p class="font19">Номер заказа: <?php echo $order['number'] ?></p>
  <p class="font16">Дата заказа: <?php echo format_date($order['added'], 'd', 'ru') ?></p>
  <p class="font16">Сумма заказа: <?php echo $order['sum'] ?> <span class="rubl">p</span></p>
  <div class="line pb15"></div>
<?php endforeach ?>

<div class="mt32">
  В ближайшее время мы вам перезвоним :)
  <br />Специалист нашего Контакт-cENTERа уточнит, где и когда будет удобно получить заказ.
</div>

<div class="mt32" style="text-align: center">
  <a class='bBigOrangeButton' href="<?php echo url_for('homepage') ?>">Продолжить покупки</a>
</div>

<?php include_partial('order_/footer') ?>

<?php if (false): ?>
  <?php slot('seo_counters_advance') ?>
    <?php include_component('order', 'seo_counters_advance', array('step' => 2)) ?>
  <?php end_slot() ?>
<?php endif ?>