<div id="MVVM">

<div class='bOrderPreloader' data-bind="style: { display: $root.showForm()===true ? 'none' : 'block' }">
	<span>Ваш заказ формируется...</span><img src='/images/bPreloader.gif'>
</div>

<div id="OrderView" data-bind="visible: $root.showForm()" style="display:none">
<h2>Информация о заказе</h2>
<dl class="bBuyingLine">
	<dt>Выберите предпочтительный способ</dt>
	<dd id="dlvrTypes">
		<!-- ko if: dlvrCourierEnable() -->
		<div>
			<p></p>
			<label for="order_delivery_type_id_1"
				data-bind="click: pickCourier, css: {mChecked: !dlvrShopEnable()}">
				<b></b> Доставка заказа курьером
				<input type="radio" name="order[delivery_type_id]" class="bBuyingLine__eRadio" id="order_delivery_type_id_1"
				value="1" autocomplete="off"/>
			</label>
			<i>Мы привезем заказ по любому удобному вам адресу. Пожалуйста, укажите дату и время доставки.</i>
		</div>
		<!-- /ko -->
		<!-- ko if: dlvrShopEnable() -->
		<div>
			<p></p>
			<label class="" for="order_delivery_type_id_3"
				data-bind="click: pickShops, css: {mChecked: !dlvrCourierEnable()}">
				<b></b>
				Самостоятельно заберу в магазине
				<input type="radio" name="order[delivery_type_id]" class="bBuyingLine__eRadio" id="order_delivery_type_id_3"
				value="3" autocomplete="off"/>
			</label>
			<i>Вы можете самостоятельно забрать товар из ближайшего к вам магазина Enter. Услуга бесплатная! Пожалуйста, выберите магазин.</i>
		</div>
		<!-- /ko -->
		<div class="pl20 pt5">
			<a href="#" style="display: none; font-size: 16px; padding: 6px 30px;" class="bBigOrangeButton selectShop"
				data-bind="visible: shopButtonEnable, click: showAllShops">
				Выберите магазин</a>
		</div>

	</dd>
</dl>

  <div id="orderMapPopup" class='popup'>
    <i class='close'></i>
    <div class='bMapShops__eMapWrap' id="mapPopup" style="float: right;">
    </div>
    <div class='bMapShops__eList'>
      <h3>Выберите магазин Enter для самовывоза</h3>
      <ul id="mapPopup_shopInfo">
      	<!-- ko foreach: shopsInPopup -->
      	<li data-bind="attr: {ref: id}, click: $root.selectShop ">
			<div class="bMapShops__eListNum"><img src="/images/shop.png" alt=""/></div>
			<div data-bind="text: name"></div>
			<span>Работаем</span> <span data-bind="text: regime"></span>
		</li> 
		<!-- /ko -->
      </ul>
    </div>
  </div>

<div data-bind="style: { display: $root.stolenItems().length > 0 ? 'block' : 'none' }" class="hf">
	<div class='bMobDownWrapAbs customalign'>
		<div class='bMobDownWrapRel'>
			<div class='bMobDown mBR5 mW2 mW750'>
				<div class='bMobDown__eWrap'>
					<img class='fr pt20 mr20' src='/images/error_ajax.gif'/>
					<h2 class="pb30">Кто-то был быстрее вас.<br/>
					Некоторых товаров уже нет в наличии:</h2>
					<!-- ko foreach: $root.stolenItems -->
					<div class='bFormSave'>
						<span data-bind="html: title"></span>
						<h2><span data-bind="html: price"></span> <span class='rubl'>p</span></h2>
					</div>
					<!-- /ko -->
					<a class='bOrangeButton mr20' id="tocontinue" href>Оформить заказ без этих товаров</a>
					<a class='bOrangeButton' >Подобрать похожий товар</a>
				</div>
			</div>
		</div>
	</div>
	<div class="graying"></div>
</div>

<div style="display:none" data-bind="visible: step2">
<!-- ko foreach: dlvrBoxes -->
<div class="bBuyingLineWrap order-delivery-holder">
	<div class="delivery-message red"></div>
	<dl class="bBuyingLine">
		<dt>
			<h2>
				<span data-bind="visible: type === 'self' ">Самовывоз</span>
				<span data-bind="visible: type === 'standart' ">Доставим</span>
				<span data-bind="text: $root.printDate( $data.chosenDate() )"></span>*
			</h2>
			<div style="margin: 8px 0 12px 0;" class="bSelect mFastInpSmall">
				<span data-bind="text: $data.chosenInterval()"></span>
				<div class="bSelect__eArrow"></div>
				<div id="order-interval_standart_rapid-holder" class="bSelect__eDropmenu order-interval-holder">
					<!-- ko foreach : currentIntervals -->
					<div class="order-interval" 
						data-bind=" click: function(data, event) { $root.clickInterval($parent, data, event) } ">
						<span data-bind="text: $data"></span>
					</div>
					<!-- /ko -->
				</div>
			</div>

			<i class="order-delivery_price">
				<!-- ko if: dlvrPrice() > 0 -->
				<span class="red">Стоимость доставки
					<span data-bind="text: printPrice( dlvrPrice() )"></span> 
					<span class="rubl">p</span>
				</span>
				<!-- /ko -->
				<!-- ko if: dlvrPrice() <= 0 -->
				Бесплатно
				<!-- /ko -->
			</i>
		</dt>
		<i>
			<i>
				<dd>
			  		<div>
			    		<p></p>
			    		<ul class="bBuyingDates">
			      			<li data-direction="prev" class="bBuyingDates__eLeft order-delivery_date-control"
			      				data-bind="click: function(data, event) { $root.changeWeek('-1', data, event) }">
			      				<b></b><span class="dow"></span>
			      			</li>
			      			<!-- ko foreach: caclDates -->
							<li class="order-delivery_date"
								data-bind="style: { display: ( $data.week === $parent.curWeek() ) ? $root.cssForDate : 'none' },
										click: function(data, event) { $root.clickDate($parent, data, event) },
										css: { bBuyingDates__eEnable: $data.enable(),
												bBuyingDates__eDisable: (!$data.enable()),
												bBuyingDates__eCurrent: ($data.tstamp == $parent.chosenDate()) }">
								<span data-bind="text: day"></span> <span class="dow" data-bind="text: dayOfWeek"></span>
							</li>
							<!-- /ko -->
			                <li data-direction="next" class="bBuyingDates__eRight order-delivery_date-control"
			                	data-bind="click: function(data, event) { $root.changeWeek('1', data, event) }">
			                	<b></b><span class="dow"></span>
			                </li>
					    </ul>
			  		</div>
				</dd>
			</i>
		</i>
	</dl>

	<i>
		<i>
			<!-- ko foreach: $data.itemList -->	
			<dl class="bBuyingLine">
				<dt data-bind="ifnot: $index()*1">
					<!-- ko if: $parent.type === 'self' -->
					<span data-bind="text: $parent.shop().name"></span>
					<p></p>
					<a class="bBigOrangeButton selectShop" href="#"
					style="font-size: 16px; padding: 6px 30px; border: 1px solid #E26500;"
					data-bind="click: function(data, event) { $root.showShopPopup($parent, data, event) }">Другой магазин</a>
					<!-- /ko -->
                </dt>
				<dd class="order-item-holder">
					<div class="order-item-container">
						<p><span data-bind="text: printPrice( $data.total )"></span> <span class="rubl">p</span></p>
						<p>
							<a class="mBacket" 
							data-bind=" click: function(data, event) { $root.deleteItem($parent, data, event) }">удалить</a>
						</p>
						<img data-bind="attr: {src: $data.image, alt: $data.name }" />

						<span class="bBuyingLine__eInfo">
							<a href="" target="_blank" 
							data-bind="html: name, attr: { href: $data.url }"></a>
							<br/>
							<span>(<span data-bind="text: $data.quantity "></span> шт.)</span>
						</span>
					</div>
				</dd>
			</dl>
			<!-- /ko -->
			<div data-template="#order-delivery_total-template" class="order-delivery_total-holder">
				<div class="bBuyingLineWrap__eSum">Итого с доставкой: 
					<b><span data-bind="text: printPrice( $data.totalPrice() )"></span> <span class="rubl">p</span></b>
				</div>
			</div>
		</i>
	</i>
</div>
<!-- /ko -->

<div style="margin-top: -10px;">*Дату доставки уточнит специалист Контакт-сENTER</div>

<dl class='bBuyingLine mSumm order-total-container' style="margin-top: 0;">
	<dt>
		<a class="motton font14" style="border-color: #4FCBF4; font-weight: bold;" 
		href="<?php echo url_for('cart') ?>" 
		alt="Вернуться в корзину для выбора услуг и увеличения количества товаров"
		title="Вернуться в корзину для выбора услуг и увеличения количества товаров">&lt; Редактировать товары</a>
	</dt>
	<dd>
		<div>
			<span>Сумма всех заказов</span>
			<h3>
				<span data-bind="text: printPrice( totalSum() )"></span>
				<span class="rubl">p</span>
			</h3>
		</div>
	</dd>
</dl>
</div>
</div>
</div>