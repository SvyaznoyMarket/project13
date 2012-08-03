<div id="OrderView">

<dl class="bBuyingLine">
	<dt>Выберите предпочтительный способ</dt>
	<dd>
		<div>
			<p></p>
			<label class="" for="order_delivery_type_id_1">
				<b></b> Доставка заказа курьером
				<input type="radio" name="order[delivery_type_id]" class="bBuyingLine__eRadio" id="order_delivery_type_id_1"
				value="1" autocomplete="off"/>
			</label>
			<i>Мы привезем заказ по любому удобному вам адресу. Пожалуйста, укажите дату и время доставки.</i>
		</div>
		<div>
			<p></p>
			<label class="" for="order_delivery_type_id_3"><b></b>
				Самостоятельно заберу в магазине
				<input type="radio" name="order[delivery_type_id]" class="bBuyingLine__eRadio" id="order_delivery_type_id_3"
				value="3" autocomplete="off"/>
			</label>
			<i>Вы можете самостоятельно забрать товар из ближайшего к вам магазина Enter. Услуга бесплатная! Пожалуйста, выберите магазин.</i>
		</div>
		<div class="pl20 pt5">
			<a href="#" style="display: none; font-size: 16px; padding: 6px 30px;" class="bBigOrangeButton order-shop-button">Выберите магазин</a>
		</div>

	</dd>
</dl>

<!-- ko foreach: dlvrBoxes -->
<div class="bBuyingLineWrap order-delivery-holder">
	<div class="delivery-message red"></div>
	<dl class="bBuyingLine">
		<dt>
			<h2>
				Доставим <span data-bind="text: $data.displayDate()"></span>*
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
			      				<b></b><span></span>
			      			</li>
			      			<!-- ko foreach: caclDates -->
							<li class="order-delivery_date"
								data-bind="style: { display: ( $data.week === $root.curWeek() ) ? 'inline-block' : 'none' },
										click: function(data, event) { $root.clickDate($parent, data, event) },
										css: { bBuyingDates__eEnable: $data.enable(),
												bBuyingDates__eDisable: (!$data.enable()),
												bBuyingDates__eCurrent: ($data.tstamp == $parent.chosenDate()) }">
								<da data-bind="text: day"></da> <span data-bind="text: dayOfWeek"></span>
							</li>
							<!-- /ko -->
			                <li data-direction="next" class="bBuyingDates__eRight order-delivery_date-control"
			                	data-bind="click: function(data, event) { $root.changeWeek('1', data, event) }">
			                	<b></b><span></span>
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
				<dt data-bind="attr: {ref: $index}, ifnot: $index()*1">
					<!-- ko if: $parent.type === 'self' -->
					<span data-bind="text: $parent.shop.name"></span>
					<p></p>
					<a class="bBigOrangeButton order-shop-button" href="#"
					style="font-size: 16px; padding: 6px 30px; border: 1px solid #E26500;">Другой магазин</a>
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

<div style="margin-top: -10px;">*Дату доставки уточнит специалист Контакт Centra</div>

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