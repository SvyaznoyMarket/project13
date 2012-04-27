<?php include_partial('order_/header', array('title' => 'Ваш заказ принят, спасибо!')) ?>

<?php foreach ($orders as $order): ?>
  <p class="font19">Номер заказа: <?php echo $order['number'] ?></p>
  <?php if (!empty($order['added'])): ?>
    <p class="font16">Дата заказа: <?php echo format_date($order['added'], 'd', 'ru') ?></p>
  <?php endif ?>
  <p class="font16">Сумма заказа: <?php echo $order['sum'] ?> <span class="rubl">p</span></p>
  <div class="line pb15"></div>
<?php endforeach ?>

<div class="mt32">
  В ближайшее время мы вам перезвоним :)
  <br />Специалист нашего Контакт-cENTERа уточнит, где и когда будет удобно получить заказ.
</div>

<?php if ($paymentForm): ?>
  <p>Через <span class="timer">5</span> сек. мы автоматически перенаправим Вас на страницу оплаты, если этого не произойдет, пожалуйста, нажмите на кнопку "Оплатить заказ".</p>
  <div class="pt10">
    <form class="form" action="<?php echo $paymentForm->getUrl() ?>" method="post">
      <?php echo $paymentForm ?>
      <input id="pay-button" type="submit" class="button bigbutton" value="Оплатить заказ" />
    </form>
  </div>

<?php else: ?>
  <div class="mt32" style="text-align: center">
    <a class='bBigOrangeButton' href="<?php echo url_for('homepage') ?>">Продолжить покупки</a>
  </div>
<?php endif ?>

<?php include_partial('order_/footer') ?>

<?php slot('seo_counters_advance') ?>
  <?php include_component('order', 'seo_counters_advance', array('step' => 2)) ?>
<?php end_slot() ?>

<?php slot('adhands_report') ?>
  <script type="text/javascript" src="http://sedu.adhands.ru/js/counter.js"></script>
  <script type="text/javascript">
    <?php foreach ($orders as $order): ?>
    var report = new adhandsReport('http://sedu.adhands.ru/site/');
    report.id('1053');
    report.data('am','<?php echo $order['sum'] ?>');
    report.data('ordid','<?php echo $order['number'] ?>');
    report.send();
    <?php endforeach ?>
  </script>
  <noscript>
    <img width="1" height="1" src="http://sedu.adhands.ru/site/?static=on&clid=1053&rnd=1234567890123"
         style="display:none;">
  </noscript>
  <!-- /AdHands -->
  <script type="text/javascript">
    (function () {
      <?php foreach ($orders as $order): ?>
      document.write('<script type="text/javascript" src="' + ('https:' == document.location.protocol ? 'https://' : 'http://') + 'bn.adblender.ru/pixel.js?cost=' + escape(<?php echo $order['sum'] ?>) + '&r=' + Math.random() + '" ></sc' + 'ript>');
      <?php endforeach ?>
    })();
  </script>
<?php end_slot() ?>


<!--Трэкер "Покупка"-->
<script>document.write('<img src="http://mixmarket.biz/tr.plx?e=3779408&r='+escape(document.referrer)+'&t='+(new Date()).getTime()+'" width="1" height="1"/>');</script>
<!--Трэкер "Покупка"-->