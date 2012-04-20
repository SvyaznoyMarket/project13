<?php include_partial('order_/header', array('title' => 'Финальный шаг :)')) ?>

<input disabled="disabled" id="map-center" type="hidden" data-content='<?php echo $mapCenter ?>' />
<input disabled="disabled" id="order-validator" type="hidden" data-value='{"order[recipient_first_name]":"Заполните поле", "order[recipient_phonenumbers]":"Заполните поле", "order[address]":"Заполните поле", "order[payment_method_id]":"Выберите способ оплаты", "order[agreed]":"Необходимо согласие"}' />

<div class="pb15">< <a href="<?php echo url_for('cart') ?>">Вернуться к покупкам</a></div>

<form id="order-form" data-validator="#order-validator" method="post" action="<?php echo url_for('order_create') ?>" data-delivery-map-url="<?php echo url_for('order_deliveryMap') ?>" data-cart-url="<?php echo url_for('cart') ?>">

  <div id="order-form-part1" class='bBuyingInfo hidden'>

    <h2>Информация о заказе</h2>

    <?php echo $form['region_id'] ?>

    <?php echo $form['delivery_type_id'] ?>

  </div>

  <div id="order-loader-holder">
    <div class='bOrderPreloader'>
      <span>Загрузка...</span><img src='/images/bPreloader.gif'>
    </div>
  </div>

  <div id="order-form-part2" class="hidden">

    <div id="order-message" class='bBuyingInfo'>
      <span><?php count($deliveryMap->unavailable) ? 'Некоторые товары не могут быть доставлены' : 'Отличный выбор!' ?></span>
    </div>

    <div id="order-delivery-holder">
      <?php include_component('order_', 'field_products', $sf_data) ?>
    </div>

    <dl class='bBuyingLine mSumm order-total-container'>
      <dt><a class="red" style="border-color: #cb3735" href="<?php echo url_for('cart') ?>" alt="Вернуться в корзину для выбора услуг и увеличения количества товаров" title="Вернуться в корзину для выбора услуг и увеличения количества товаров">Редактировать товары</a></dt>
      <dd>
        <div>Сумма всех заказов <h3><span data-assign='{"total": ["text", "_value"]}'></span> <span class="rubl">p</span></h3></div>
      </dd>
    </dl>

    <div class='bBuyingInfo'>
      <h2>Информация о счастливом получателе</h2>

      <dl class='bBuyingLine'>

        <dt>Имя и Фамилия получателя*</dt>
        <dd>
          <div>
            <p></p>
            <?php echo $form['recipient_first_name']->render(array('class' => 'bBuyingLine__eText mInputLong')) ?>
          </div>
        </dd>
      </dl>

      <dl class='bBuyingLine'>
        <dt>Телефон для связи*</dt>
        <dd>

            <p></p>
            <?php echo $form['recipient_phonenumbers']->render(array('class' => 'bBuyingLine__eText mInputLong')) ?>
            <div>
              <p></p>
              <label for="<?php echo $form['is_receive_sms']->renderId() ?>">
                <b></b> <h5>Я хочу получать СМС уведомления об изменении статуса заказа</h5>
                <?php echo $form['is_receive_sms']->render(array('class' => 'bBuyingLine__eRadio')) ?>
              </label>
            </div>
        </dd>
      </dl>


      <dl class='bBuyingLine' id="addressField">
        <dt>Адрес доставки*</dt>
        <dd>
          <div>
            <p></p>
            <?php echo $form['address']->render(array('class' => 'bBuyingLine__eText mInputLong')) ?>
          </div>
        </dd>
      </dl>

      <dl class='bBuyingLine'>
        <dt>Пожелания и дополнения</dt>

        <dd>
          <div>
            <p></p>
            <?php echo $form['extra']->render(array('class' => 'bBuyingLine__eTextarea')) ?>
            <i class='mILong'>Сколько раз повернуть направо, наличие бабушек у подъезда или цвет глаз секретаря - укажите любую информацию, которая поможет нам еще быстрее выполнить Ваш заказ.</i>
          </div>
        </dd>
      </dl>

      <dl class='bBuyingLine'>
        <dt>У вас есть карта &laquo;Связной-Клуб&raquo;?</dt>
        <dd class="bSClub">
          <div class="bSClub__eWrap pb25">
            <?php echo $form['sclub_card_number']->render(array('class' => 'bBuyingLine__eText mInputShort mb15')) ?>
            <h4>Номер карты для зачисления баллов</h4><br>
            Номер под штрихкодом на оборотной стороне карты, начинается на 298
          </div>
          <!--<label><b></b> <h5>Сохранить мои данные для следующих покупок</h5> <input class='bBuyingLine__eRadio' name='r1' type='radio'></label>-->
        </dd>
      </dl>

      <h2>Об оплате</h2>

      <?php echo $form['payment_method_id'] ?>

      <div class='line'></div>

      <dl class='bBuyingLine'>
        <dt></dt>
        <dd>

          <div>
            <label class='mLabelLong' for="<?php echo $form['agreed']->renderId() ?>">
              <b></b>
              <h4>Я ознакомлен и согласен с &laquo;<a href="<?php echo url_for('default_show', array('page' => 'terms')) ?>" target="_blank">Условиями продажи</a>&raquo; и &laquo;<a href="<?php echo url_for('default_show', array('page' => 'legal')) ?>" target="_blank">Правовой информацией</a>&raquo;*</h4>
              <?php echo $form['agreed']->render(array('class' => 'bBuyingLine__eRadio')) ?>
            </label>
          </div>
        </dd>
      </dl>
      <dl class='bBuyingLine'>
        <dt></dt>
        <dd>
          <div>
            <p></p>
            <i class='mILong'>* Поля обязательные для заполнения</i>
          </div>
        </dd>
      </dl>

    </div>

    <dl class='bBuyingLine mConfirm'>

      <dt>&nbsp;</dt>
      <dd>
        <div><a id="order-submit" class='bBigOrangeButton' href="#">Завершить оформление</a></div>
      </dd>
    </dl>

  </div>

  <div id="order-shop-popup" class="hidden"></div>

</form>

<div id="order-loader" class='bOrderPreloader hf'>
  <span>Формирую заказ...</span><img src='/images/bPreloader.gif'>
</div>

<?php include_partial('order_/map', $sf_data) ?>

<?php include_partial('order_/footer') ?>

<?php if (false): ?>
  <?php slot('seo_counters_advance') ?>
    <?php include_component('order', 'seo_counters_advance', array('step' => 2)) ?>
  <?php end_slot() ?>
<?php endif ?>