<?php if ($isCredit) { ?>
    <?php include_partial('order_/header', array('title' => 'Покупка в кредит')) ?>
<?php } else { ?>
    <?php include_partial('order_/header', array('title' => 'Ваш заказ принят, спасибо!')) ?>
<?php } ?>

<?php foreach ($orders as $order): ?>
  <p class="font19">Номер заказа: <?php echo $order['number'] ?></p>
  <?php if (!empty($order['added'])): ?>
    <p class="font16">Дата заказа: <?php echo format_date($order['added'], 'd', 'ru') ?></p>
  <?php endif ?>
  <p class="font16">Сумма заказа: <?php echo $order['sum'] ?> <span class="rubl">p</span></p>
  <p class="font16">Сумма для оплаты: <span id="paymentWithCard"><?php echo $order['sum'] ?></span> <span class="rubl">p</span></p>
  <div class="line pb15"></div>
<?php endforeach ?>

<? if (!$sf_user->getRegion('region')->getHasTransportCompany()): ?>
<div class="orderFinal__certificate">
	<script type="text/html" id="processBlock">
		<div class="process">
			<div class="img <%=typeNum%>"></div>
			<p><%=text%></p>
			<div class="clear"></div>
		</div>
	</script>
	<h2>Заполните информацию с подарочной карты</h2>
	<form>
		<input class="bBuyingLine__eText cardNumber" placeholder="Номер" />
		<input class="bBuyingLine__eText cardPin" placeholder="ПИН" />
		<input id="sendCard" class="button bigbutton mDisabled" type="submit" value="Активировать" />
	</form>
  <div id="processing"></div>
	<p class="certifText">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
</div>
<div class="line pb15"></div>
<? endif ?>

<?php if (!$isCredit) { ?>
    <div class="mt32">
      В ближайшее время мы вам перезвоним :)
      <br />Специалист нашего Контакт-сENTER уточнит, где и когда будет удобно получить заказ.
    </div>
<?php } ?>

<?php if ($paymentForm) { ?>
  <p>Через <span class="timer">5</span> сек. мы автоматически перенаправим Вас на страницу оплаты, если этого не произойдет, пожалуйста, нажмите на кнопку "Оплатить заказ".</p>
  <div class="pt10">

    <form class="form" method="post" action="<?php echo $paymentProvider->getFormUrl() ?>">
      <?php echo $paymentForm ?>
      <input id="pay-button" type="submit" class="button bigbutton" value="Оплатить заказ" />
    </form>

  </div>
<?php } else{ ?>
  <div class="mt32" style="text-align: center">
    <a class='bBigOrangeButton' href="<?php echo url_for('homepage') ?>">Продолжить покупки</a>
  </div>
<?php } ?>

<?php if ($isCredit) { ?>
    <div id='credit-widget' data-value='<?php echo $jsCreditData; ?>' ></div>
<?php } ?>


<?php //include_partial('order_/footer') ?>

<?php slot('seo_counters_advance') ?>
  <?php include_component('order', 'seo_counters_advance', array('step' => 2)) ?>
<?php end_slot() ?>


<?php slot('analytics_report') ?>
	<?php foreach ($orders as $order): ?>
		<div id="adblenderCost" data-vars="<?php echo $order['sum'] ?>" class="jsanalytics"></div>
	<?php endforeach ?>

<?php if ('live' == sfConfig::get('sf_environment')): ?>    
  <script type="text/javascript">
  <?php foreach ($orders as $order): ?>

  if (typeof _gaq != 'undefined') _gaq.push(['_addTrans',
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
      if (typeof _gaq != 'undefined') _gaq.push(['_addItem', <?php echo $gaItem ?>]);
    <?php endforeach ?>

  if (typeof _gaq != 'undefined') _gaq.push(['_trackTrans']);

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
  <?php endif ?>

  <?php //include_component('order_','seo_admitad', array('orders' => $orders)) ?>

    <div id="mixmarket" class="jsanalytics"></div>
    <div id="gooReMaSuccess" class="jsanalytics"></div>
    <div id="marketgidOrderSuccess" class="jsanalytics"></div>

    <?php foreach ($orders as $i => $order): 
      $jsonOrdr = array (
          'order_article' => implode(',', array_map(function($i) { return $i['id']; }, $order['product'])),
          'order_id' => $order['number'],
          'order_total' => $order['sum'],
          'product_quantity' => implode(',', array_map(function($i) { return $i['quantity']; }, $order['product'])),
      );  
      $jsonMyThings = array (
        'order_id' => $order['number'],
        'order_total' => $order['sum'],
        'products' => array()
      );

      foreach($order['product'] as $orderProduct){
          $jsonMyThings['products'][] = array(
            'ProductID' => $orderProduct['id'],
            'price' => $orderProduct['price'],
            'qty' => $orderProduct['quantity']
        );
      }
      ?>
      <div id="heiasComplete" data-vars='<?php echo json_encode( $jsonOrdr ) ?>' class="jsanalytics"></div>

      <div id="adriverOrder" data-vars='<?php echo json_encode( $jsonOrdr ) ?>' class="jsanalytics"></div>

      <div id="myThingsFin" data-vars='<?php echo json_encode( $jsonMyThings ) ?>' class="jsanalytics"></div>
  <!-- Efficient Frontiers -->
      <img src='http://pixel.everesttech.net/3252/t?ev_Orders=1&amp;ev_Revenue=<?php echo $order['sum'] ?>&amp;ev_Quickorders=0&amp;ev_Quickrevenue=0&amp;ev_transid=<?php echo $order['number'] ?>' width='1' height='1'/>

    <?php endforeach ?>




<?php end_slot() ?>


<?php if (sfConfig::get('app_smartengine_push')): ?>
  <?php $productIds = array(); foreach ($orders as $order) $productIds = array_merge($productIds, array_map(function($i) use ($productIds) { return $i['product_id']; }, $order['product'])) ?>

  <?php if (count($productIds)): ?>
    <div id="product_buy-container" data-url="<?php echo url_for('smartengine_buy', array('product' => implode('-', $productIds))) ?>"></div>
  <?php endif ?>
<?php endif ?>
