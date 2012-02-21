<?php echo $form['product_quantity']->render() ?>
<?php echo $form['shop_id']->render() ?>

<div class="basketline">
  <?php include_component('product', 'show', array('view' => 'orderOneClick', 'product' => $product)) ?>

  <div class="clear"></div>

  <div class="b1Click__eTop mPR">
    <div class="b1Click__ePrice">Сумма заказа:
      <span class="b1Click__ePriceBig">
        <span class="price"><?php echo number_format($order['sum'], 0, ',', ' ') ?></span> <span class="rubl">p</span>
      </span>
    </div>
  </div>
</div>

<div class="clear"></div>

<div class="b1Click">

  <div class="b1Click__eTop">
    Это быстро! Просто заполните 2 поля.<br>
    После оформления заказа с вами обязательно свяжется специалист нашего Контакт-cENTER
    <div class="clear"></div>

    <div class="b1Click__eInp">
      <span>Имя получателя:</span><?php echo $form['recipient_first_name']->render($form['recipient_first_name']->hasError() ? array('class' => 'mRed') : array()) ?>
    </div>

    <div class="b1Click__eInp">
      <span>Телефон для связи:</span><?php echo $form['recipient_phonenumbers']->render($form['recipient_phonenumbers']->hasError() ? array('class' => 'mRed') : array()) ?>
    </div>

    <div class="bFormB2">
      <input type="submit" value="Оформить заказ" id="bigbutton" class="bBigOrangeButton" style="margin-left: 20px; display: inline-block; width: 592px; cursor: pointer; height: 45px;" />
    </div>
  </div>
</div>
