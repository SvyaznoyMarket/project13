<?php
/**
 * @var $sf_user myUser
 */

$isCorporative = $sf_user->getGuardUser() && $sf_user->getGuardUser()->getIsCorporative();
?>

<?php $jsValidator = array(
  'order[recipient_first_name]'   => 'Заполните поле',
  'order[recipient_last_name]'    => 'Заполните поле',
  'order[recipient_phonenumbers]' => 'Заполните поле',
  'order[address_street]'         => 'Укажите адрес',
  'order[address_building]'       => 'Укажите адрес',
  'order[payment_method_id]'      => 'Выберите способ оплаты',
  'order[agreed]'                 => 'Необходимо согласие',
);
if (isset($form['address_metro'])) $jsValidator['order[address_metro]'] = 'Укажите ближайшее метро';
?>
<?php
  $jsStationNames = json_encode(isset($subwayStations) ? $subwayStations : array(), JSON_HEX_QUOT | JSON_HEX_APOS);
?>
<?php include_partial('order_/header', array('title' => 'Финальный шаг :)')) ?>
<div id="adfox920" class="adfoxWrapper"></div>

<input disabled="disabled" id="order-validator" type="hidden" data-value='<?php echo json_encode($jsValidator) ?>' />


<script id="mapInfoBlock" type="text/html">
  <div class="bMapShops__ePopupRel">
    <h3><%=name%></h3>
    <span>Работает </span>
    <span><%=regime%></span>
    <br/>
    <span class="shopnum" style="display: none;"><%=id%></span>
    <a class="bGrayButton shopchoose" href="">Забрать из этого магазина</a>
  </div>
</script>

<div id="map-info_window-container" style="display:none"></div>

<div class="pb15"> <a class="motton font14" href="<?php echo $backLink ?>" style="font-weight: bold">&lt; Вернуться к покупкам</a></div>

  

    

    <input id="order-delivery_map-data" type="hidden" data-value='<?php echo $deliveryMap_json ?>' />
    <?php 
    // KNOCKOUT
      include_partial('order_/blocks_tmpl')
    ?>  


  <form id="order-form" style="display:none" data-validator="#order-validator" method="post" action="<?php echo url_for('order_create') ?>" data-delivery-map-url="<?php echo url_for('order_deliveryMap') ?>" data-cart-url="<?php echo url_for('cart') ?>">
    <div class='bBuyingInfo'>
      <h2>Информация о счастливом получателе</h2>

      <div id="user-block">
        <?php if ($sf_user->isAuthenticated()): ?>
        Привет, <a href="<?php echo url_for('user') ?>"><?php echo $sf_user->getGuardUser() ?></a>
        <?php else: ?>
        Уже покупали у нас? <strong><a class="auth-link underline" data-update-url="<?php echo url_for('order_getUser') ?>" href="<?php echo url_for('user_signin') ?>">Авторизуйтесь</a></strong> и вы сможете использовать ранее введенные данные
        <?php endif ?>
      </div>

      <dl class='bBuyingLine'>

        <dt>Имя получателя*</dt>
        <dd>
          <div>
            <p></p>
            <?php echo $form['recipient_first_name']->render(array('class' => 'bBuyingLine__eText mInputLong')) ?>
          </div>
        </dd>
      </dl>

      <dl class='bBuyingLine'>

        <dt>Фамилия получателя*</dt>
        <dd>
          <div>
            <p></p>
            <?php echo $form['recipient_last_name']->render(array('class' => 'bBuyingLine__eText mInputLong')) ?>
          </div>
        </dd>
      </dl>

      <dl class='bBuyingLine'>
        <dt>Телефон для связи*</dt>
        <dd> 
            <div class="phonePH">
                <span class="placeholder">8</span>
                 <?php echo $form['recipient_phonenumbers']->render(array('class' => 'bBuyingLine__eText mInputLong')) ?>
            </div>
            
            <div>
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
            <?php echo $form['region_id'] ?>
          </div>

          <?php if (isset($form['address_metro'])): ?>
          <div class="ui-css">
            <span class="placeholder">Метро</span><?php echo $form['address_metro']->render(array('class' => 'placeholder-input bBuyingLine__eText mInputLong', 'title' => 'Метро')) ?>
            <div id="metrostations" data-name='<?php echo $jsStationNames ?>'></div>
            <?php echo $form['subway_id']->render() ?>
          </div>
          <?php endif ?>

          <div class="street">
            <span class="placeholder">Улица</span><?php echo $form['address_street']->render(array('class' => 'placeholder-input bBuyingLine__eText mInputLong', 'title' => 'Улица')) ?>
          </div>
			
			<div class="number">
				<span class="placeholder">Дом</span><?php echo $form['address_building']->render(array('class' => 'placeholder-input bBuyingLine__eText mInputShort', 'title' => 'Дом')) ?>
			</div>
			
          <div class="building">
            <span class="placeholder">Корпус</span><?php echo $form['address_number']->render(array('class' => 'placeholder-input bBuyingLine__eText mInputShort', 'title' => 'Корпус')) ?>
          </div>
          <div class="apartament">
            <span class="placeholder">Квартира</span><?php echo $form['address_apartment']->render(array('class' => 'placeholder-input bBuyingLine__eText mInputShort', 'title' => 'Квартира')) ?>
          </div>
          <div class="floor">
            <span class="placeholder">Этаж</span><?php echo $form['address_floor']->render(array('class' => 'placeholder-input bBuyingLine__eText mInputShort', 'title' => 'Этаж')) ?>
          </div>
        </dd>
      </dl>

      <dl class='bBuyingLine'>
        <dt>Пожелания и дополнения</dt>

        <dd>
          <div>
            <p></p>
            <?php echo $form['extra']->render(array('class' => 'bBuyingLine__eTextarea')) ?>
          </div>
        </dd>
      </dl>

      <dl class='bBuyingLine<?php echo $isCorporative ? ' hidden' : '' ?>'>
        <dt>Если у вас есть карта<br />&laquo;Связной-Клуб&raquo;, вы можете указать ее номер</dt>
        <dd class="bSClub">
          <div class="bSClub__eWrap pb25">
            <?php echo $form['sclub_card_number']->render(array('class' => 'bBuyingLine__eText mInputShort mb15')) ?>
            <i class="mILong">Чтобы получить 1% от суммы заказа<br/>баллами на карту, введите ее номер,<br />расположенный на обороте под штрихкодом</i>
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
              <?php if ($isCorporative) { ?>
                <h4>Я ознакомлен и согласен с &laquo;<a href="<?php echo url_for('default_show', array('page' => 'corp-terms')) ?>" target="_blank">Условиями продажи</a>&raquo; и &laquo;<a href="<?php echo url_for('default_show', array('page' => 'legal')) ?>" target="_blank">Правовой информацией</a>&raquo;*</h4>
              <? } else { ?>
                <h4>Я ознакомлен и согласен с &laquo;<a href="<?php echo url_for('default_show', array('page' => 'terms')) ?>" target="_blank">Условиями продажи</a>&raquo; и &laquo;<a href="<?php echo url_for('default_show', array('page' => 'legal')) ?>" target="_blank">Правовой информацией</a>&raquo;*</h4>
              <? } ?>
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
  

  </form>

  <dl class='bBuyingLine mConfirm'>

    <dt>&nbsp;</dt>
    <dd>
      <div><a id="order-submit" class='bBigOrangeButton disable' href="#">Завершить оформление</a></div>
    </dd>
  </dl>

  <div id="order-shop-popup" class="hidden"></div>

<div id="order-loader" class='bOrderPreloader hf'>
  <span>Формирую заказ...</span><img src='/images/bPreloader.gif' />
</div>

<?php if(false) { include_partial('order_/map', $sf_data); } ?>

<?php //include_partial('order_/footer') ?>

<div id="marketgidOrder" class="jsanalytics"></div>
<?php if ('live' == sfConfig::get('sf_environment')): ?>
  <div id="heiasOrder" data-vars="<?php echo $sf_user->getCart()->getSeoCartArticle() ?>" class="jsanalytics"></div>

<?php endif ?>