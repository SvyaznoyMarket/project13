<?php use_helper('Date') ?>

<?php slot('complete_order_id', $order['number']) ?>
<?php slot('complete_order_sum', $order['sum']) ?>

<?php slot('title', 'Ваш заказ принят,<br />спасибо за покупку!') ?>
<?php //myDebug::dump($order) ?>
<!-- Basket -->
<?php if (false): ?>
		<div class="mSR fr">
			<a href="<?php echo url_for('default_show', array('page' => 'new_year_information_letter_from_rumyancev')) ?>">Обращение<br> генерального<br> директора</a>
		</div>
<?php endif ?>
<div class="fl width874 font16 pb20">
  <strong>Номер вашего заказа: <?php echo $order->number ?></strong><br /><br />
  Дата заказа: <?php echo format_date($order->created_at, 'D') ?><br />
  Сумма заказа: <?php include_partial('default/sum', array('sum' => $order->sum,)) ?> <span class="rubl">p</span><br /><br />
  <!--С вами свяжется оператор для получения и уточнения параметров заказа.-->
  <?php if (isset($result)): ?>
    <strong><?php echo $result['stage']['name'] ?>:</strong> <?php echo $result['message'] ?><br />
  <?php endif ?>
  Чтобы подтвердить заказ, в течение 15 минут с вами свяжется специалист нашего Контакт cENTER.
</div>

<!--div class="fr width250 pb20 form"><label for="radio-1">Я хочу получать СМС уведомления об изменении статуса заказа</label><input id="radio-1" name="radio-1" type="radio" value="radio-1" /></div-->
<div class="line pb20"></div>
<!--input type="button" class="button bigbutton fl" value="Посмотреть мои заказы" /-->

<?php if ($sf_user->isAuthenticated()): ?>
<form method="get" action="<?php echo url_for('user_orders') ?>">
  <input type="submit" class="button bigbutton fl" value="Посмотреть мои заказы" />
</form>
<?php else: ?>
<form id="basic_register-form" method="post" action="<?php echo url_for('user_basicRegister') ?>">
  <div class="form-content">
    <?php echo $form ?>
  </div>
  <div class="pt10">
    <input type="submit" class="button bigbutton fl" value="Запомнить меня" />
  </div>
</form>
<?php endif ?>

<form method="get" action="<?php echo url_for('homepage') ?>">
  <input type="submit" class="button bigbutton fr" value="Продолжить покупки" />
</form>

<!-- /Basket -->
<?php if ($order['number']): ?>
  <script type="text/javascript">
       _gaq.push(['_addTrans',
           '<?php echo $order['number'] ?>',           // Номер заказа
           '<?php echo $order->Shop ?>',  // Название магазина (Необязательно)
           '<?php echo str_replace(',', '.', $order['sum']) ?>',          // Полная сумма заказа (дроби через точку)
           '0',              // Стоимость доставки (дроби через точку)
           '<?php echo $order->getCityName() ?>',       // Город доставки (Необязательно)
           '<?php echo $order->getAreaName() ?>',     // Область (необязательно)
           '<?php echo $order->getCountryName() ?>'             // Страна (нобязательно)
       ]);
      var yaParams = {
        'order_id': '<?php echo $order['number'] ?>',
        'order_price': '<?php echo str_replace(',', '.', $order['sum']) ?>',
        'currency': 'RUR',
        'exchange_rate': 1,
        'goods': []
      };
  <?php foreach ($order->ProductRelation as $product): ?>
             _gaq.push(['_addItem',
                  '<?php echo $order['number'] ?>',           // Номер заказа
                  '<?php echo $product->Product['article'] ?>',           // Артикул
                  '<?php echo $product->Product['name'] ?>',        // Название товара
                  '<?php echo $product->Product->getMainCategory() ?>',   // Категория товара
                  '<?php echo str_replace(',', '.', $product['price']) ?>',          // Стоимость 1 единицы товара
                  '<?php echo str_replace(',', '.', $product['quantity']) ?>'               // Количество товара
              ]);
             yaParams.goods.push({
               'id': '<?php echo $product->Product['article'] ?>',
               'name': '<?php echo $product->Product['name'] ?>',
               'price': '<?php echo str_replace(',', '.', $product['price']) ?>'
             });
  <?php endforeach ?>
           _gaq.push(['_trackTrans']);

  </script>

    <!--Трэкер "Покупка"-->
    <script>document.write('<img src="http://mixmarket.biz/tr.plx?e=3779408&r='+escape(document.referrer)+'&t='+(new Date()).getTime()+'" width="1" height="1"/>');</script>
    <!--Трэкер "Покупка"-->
<?php
endif ?>