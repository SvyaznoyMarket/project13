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


<?php slot('analytics_report') ?>
	<?php foreach ($orders as $order): ?>
		<div id="adblenderCost" data-vars="<?php echo $order['sum'] ?>" class="jsanalytics"></div>
	<?php endforeach ?>
    
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

 
  <?php include_component('order_','seo_admitad', array('orders' => $orders)) ?>

    <div id="mixmarket" class="jsanalytics"></div>
    <?php foreach ($orders as $i => $order): 
      $jsonOrdr = array (
          'order_article' => implode(',', array_map(function($i) { return $i['id']; }, $order['product'])),
          'order_id' => $order['number'],
          'order_total' => $order['sum'],
          'product_quantity' => implode(',', array_map(function($i) { return $i['quantity']; }, $order['product'])),
      );  ?>
      
      <div id="heiasComplete" data-vars='<?php echo json_encode( $jsonOrdr ) ?>' class="jsanalytics"></div>

      <div id="adriverOrder" data-vars='<?php echo json_encode( $jsonOrdr ) ?>' class="jsanalytics"></div>

  <!-- Efficient Frontiers -->
      <img src='http://pixel.everesttech.net/3252/t?ev_Orders=1&amp;ev_Revenue=<?php echo $order['sum'] ?>&amp;ev_Quickorders=0&amp;ev_Quickrevenue=0&amp;ev_transid=<?php echo $order['number'] ?>' width='1' height='1'/>

    <?php endforeach ?>



<?php end_slot() ?>
