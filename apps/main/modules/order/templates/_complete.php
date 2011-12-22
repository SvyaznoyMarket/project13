<?php $rememberMe = !$sf_user->isAuthenticated() && $form->isValid() ?>

<?php use_helper('Date') ?>

<div style="width: 900px;">

  <div class="bFormSave">
    <h2>Номер вашего заказа: <?php echo $order['number'] ?></h2>
    <p>Дата заказа: <?php echo format_date($order['created_at'], 'dd.MM.yyyy') ?>.<br>Сумма заказа: <?php echo number_format($order['sum'], 0, ',', ' ') ?> <span class="rubl">p</span></p>
    <span>В ближайшее время мы свяжемся с вами для уточнения параметров заказа.</span>
  </div>

<?php if ($order['number']): ?>
  <script type="text/javascript">
    function runAnalitics(){
       _gaq.push(['_addTrans',
           '<?php echo $order['number'].'_F' ?>',           // Номер заказа
           '<?php echo $order->Shop ?>',  // Название магазина (Необязательно)
           '<?php echo str_replace(',', '.', $order['sum']) ?>',          // Полная сумма заказа (дроби через точку)
           '0',              // Стоимость доставки (дроби через точку)
           '<?php echo $order->getCityName() ?>',       // Город доставки (Необязательно)
           '<?php echo $order->getAreaName() ?>',     // Область (необязательно)
           '<?php echo $order->getCountryName() ?>'             // Страна (нобязательно)
       ]);
  <?php foreach ($order->ProductRelation as $product): ?>
             _gaq.push(['_addItem',
                  '<?php echo $order['number'].'_F' ?>',           // Номер заказа
                  '<?php echo $product->Product['article'] ?>',           // Артикул
                  '<?php echo $product->Product['name'] ?>',        // Название товара
                  '<?php echo $product->Product->getMainCategory() ?>',   // Категория товара
                  '<?php echo str_replace(',', '.', $product['price']) ?>',          // Стоимость 1 единицы товара
                  '<?php echo str_replace(',', '.', $product['quantity']) ?>'               // Количество товара
              ]);
  <?php endforeach ?>
           _gaq.push(['_trackTrans']);
   }

  </script>

<?php endif ?>

  <div class="line"></div>

  <?php if ($rememberMe): ?>
  <p class="bFormSave__eBtm">Нажмите кнопку «Запомнить мои данные» &mdash; при следующих покупках вам не придется заново указывать свои контакты и данные для доставки. Кстати, вы еще и сможете отслеживать статус заказа!</p>
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
      <a href="<?php echo url_for('@homepage') ?>" onclick="$('#order1click-container').trigger('close'); return false">Продолжить покупки</a> <span>&gt;</span>
    </div>
  </div>

</div>