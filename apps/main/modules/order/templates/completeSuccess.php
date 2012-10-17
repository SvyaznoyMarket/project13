<?php use_helper('Date') ?>

<?php slot('complete_order_id', $order['number']) ?>
<?php slot('complete_order_sum', $order['sum']) ?>

<?php slot('title', 'Ваш заказ принят, спасибо за покупку!') ?>
<?php //myDebug::dump($order) ?>
<!-- Basket -->

<?php if (false): ?>
<div class="mSR fr">
  <a href="<?php echo url_for('default_show', array('page' => 'new_year_information_letter_from_rumyancev')) ?>">Обращение<br>
    генерального<br> директора</a>
</div>
<?php endif ?>
<div class="fl width874 font16 pb20">
  <strong>Номер вашего заказа: <?php echo $order->number ?></strong><br/><br/>
  Дата заказа: <?php echo format_date($order->created_at, 'D') ?><br/>
  Сумма заказа: <?php include_partial('default/sum', array('sum' => $order->sum,)) ?> <span
  class="rubl">p</span><br/><br/>
  <?php if (isset($result)): ?>
  <strong><?php echo $result['stage']['name'] ?>:</strong> <?php echo $result['message'] ?><br/>
  <?php endif ?>
  В ближайшее время мы вам перезвоним :)<br />
  Специалист нашего Контакт-сENTER уточнит, где и когда будет удобно получить заказ.
</div>

<!--div class="fr width250 pb20 form"><label for="radio-1">Я хочу получать СМС уведомления об изменении статуса заказа</label><input id="radio-1" name="radio-1" type="radio" value="radio-1" /></div-->
<div class="line pb20"></div>
<!--input type="button" class="button bigbutton fl" value="Посмотреть мои заказы" /-->

<?php if ($sf_user->isAuthenticated()): ?>
<form method="get" action="<?php echo url_for('user_orders') ?>">
  <input type="submit" class="button bigbutton fl" value="Посмотреть мои заказы"/>
</form>
<?php else: ?>
<form id="basic_register-form" method="post" action="<?php echo url_for('user_basicRegister') ?>">
  <div class="form-content">
    <?php echo $form ?>
  </div>
  <div class="pt10">
    <input type="submit" class="button bigbutton fl" value="Запомнить меня"/>
  </div>
</form>
<?php endif ?>

<form method="get" action="<?php echo url_for('homepage') ?>">
  <input type="submit" class="button bigbutton fr" value="Продолжить покупки"/>
</form>

<!-- /Basket -->
<?php if ($order['number']): ?>
<script type="text/javascript">
  _gaq.push(['_addTrans',
    '<?php echo $order['number'] ?>', // Номер заказа
    '<?php echo $order->Shop ?>', // Название магазина (Необязательно)
    '<?php echo str_replace(',', '.', $order['sum']) ?>', // Полная сумма заказа (дроби через точку)
    '', // налог
    '<?php echo $order->getDeliveryPrice() ?>', // Стоимость доставки (дроби через точку)
    '<?php echo $order->getCityName() ?>', // Город доставки (Необязательно)
    '<?php echo $order->getAreaName() ?>', // Область (необязательно)
    '<?php echo $order->getCountryName() ?>'             // Страна (нобязательно)
  ]);
  var yaParams = {
    order_id:'<?php echo $order['number'] ?>',
    order_price: <?php echo str_replace(',', '.', $order['sum']) ?>,
    currency:'RUR',
    exchange_rate:1,
    goods:[
      <?php foreach ($order->ProductRelation as $product): ?>
        {
          id:'<?php echo $product->Product['article'] ?>',
          name:'<?php echo $product->Product['name'] ?>',
          price: <?php echo str_replace(',', '.', $product['price']) ?>,
          quantity: <?php echo $product['quantity'] ?>
        },
        <?php endforeach ?>
      <?php foreach ($order->ServiceRelation as $service): ?>
        {
          id:'<?php echo $service->Service['token'] ?>',
          name: <?php echo $service->Service['name'] ?>,
          price: <?php echo str_replace(',', '.', $service['price']) ?>,
          quantity: <?php echo $service['quantity'] ?>
        },
        <?php endforeach ?>
    ]
  };
    <?php foreach ($order->ProductRelation as $product): ?>
    <?php $category = $product->Product->getMainCategory() ?>
    <?php if (!empty($category)) $rootCategory = $category->getRootCategory() ?>
  _gaq.push(['_addItem',
    '<?php echo $order['number'] ?>', // Номер заказа
    '<?php echo $product->Product['article'] ?>', // Артикул
    '<?php echo $product->Product['name'] ?>', // Название товара
    '<?php if (!empty($category)) {
      echo ($category->id != $rootCategory->id) ? ($rootCategory . ' - ' . $category) : $category;
    } ?>', // Категория товара
    '<?php echo str_replace(',', '.', $product['price']) ?>', // Стоимость 1 единицы товара
    '<?php echo $product['quantity'] ?>'               // Количество товара
  ]);
    <?php endforeach ?>
    <?php foreach ($order->ServiceRelation as $service):
    $catName = 'Услуга F1';
    $cat = $service->Service->getCatalogParent();
    if ($cat && isset($cat['name'])) {
      $catName .= ' - ' . $cat['name'];
    }
    ?>
  _gaq.push(['_addItem',
    '<?php echo $order['number'] ?>', // Номер заказа
    '<?php echo $service->Service['token'] ?>', // id
    '<?php echo $service->Service['name'] ?>', // Название услуги
    '<?php echo $catName ?>', // Категория товара
    '<?php echo str_replace(',', '.', $service['price']) ?>', // Стоимость 1 единицы товара
    '<?php echo $service['quantity'] ?>'               // Количество услуг
  ]);
    <?php endforeach ?>
  _gaq.push(['_trackTrans']);

</script>

<!--Трэкер "Покупка"-->
<script>document.write('<img src="http://mixmarket.biz/tr.plx?e=3779408&r=' + escape(document.referrer) + '&t=' + (new Date()).getTime() + '" width="1" height="1"/>');</script>
<!--Трэкер "Покупка"-->
<?php
endif ?>

<!--  AdRiver code START. Type:audit Site: Enter SZ: order PZ: 0 BN: 0 -->
<script language="javascript" type="text/javascript"><!--
var RndNum4NoCash = Math.round(Math.random() * 1000000000);
var ar_Tail='unknown'; if (document.referrer) ar_Tail = escape(document.referrer);
document.write('<img src="http://ad.adriver.ru/cgi-bin/rle.cgi?' + 'sid=182615&sz=order&bt=55&pz=0&custom=150=<?php echo $order->number ?>&rnd=' + RndNum4NoCash + '&tail256=' + ar_Tail + '" border=0 width=1 height=1>')
//--></script>
<noscript><img src="http://ad.adriver.ru/cgi-bin/rle.cgi?sid=182615&sz=order&bt=55&pz=0&rnd=1086697038&custom=150=<?php echo $order->number ?>" border=0 width=1 height=1></noscript>
<!--  AdRiver code END  -->

<?php slot('seo_counters_advance') ?>
<?php include_component('order', 'seo_counters_advance', array('step' => 4, 'order' => $order)) ?>
<?php end_slot() ?>
