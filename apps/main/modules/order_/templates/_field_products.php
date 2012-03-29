<?php
/* @var $deliveryMap Order_DeliveryMapView */
/* @var $deliveryType Order_DeliveryTypeView */
/* @var $item Order_ItemView */
?>

<input id="order-delivery_map-data" type="hidden" data-value='<?php echo json_encode($deliveryMap) ?>' />

<?php foreach ($deliveryMap->deliveryTypes as $deliveryType): ?>
<div data-delivery-type="<?php echo $deliveryType->token ?>" class="bBuyingLineWrap order-delivery-holder">

  <dl class="bBuyingLine">
    <dt>
      <h2>
        <?php echo $deliveryType->name ?>
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

      <div class="order-item-holder" data-template="#order-item-template"></div>

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
  <dl class="bBuyingLine">
    <dt></dt>
    <dd>
      <div>
        <p>24 190 <span class="rubl">p</span></p>

        <p>
          <a data-assign='{"deleteUrl": ["attr", ["href", "_value"]]}' href="#" class="bImgButton mBacket"></a>
          <a class="bImgButton mArrows" href=""></a>
        </p>
        <img data-assign='{"image": ["attr", ["src", "_value"]], "name": ["attr", ["alt", "_value"]]}' src="" alt="" />

            <span class="bBuyingLine__eInfo">
              <data data-assign='{"name": ["text", "_value"]}'></data>
              <br><span>(<data data-assign='{"quantity": ["text", "_value"]}'></data> шт.)</span>
            </span>
      </div>
    </dd>
  </dl>
</div>
<!-- } -->


<?php if (false): ?>
<div id="order-delivery-template">

  <div class='bBuyingLineWrap'>

    <dl class='bBuyingLine'>
      <dt>
      <h2 data-assign-delivery='{"text": "data.name"}'></h2>
      <i data-assign-delivery='{"text": "data.price"}'></i>
      </dt>
      <dd>
        <div>
          <p></p>
          <ul class='bBuyingDates'>
            <li class='bBuyingDates__eLeft'><b></b><span></span></li>
            <?php foreach ($dates as $i => $date): ?>
              <li<?php echo $i >= 7 ? ' style="display:none"' : '' ?> class='bBuyingDates__eDisable' data-assign-date='{"text": "data.date"}' data-week="<?php echo floor($i / 7) + 1 ?>"><?php echo $date['day'] ?> <span><?php echo $date['dayOfWeek'] ?></span></li>
            <?php endforeach ?>
            <li class='bBuyingDates__eRight'><b></b><span></span></li>
          </ul>
          <!-- ko foreach: dates -->
          <span class="bBuyingDatePopup" data-bind="attr: {ref: dv}" style="top: 2px; display:none">
            <h3 class="bBuyingDatePopup__eTitle" data-bind="text: $parent.curDate"></h3>
            <!-- ko foreach: schedule -->
            <span class="bBuyingDatePopup__eLine">
              <i data-bind="css: { bBuyingDatePopup__eOK: $parents[1].curTime() == txt }"></i>
              <span data-bind="text: txt, click: $parents[1].pickTime">c 9:00 до 14:00</span>
            </span>
            <!-- /ko -->
          </span>
          <!-- /ko -->
        </div>
      </dd>
    </dl>

    <div class="order-product-holder"></div>

    <div class='bBuyingLineWrap__eSum'>Итого с доставкой: <b>
      <span data-bind="text: totalPrice"></span> <span class="rubl">p</span></b></div>
  </div>

</div>



<dl id="order-product-template" class='bBuyingLine'>
  <dt></dt>
  <dd>
    <div>
      <p><span data-assign-product='{"text": "data.cost"}'></span> <span class="rubl">p</span></p>

      <p>
        <a class='bImgButton mBacket' href="#" data-assign-product='{"attr": ["href", "data.delete_url"]}'></a>

        <a class='bImgButton mArrows' href></a>
        <span class="bButtonPopup" style="left: 203px; display:none">
          <span class="bButtonPopup__eTitle">Переместить товар:</span>
          <a class="bButtonPopup__eLine moveline"></a>
        </span>
      </p>

      <img data-assign-product='{"attr": ["src", "data.image"]}'/>
          <span class='bBuyingLine__eInfo'>
            <div data-assign-product='{"html": "data.name"}'></div>
            <span>(</span><span data-assign-product='{"html": "data.quantity"}'></span><span> шт.)</span>
          </span>
    </div>
  </dd>
</dl>
<?php endif ?>