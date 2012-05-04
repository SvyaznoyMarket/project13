<?php $rememberMe = !$sf_user->isAuthenticated() && $form->isValid() ?>

<?php use_helper('Date') ?>

<div style="width: 900px;">

  <div class="bFormSave">
    <h2>Номер вашего заказа: <?php echo $order['number'] ?></h2>

    <p>Дата заказа: <?php echo format_date($order['created_at'], 'dd.MM.yyyy') ?>.<br>Сумма
      заказа: <?php echo number_format($order['sum'], 0, ',', ' ') ?> <span class="rubl">p</span></p>
    <span>В ближайшее время мы свяжемся с вами для уточнения параметров заказа.</span>
  </div>

  <?php if ($order['number']): ?>
  <script type="text/javascript">
    function runAnalitics() {
      if (typeof(_gaq) !== 'undefined') {
        _gaq.push(['_addTrans',
          '<?php echo $order['number'] . '_F' ?>', // Номер заказа
          '<?php echo $order->Shop ?>', // Название магазина (Необязательно)
          '<?php echo str_replace(',', '.', $order['sum']) ?>', // Полная сумма заказа (дроби через точку)
          '0', // Стоимость доставки (дроби через точку)
          '<?php echo $order->getCityName() ?>', // Город доставки (Необязательно)
          '<?php echo $order->getAreaName() ?>', // Область (необязательно)
          '<?php echo $order->getCountryName() ?>'             // Страна (нобязательно)
        ]);
        _gaq.push(['_trackEvent', 'QuickOrder', 'Success']);
        <?php foreach ($order->ProductRelation as $product): ?>
          <?php $productTagCategory = $product->Product->getMainCategory() ?>
          <?php if (!empty($productTagCategory)) $rootCategory = $productTagCategory->getRootCategory() ?>
          _gaq.push(['_addItem',
            '<?php echo $order['number'] . '_F' ?>', // Номер заказа
            '<?php echo $product->Product['article'] ?>', // Артикул
            '<?php echo $product->Product['name'] ?>', // Название товара
            '<?php if (!empty($productTagCategory)) {
              echo ($productTagCategory->id != $rootCategory->id) ? ($rootCategory . ' - ' . $productTagCategory) : $productTagCategory;
            } ?>', // Категория товара
            '<?php echo str_replace(',', '.', $product['price']) ?>', // Стоимость 1 единицы товара
            '<?php echo str_replace(',', '.', $product['quantity']) ?>'               // Количество товара
          ]);
          <?php endforeach ?>
        _gaq.push(['_trackTrans']);
      }

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
        ]
      };
      if (typeof(yaCounter10503055) !== 'undefined')  yaCounter10503055.reachGoal('QORDER', yaParams);

      if (typeof(window.adBelnder) != 'undefined') window.adBelnder.addOrder(<?php echo str_replace(',', '.', $order['sum']) ?>);
    }
  </script>

  <?php endif ?>

  <div class="line"></div>

  <?php if ($rememberMe): ?>
  <p class="bFormSave__eBtm">Нажмите кнопку «Запомнить мои данные» &mdash; при следующих покупках вам не придется заново
    указывать свои контакты и данные для доставки. Кстати, вы еще и сможете отслеживать статус заказа!</p>
  <?php endif ?>

  <div class="bFormB2">

    <?php if ($rememberMe): ?>
    <div class="fl">
      <form id="basic_register-form" method="post" action="<?php echo url_for('user_basicRegister') ?>">
        <div class="form-content">
          <?php echo $form ?>
        </div>
        <span><input type="submit" value="Запомнить мои данные" id="bigbutton" class="button bigbutton"> </span>
      </form>
    </div>
    <?php endif ?>

    <div class="fr">
      <a href="<?php echo url_for('@homepage') ?>" onclick="$('#order1click-container').trigger('close'); return false">Продолжить
        покупки</a> <span>&gt;</span>
    </div>
  </div>

</div>

<!--  AdRiver code START. Type:audit Site: Enter SZ: order PZ: 0 BN: 0 -->
<script language="javascript" type="text/javascript"><!--
var RndNum4NoCash = Math.round(Math.random() * 1000000000);
var ar_Tail='unknown'; if (document.referrer) ar_Tail = escape(document.referrer);
document.write('<img src="http://ad.adriver.ru/cgi-bin/rle.cgi?' + 'sid=182615&sz=order&bt=55&pz=0&custom=150=<?php echo $order['number'] ?>&rnd=' + RndNum4NoCash + '&tail256=' + ar_Tail + '" border=0 width=1 height=1>')
//--></script>
<noscript><img src="http://ad.adriver.ru/cgi-bin/rle.cgi?sid=182615&sz=order&bt=55&pz=0&rnd=1086697038&custom=150=<?php echo $order['number'] ?>" border=0 width=1 height=1></noscript>
<!--  AdRiver code END  -->