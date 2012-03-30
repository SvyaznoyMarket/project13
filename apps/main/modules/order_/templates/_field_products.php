<?php
/* @var $deliveryMap Order_DeliveryMapView */
/* @var $deliveryType Order_DeliveryTypeView */
/* @var $item Order_ItemView */
?>

<input id="order-delivery_map-data" type="hidden" data-value='<?php echo json_encode($deliveryMap) ?>' />

<?php foreach ($deliveryMap->deliveryTypes as $deliveryType): ?>
<div data-value="<?php echo $deliveryType->token ?>" class="bBuyingLineWrap order-delivery-holder">

  <dl class="bBuyingLine">
    <dt>
      <h2>
        <?php echo $deliveryType->shortName ?>
        <span>30 марта</span>
        <br><span>с 12:00 до 18:00</span>
      </h2>

      <i>Стоимость доставки 700 <span class="rubl">p</span><i></i></i>
    </dt>
    <i>
      <i>
        <dd>
          <div>
            <p></p>
            <ul class="bBuyingDates">
              <li class="bBuyingDates__eLeft mDisabled"><b></b><span></span></li>
              <?php foreach ($dates as $i => $date): ?>
                <li<?php echo $i >= 7 ? ' style="display:none"' : '' ?> class='bBuyingDates__eDisable' data-assign-date='{"text": "data.date"}' data-week="<?php echo floor($i / 7) + 1 ?>"><?php echo $date['day'] ?> <span><?php echo $date['dayOfWeek'] ?></span></li>
              <?php endforeach ?>
              <li class="bBuyingDates__eRight"><b></b><span></span></li>
            </ul>

            <span class="order-interval-holder" data-template="#order-interval-template"></span>

          </div>
        </dd>
      </i>
    </i>
  </dl>

  <i>
    <i>

      <dl class="bBuyingLine">
        <dt><?php echo $deliveryType->shop ? $deliveryType->shop->name : '' ?></dt>
        <dd class="order-item-holder" data-template="#order-item-template"></dd>
      </dl>

      <div class="bBuyingLineWrap__eSum">Итого с доставкой: <b>30 840 <span class="rubl">p</span></b></div>
    </i>
  </i>
</div>
<?php endforeach ?>

<!-- шаблон интервалов { -->
<div id="order-interval-template" class="hidden">
  <span style="top: 2px; left: 152px" class="bBuyingDatePopup">
    <h3 class="bBuyingDatePopup__eTitle">12 декабря</h3>
    <span class="bBuyingDatePopup__eLine"><i class="bBuyingDatePopup__eOK"></i> c 9:00 до 14:00</span>
    <span class="bBuyingDatePopup__eLine"><i></i> c 14:00 до 18:00</span>
    <span class="bBuyingDatePopup__eLine"><i></i> c 18:00 до 21:00</span>
  </span>
</div>
<!-- } -->

<!-- шаблон товаров { -->
<div id="order-item-template" class="hidden">
  <div>
    <p><data data-assign='{"totalFormatted": ["text", "_value"]}'></data> <span class="rubl">p</span></p>

    <p>
      <a data-assign='{"deleteUrl": ["attr", ["href", "_value"]]}' href="#" class="bImgButton mBacket"></a>
      <a class="bImgButton mArrows order-item_delivery-button" href="" data-template="#order-item_delivery-template"></a>
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
    <a class="bButtonPopup__eLine" data-assign='{"name": ["text", "_value"]}'></a>
  </span>
</div>
<!-- } -->

