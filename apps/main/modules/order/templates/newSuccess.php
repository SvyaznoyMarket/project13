<?php include_partial('order/header', array('title' => 'Финальный шаг :)')) ?>

<input disabled="disabled" id="delivery-map" type="hidden" data-value='<?php echo $deliveryMap ?>' />
<input disabled="disabled" id="map-center" type="hidden" data-content='<?php echo $mapCenter ?>'>
<input disabled="disabled" id="delete-urls" type="hidden"
	data-services='<?php echo json_encode($serviceDeleteUrls) ?>'
	data-products='<?php echo json_encode($productDeleteUrls) ?>'>

<?php slot('js_template', get_partial('order/js_template', $sf_data)) ?>

<form id="order" method="post" action="<?php echo url_for('order_new') ?>">

  <div class='bBuyingInfo'>

    <h2>Информация о заказе</h2>

    <?php echo $form['region_id'] ?>

    <?php echo $form['delivery_type_id'] ?>

    <span>Отличный выбор! Для вашего удобства мы сформировали несколько заказов в зависимости от типа доставки:</span>
  </div>


  <?php include_partial('order/field_product_list', $sf_data) ?>


  <div class='bBuyingInfo'>
    <h2>Информация о счастливом получателе</h2>

    <dl class='bBuyingLine'>

      <dt>Имя и Фамилия получателя*:</dt>
      <dd>
        <div>
          <p></p>
          <?php echo $form['recipient_first_name']->render(array('class' => 'bBuyingLine__eText mInputLong')) ?>
        </div>
      </dd>
    </dl>

    <dl class='bBuyingLine'>
      <dt>Телефон для связи*:</dt>
      <dd>

        <div>
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


    <dl class='bBuyingLine'>
      <dt>Адрес доставки*:</dt>
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

        <div>
          <p></p>
          <i class='mILong'>* Поля обязательные для заполнения</i>
        </div>
      </dd>
    </dl>

  </div>


  <dl class='bBuyingLine mConfirm'>

    <dt>< <a href="<?php echo url_for('cart') ?>">Вернуться к покупкам</a></dt>
    <dd>
      <div><a class='bBigOrangeButton' href>Завершить оформление</a></div>
    </dd>
  </dl>

</form>


<?php include_partial('order/footer') ?>


<?php slot('seo_counters_advance') ?>
<?php include_component('order', 'seo_counters_advance', array('step' => 2)) ?>
<?php end_slot() ?>