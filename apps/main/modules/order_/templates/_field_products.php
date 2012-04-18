<?php
/* @var $deliveryMap Order_DeliveryMapView */
/* @var $deliveryType Order_DeliveryTypeView */
/* @var $item Order_ItemView */
?>

<input id="order-delivery_map-data" type="hidden" data-value='<?php echo json_encode($deliveryMap) ?>' />

<?php foreach ($deliveryMap->deliveryTypes as $deliveryType): ?>

<?php
$currentWeekNum = 1;
foreach ($dates as $i => $date) {
    if ($date['value'] == $deliveryType->date) {
      $currentWeekNum = $date['weekNum'];
      break;
    }
}
?>

<div id="order-unavailable" class="bBuyingLineWrap" style="display: none; border-color: #cb3735;">
  <dl class="bBuyingLine">
    <dt>
      <h2 class="red">Недоступные товары</h2>
    </dt>
  </dl>

  <i>
    <i>
      <dl class="bBuyingLine">
        <dt></dt>
        <dd class="order-item-holder"></dd>
      </dl>
    </i>
  </i>
</div>


<div data-value="<?php echo $deliveryType->token ?>" class="bBuyingLineWrap order-delivery-holder">

  <div class="delivery-message red"></div>

  <dl class="bBuyingLine">
    <dt>
      <h2>
        <?php echo $deliveryType->shortName ?>
        <span data-assign='{"displayDate": ["text", "_value"]}'><?php echo $deliveryType->displayDate ?></span>
        <br><span data-assign='{"displayInterval": ["text", "_value"]}'><?php echo $deliveryType->displayInterval ?></span>
      </h2>

      <i class="order-delivery_price" data-template="#order-delivery_price-template"></i>
    </dt>
    <i>
      <i>
        <dd>
          <div>
            <p></p>
            <ul class="bBuyingDates" data-interval-holder="<?php echo('self' != $deliveryType->type ? ('#order-interval_'.$deliveryType->token.'-holder') : '') ?>">
              <li class="bBuyingDates__eLeft order-delivery_date-control" data-value="<?php echo $currentWeekNum > 1 ? ($currentWeekNum - 1) : 1 ?>" data-direction="prev"><b></b><span></span></li>
              <?php foreach ($dates as $i => $date): ?>
                <li<?php echo $date['weekNum'] != $currentWeekNum ? ' style="display:none"' : '' ?> class='bBuyingDates__eDisable order-delivery_date' data-value='<?php echo $date['value'] ?>' data-display-value='<?php echo $date['displayValue'] ?>' data-week="<?php echo $date['weekNum'] ?>"><?php echo $date['day'] ?> <span><?php echo $date['dayOfWeek'] ?></span></li>
              <?php endforeach ?>
              <li class="bBuyingDates__eRight order-delivery_date-control" data-value="<?php echo $currentWeekNum + 1 ?>" data-direction="next"><b></b><span></span></li>
            </ul>

            <span id="<?php echo 'order-interval_'.$deliveryType->token.'-holder' ?>" class="order-interval-holder" data-template="#order-interval-template"></span>

          </div>
        </dd>
      </i>
    </i>
  </dl>

  <i>
    <i>

      <dl class="bBuyingLine">
        <dt>
        <?php if ($deliveryType->shop): ?>
          <span data-assign='{"shopName": ["text", "_value"]}'><?php echo $deliveryType->shop->name ?></span>
          <p></p>
          <a class="bBigOrangeButton order-shop-button" data-delivery="<?php echo $deliveryType->token ?>" style="font-size: 16px; padding: 6px 30px; border: 1px solid #E26500;" href="#">Другой магазин</a>
        <?php endif ?>
        </dt>
        <dd class="order-item-holder" data-template="#order-item-template"></dd>
      </dl>

      <div class="order-delivery_total-holder" data-template="#order-delivery_total-template"></div>
    </i>
  </i>
</div>
<?php endforeach ?>


<!-- шаблон интервалов { -->
<div id="order-interval-template" class="hidden">
  <span class="bBuyingDatePopup">
    <h3 class="bBuyingDatePopup__eTitle" data-assign='{"date": ["text", "_value"]}'></h3>
    <span class="bBuyingDatePopup__eLine order-interval" data-assign='{"value": ["attr", ["data-value", "_value"]], "date": ["attr", ["data-date", "_value"]], "deliveryType": ["attr", ["data-delivery-type", "_value"]]}' data-value="" data-date="" data-delivery-type=""><i></i><data data-assign='{"name": ["text", "_value"]}'></data></span>
  </span>
</div>
<!-- } -->

<!-- шаблон товаров { -->
<div id="order-item-template" class="hidden">
  <div class="order-item-container">
    <p><data data-assign='{"totalFormatted": ["text", "_value"]}'></data> <span class="rubl">p</span></p>

    <p>
      <a data-assign='{"deleteUrl": ["attr", ["href", "_value"]], "token": ["attr", ["data-token", "_value"]]}' href="#" class="bImgButton mBacket" data-token=""></a>
      <!--<a class="bImgButton mArrows order-item_delivery-button" href="#" data-assign='{"token": ["attr", ["data-value", "_value"]]}' data-value='' data-template="#order-item_delivery-template"></a>-->
    </p>
    <img data-assign='{"image": ["attr", ["src", "_value"]], "name": ["attr", ["alt", "_value"]]}' src="" alt="" />

    <span class="bBuyingLine__eInfo">
      <data data-assign='{"name": ["text", "_value"]}'></data>
      <br><span>(<data data-assign='{"quantity": ["text", "_value"]}'></data> шт.)</span>
    </span>
  </div>
</div>
<!-- } -->

<!-- шаблон меню для перемещения в другой заказ -->
<div id="order-item_delivery-template" class="hidden">
  <span class="bButtonPopup">
    <span class="bButtonPopup__eTitle">Переместить товар:</span>
    <a class="bButtonPopup__eLine" data-assign='{"name": ["text", "_value"], "route": ["attr", ["data-value", "_value"]]}' data-value=''></a>
  </span>
</div>
<!-- } -->

<!-- шаблон стоимости доставки -->
<div id="order-delivery_price-template" class="hidden">
  <span class="red">Стоимость доставки <data data-assign='{"price": ["text", "_value"]}'></data> <span class="rubl">p</span><i></i></span>
</div>
<!-- } -->

<!-- шаблон стоимости подзаказа -->
<div id="order-delivery_total-template" class="hidden">
  <div class="bBuyingLineWrap__eSum"><data data-assign='{"name": ["text", "_value"]}'></data>: <b><data data-assign='{"total": ["text", "_value"]}'></data> <span class="rubl">p</span></b></div>
</div>
<!-- } -->