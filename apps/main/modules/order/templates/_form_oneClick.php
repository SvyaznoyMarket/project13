<?php echo $form['product_quantity']->render() ?>
<?php echo $form['shop_id']->render() ?>

<div class="basketline">
  <?php include_component('product', 'show', array('view' => 'orderOneClick', 'product' => $product)) ?>
</div>

<div class="clear"></div>

<div class="b1Click">
  <div class="b1Click__eTop">
    <div class="b1Click__ePrice">Сумма заказа:<br><span class="b1Click__ePriceBig"><span class="price"><?php echo number_format($order['sum'], 0, ',', ' ') ?></span> <span class="rubl">p</span></span></div>
    Вы можете купить товар за 1 клик &mdash; оформление заказа займет всего 20 секунд, просто заполните 2 поля.
    <div class="clear"></div>
    <div class="b1Click__eInp">
      <span>Имя получателя: </span>
      <?php echo $form['recipient_first_name']->render($form['recipient_first_name']->hasError() ? array('class' => 'mRed') : array()) ?>
    </div>
    <div class="b1Click__eInp">
      <span>Телефон для связи:</span>
      <?php echo $form['recipient_phonenumbers']->render($form['recipient_phonenumbers']->hasError() ? array('class' => 'mRed') : array()) ?>
    </div>
    <div class="bFormB2"><span><input type="submit" value="Оформить заказ" id="bigbutton" class="button bigbutton"> </span></div>
  </div>
</div>

</form>