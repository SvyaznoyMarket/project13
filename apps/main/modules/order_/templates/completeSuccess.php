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

<?php if ($paymentForm) { ?>
  <p>Через <span class="timer">5</span> сек. мы автоматически перенаправим Вас на страницу оплаты, если этого не произойдет, пожалуйста, нажмите на кнопку "Оплатить заказ".</p>
  <div class="pt10">
    <form class="form" action="<?php echo $paymentForm->getUrl() ?>" method="post">
      <?php echo $paymentForm ?>
      <input id="pay-button" type="submit" class="button bigbutton" value="Оплатить заказ" />
    </form>
  </div>
<?php } elseif ($creditForm) { ?>


<script type="text/javascript">dc_getCreditForTheProduct('4427', '<?php echo session_id();?>', 'orderProductToBuyOnCredit', { order_id : '<?php echo  $order['number']; ?>', nomenclature : '000000'});</script>


<?php } else { ?>
  <div class="mt32" style="text-align: center">
    <a class='bBigOrangeButton' href="<?php echo url_for('homepage') ?>">Продолжить покупки</a>
  </div>
<?php } ?>

<?php include_partial('order_/footer') ?>

<?php slot('seo_counters_advance') ?>
  <?php include_component('order', 'seo_counters_advance', array('step' => 2)) ?>
<?php end_slot() ?>


<?php slot('analytics_report') ?>
  <script type="text/javascript">
    (function () {
    <?php foreach ($orders as $order): ?>
      document.write('<script type="text/javascript" src="' + ('https:' == document.location.protocol ? 'https://' : 'http://') + 'bn.adblender.ru/pixel.js?cost=' + escape(<?php echo $order['sum'] ?>) + '&r=' + Math.random() + '" ></sc' + 'ript>');

    <?php endforeach ?>
    })();
  </script>

  <script type="text/javascript">
  <?php foreach ($orders as $order): ?>

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

    // _addItem: Номер заказа, Артикул, Название товара, Категория товара, Стоимость 1 единицы товара, Количество товара
    <?php foreach ($gaItems[$order['number']] as $gaItem): ?>
    _gaq.push(['_addItem', <?php echo $gaItem ?>]);
    <?php endforeach ?>

    _gaq.push(['_trackTrans']);

  <?php endforeach ?>
  </script>


  <script type="text/javascript">
    var yaParams =
    [
    <?php foreach ($orders as $i => $order): ?>
      {
        order_id:'<?php echo $order['number'] ?>',
        order_price: <?php echo str_replace(',', '.', $order['sum']) ?>,
        currency:'RUR',
        exchange_rate:1,
        goods:[
        <?php foreach ($gaItems[$order['number']] as $j => $gaItem): ?>

          {
            id:'<?php echo $gaItem->article ?>',
            name:'<?php echo $gaItem->name ?>',
            price: <?php echo $gaItem->price ?>,
            quantity: <?php echo $gaItem->quantity ?>

          }<?php echo ($j < (count($gaItems[$order['number']]) - 1)) ? ',' : '' ?>

          <?php endforeach ?>
        ]
      }<?php echo ($i < (count($orders) - 1)) ? ',' : '' ?>

    <?php endforeach ?>
    ]
  </script>

  <!--Трэкер "Покупка"-->
  <script>document.write('<img src="http://mixmarket.biz/tr.plx?e=3779408&r=' + escape(document.referrer) + '&t=' + (new Date()).getTime() + '" width="1" height="1"/>');</script>
  <!--Трэкер "Покупка"-->

  <?php include_component('order_','seo_admitad', array('orders' => $orders)) ?>


  <script language="JavaScript" type="text/javascript">
    HEIAS_T=Math.random(); HEIAS_T=HEIAS_T*10000000000000000000;
    <?php foreach ($orders as $i => $order): ?>

    var order_article='<?php echo implode(',', array_map(function($i) { return $i['id']; }, $order['product'])) ?>';
    var order_id='<?php echo $order['number'] ?>';
    var order_total='<?php echo $order['sum'] ?>';
    var product_quantity='<?php echo implode(',', array_map(function($i) { return $i['quantity']; }, $order['product'])) ?>';
    var HEIAS_SRC='https://ads.heias.com/x/heias.cpa/count.px.v2/?PX=HT|' + HEIAS_T + '|cus|12675|pb|1|order_article|' + order_article + '|product_quantity|' + product_quantity + '|order_id|' + order_id + '|order_total|' + order_total + '';
    document.write('<img width="1" height="1" src="' + HEIAS_SRC + '" />');

    <?php endforeach ?>
  </script>

<?php end_slot() ?>
