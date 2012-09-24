<div class='bMobDown mBR5 mW2 mW1000 p0' style="display:none" id="order1click-container-new">
	<div class='bMobDown__eWrap'>
		<div class='bMobDown__eClose top0 close'></div>
		<form id="oneClick" action="">
		
		<table class='bFast' cellpadding=0 cellspacing=0>
      <tr>
        <td class='bFast__eItem'>
          <div class='bFast__eItemWrap'>
            <div class='bFast__eArrow'></div>
            <div class='bFast__eNum'>Артикул #<span data-bind="text: shortcut"></span></div>
            <div class="bFast__eTitle"><a href="" data-bind="html: title"></a></div>
            <div class='bFast__eCenter'><img alt="" data-bind="attr: {src: icon}"/></div>
            <div class='bFast__eCenter' data-bind="if : !noQBar() ">
              <div class='bCountSet'>
                <a class='bCountSet__eP' href="#" data-bind="click: plusItem">+</a>
                <a class='bCountSet__eM' href="#" data-bind="click: minusItem">-</a>
                <span data-bind="text: quantityTxt">1 шт.</span>
              </div>
            </div>
            <div class='bFast__ePrice'>Цена:
              <div><span data-bind="text: priceTxt"></span> <span class="rubl">p</span></div>
            </div>
            <div class='bFast__ePrice'>Доставка:
              <div><span data-bind="text: chosenDlvr().price"></span> <span class="rubl">p</span></div>
            </div>
            <div class='bFast__ePrice'>Итого:
              <div><span data-bind="text: total()"></span> <span class="rubl">p</span></div>
            </div>
          </div>
        </td>
        <td class='bFast__eForm'>
          <table cellpadding=0 cellspacing=0 class='bFastInner'>
            <tr><th colspan="2"><h2>Заполните форму быстрого заказа:</h2></th></tr>
            <tr data-bind="visible: noDelivery()"><td colspan="2"><h2 class="red">Товар в количестве <span data-bind="text: quantity()"></span> шт. отсутствует на складе.</h2></td></tr>
            <tr data-bind="visible: !noDelivery()"><td width="200">Способ получения заказа:</td>
              <td>
                <div class='bSelect mFastInpSmall' data-bind="css: { mDisabled : (disabledSelectors() || stableType() ) }">
                  <span data-bind="text: chosenDlvr().name"></span>
                  <div class='bSelect__eArrow'></div>
                  <div class='bSelect__eDropmenu'>
                    <!-- ko foreach : dlvrs -->
                    <div data-bind="click: $root.changeDlvr"><span data-bind="text: name"></span></div>
                    <!-- /ko -->
                  </div>
                </div>

                <div class='bSelect mFastInpSmall' data-bind="css: { mDisabled : disabledSelectors() }">
                  <span data-bind="text: chosenDate().name"></span>
                  <div class='bSelect__eArrow'></div>
                  <div class='bSelect__eDropmenu'>
                    <!-- ko foreach : dates -->
                    <div data-bind="click: $root.pickDate"><span data-bind="text: name"></span></div>
                    <!-- /ko -->
                  </div>
                </div>
              </td></tr>
            <!-- ko if: chosenDlvr().type == 'self' -->
            <tr data-bind="visible: !noDelivery()"><td>Магазин для самовывоза:</td>
              <td>
                <div class='bSelect mFastInpBig' data-bind="css: { mDisabled : disabledSelectors() }">
                  <span data-bind="text: chosenShop().address"></span>
                  <div class='bSelect__eArrow'></div>
                  <div class='bSelect__eDropmenu'>
                    <!-- ko foreach : shops -->
                    <div data-bind="click: $root.pickShop"><span data-bind="text: address"></span></div>
                    <!-- /ko -->
                  </div>
                </div>
                <!-- ko if: !disabledSelectors() -->
                <a class='bFast__eMapLink' href="" data-bind="click: toggleMap">
                  <!-- ko if: !showMap() -->
                  Показать карту магазинов
                  <!-- /ko -->
                  <!-- ko if: showMap() -->
                  Скрыть карту магазинов
                  <!-- /ko -->
                </a>
                <!-- /ko -->
            </td></tr>
            <!-- /ko -->

            <tr><td colspan="2" data-bind="style: { display: showMap() ? 'table-cell' : 'none' }">
              <div class='bMapShops__eMapWrap' id="mapPopup" style="display:none"> </div>
              <div id="map-info_window-container" data-bind="with: pickedShop" style="display:none">
                <div class='bMapShops__ePopupRel'>
                  <h3 data-bind="text: address"></h3>
                  <span data-bind="text: regtime"></span><br>
                  <span class="shopnum" style="display:none" data-bind="text: id"></span>
                  <a href class='bGrayButton shopchoose' data-bind="click: $root.shopChoose">Забрать из этого магазина</a>
                </div>
              </div>
            </td></tr>


            <!-- ko foreach: textfields -->
            <tr>
              <td><span data-bind="text: title"></span>:</td>
              <td>
                <input data-bind="event: { change: $root.validateField }, value: value, attr: { name: name, id: selectorid }, css: { mEmpty: valerror }" class='bFastInner__eInput'>
                <!-- ko if: valerror -->
                <span class='mEmpty'>(!) Пожалуйста, верно заполните поле</span>
                <!-- /ko -->
              </td>
            </tr>
            <!-- /ko -->

            <tr><td colspan=2>Отправьте заказ и мы вам скоро перезвоним :)<br>
            Специалист нашего Контакт-cENTERа уточнит, где и когда будет удобно получить заказ.</td></tr>
            <tr><td colspan=2>
              <a class='bBigOrangeButton' href="" data-bind="css: { disable : noDelivery() },text: formStatusTxt, click: validateForm"></a>
            </td></tr>
            <tr data-bind="if: newWarehouse">
      				<td class="newWarehouse" colspan="2"><img src="/css/skin/img/greenCar.png" alt="green car" />Этот товар живет на новом красивом складе. Мы доставим его отдельным заказом :)</td>
      			</tr>
          </table>
        </td>
      </tr>
		</table>
		</form>
	</div>
</div>