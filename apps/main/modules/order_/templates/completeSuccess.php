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



  <script type="text/javascript">
    _gaq.push(['_addTrans',
      '<?php echo $order['number'] ?>', // Номер заказа
      '<?php echo $order['shop']['name'] ?>', // Название магазина (Необязательно)
      '<?php echo str_replace(',', '.', $order['sum']) ?>', // Полная сумма заказа (дроби через точку)
      '', // налог
      '<?php echo 0 ?>', // Стоимость доставки (дроби через точку)
      '<?php echo $order['geo']['name'] ?>', // Город доставки (Необязательно)
      '', // Область (необязательно)
      '' // Страна (нобязательно)
    ]);
    var yaParams = {
      order_id:'<?php echo $order['number'] ?>',
      order_price: <?php echo str_replace(',', '.', $order['sum']) ?>,
      currency:'RUR',
      exchange_rate:1,
      goods:[
      <?php foreach ($order['product'] as $product): ?>
        {
          id:'<?php echo $product['article'] ?>',
          name:'<?php echo $product['name'] ?>',
          price: <?php echo str_replace(',', '.', $product['price']) ?>,
          quantity: <?php echo $product['quantity'] ?>
        },
        <?php endforeach ?>
      <?php foreach ($order['service'] as $service): ?>
        {
          id:'<?php echo $service['token'] ?>',
          name: '<?php echo $service['name'] ?>',
          price: <?php echo str_replace(',', '.', $service['price']) ?>,
          quantity: <?php echo $service['quantity'] ?>
        },
        <?php endforeach ?>
      ]
    };
    <?php foreach ($order['product'] as $product): ?>
    _gaq.push(['_addItem',
      '<?php echo $order['number'] ?>', // Номер заказа
      '<?php echo $product['article'] ?>', // Артикул
      '<?php echo $product['name'] ?>', // Название товара
      '<?php echo $product['category_name'] ?>', // Категория товара
      '<?php echo str_replace(',', '.', $product['price']) ?>', // Стоимость 1 единицы товара
      '<?php echo $product['quantity'] ?>' // Количество товара
    ]);
      <?php endforeach ?>
    <?php foreach ($order['service'] as $service):
      $catName = 'Услуга F1';
    ?>
    _gaq.push(['_addItem',
      '<?php echo $order['number'] ?>', // Номер заказа
      '<?php echo $service['token'] ?>', // id
      '<?php echo $service['name'] ?>', // Название услуги
      '<?php echo $catName ?>', // Категория товара
      '<?php echo str_replace(',', '.', $service['price']) ?>', // Стоимость 1 единицы товара
      '<?php echo $service['quantity'] ?>' // Количество услуг
    ]);
      <?php endforeach ?>
    _gaq.push(['_trackTrans']);

  </script>

  <!--Трэкер "Покупка"-->
  <script>document.write('<img src="http://mixmarket.biz/tr.plx?e=3779408&r=' + escape(document.referrer) + '&t=' + (new Date()).getTime() + '" width="1" height="1"/>');</script>
  <!--Трэкер "Покупка"-->
<?php end_slot() ?>


<!--Трэкер "Покупка"-->
<script>document.write('<img src="http://mixmarket.biz/tr.plx?e=3779408&r='+escape(document.referrer)+'&t='+(new Date()).getTime()+'" width="1" height="1"/>');</script>
<!--Трэкер "Покупка"-->