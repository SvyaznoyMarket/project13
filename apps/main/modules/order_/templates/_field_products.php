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
              <a class="bButtonPopup__eLine moveline" data-bind="text: txt, click: $root.shifting.bind($data, $parent, 'rapid' )"></a>
            </span>
      </p>

      <img data-assign-product='{"attr": ["src", "data.image"]}' />
          <span class='bBuyingLine__eInfo'>
            <div data-assign-product='{"html": "data.name"}'></div>
            <span>(</span><span data-assign-product='{"html": "data.quantity"}'></span><span> шт.)</span>
          </span>
    </div>
  </dd>
</dl>