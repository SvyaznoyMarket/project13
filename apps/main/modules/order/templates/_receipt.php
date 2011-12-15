<div class="cheque-container">

  <!-- Cheque -->

  <div class="cheque">
    <div class="chequetop">
      <div class="chequebottom">
        <div class="top font16">Ваш заказ:</div>
        <ul>

          <?php foreach ($cart->getReceiptList() as $product): ?>
            <li>
              <div>
                <?php echo $product['name'] ?> (<?php echo $product['quantity'] ?>&nbsp;шт)
              </div>
              <strong><?php echo ($product['price']) ?> <span class="rubl">p</span></strong>
            </li>
          <?php endforeach ?>

        </ul>
        <div class="total">
          Сумма заказа:<br />
          <strong class="font14"><?php echo $cart->getTotal(true) ?> <span class="rubl">p</span></strong><br />
          <!--Дата доставки: 5 октября 20011 г.-->
        </div>
      </div>
    </div>
  </div>
  <!-- /Cheque -->

  <div class="ac">
    <strong><a href="<?php echo url_for('@cart') ?>" class="underline gray">Редактировать товары</a></strong>
  </div>

</div>