<?php use_helper('Date') ?>

<div style="width: 900px;">

  <div class="bFormSave">
    <h2>Номер вашего заказа: <?php echo $order['number'] ?></h2>
    <p>Дата заказа: <?php echo format_date($order['created_at'], 'dd.MM.yyyy') ?>.<br>Сумма заказа: <?php echo number_format($order['sum'], 0, ',', ' ') ?> <span class="rubl">p</span></p>
    <span>В ближайшее время мы свяжемся с вами для уточнения параметров заказа.</span>
  </div>

  <div class="line"></div>

  <p class="bFormSave__eBtm">Нажмите кнопку «Запомнить мои данные» &mdash; при следующих покупках вам не придется заново указывать свои контакты и данные для доставки. Кстати, вы еще и сможете отслеживать статус заказа!</p>
  <div class="bFormB2">

    <?php if ($form->isValid()): ?>
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
      <a href="<?php echo url_for('@homepage') ?>">Продолжить покупки</a> <span>&gt;</span>
    </div>
  </div>

</div>