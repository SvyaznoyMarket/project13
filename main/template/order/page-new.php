<?php
/**
 * @var $page          \View\Order\CreatePage
 * @var $user          \Session\User
 * @var $deliveryData  array
 * @var $productsById \Model\Product\Entity[]
 */
?>

<?

$backLink = $page->url('cart');
foreach (array_reverse($productsById) as $product) {
    /** @var $product \Model\Product\Entity */
    if ($product->getParentCategory() instanceof \Model\Product\Category\Entity) {
        $backLink = $product->getParentCategory()->getLink();
        break;
    }
}
?>


	<!-- temp styles -->
	<style type="text/css">
		.bPointPopup {

			width: 600px;

			height: 400px;

			text-align: left;

			z-index: 10000;
		}

		.bBuyingDatesList{
			position: relative;
		}
	</style>

<!-- Header -->
<div class="bBuyingHead clearfix">
	<a class="bBuyingHead__eLogo" href="<?= $page->url('homepage') ?>"></a>
	
	<div class="bBuyingHead__eTitle">
		<span class="bSubTitle">Оформление заказа</span><br/>
		<span class="bTitle">Финальный шаг :)</span>
	</div>
</div>
<!-- /Header -->



<!-- loader -->
<div data-bind="css: { mLoader: !prepareData() }"></div>
<!-- /loader -->

<!-- Общая обертка оформления заказа -->
<div class="bBuyingSteps" style="display:none" data-bind="style: { display: prepareData() ? 'block' : 'none'}">

	<div class="bBuyingLine"><a class="bBackCart" href="<?= $backLink ?>">&lt; Вернуться к покупкам</a></div>


	 <!-- Order Method -->
	<div class="bBuyingLine mOrderMethod">
		<h2 class="bBuyingSteps__eTitle">Информация о заказе</h2>

		<div class="bBuyingLine__eLeft">Выберите предпочтительный способ</div>

		<div class="bBuyingLine__eRight bInputList" data-bind="foreach: { data: deliveryTypes }">
			<input class="jsCustomRadio bCustomInput mCustomCheckBig" type="radio" name="radio" hidden data-bind="attr: { id: 'method_'+$data.id }" />
			<label class="bCustomLabel mCustomLabelBig mLabelStrong" data-bind="
									text: $data.name,
									states: $data.states,
									click: $root.chooseDeliveryTypes,
									attr: { for: 'method_'+$data.id }">
			</label>
			<p class="bBuyingLine__eDesc" data-bind="text: $data.description"></p>
		</div>
	</div>
	<!-- Order Method -->


	<!-- Delivery boxes -->
	<div data-bind="foreach: { data: deliveryBoxes, as: 'box' }">
		<div class="bBuyingLineWrap">
		    <div class="bBuyingLine">
		        <div class="bBuyingLine__eLeft">

		            <h2 class="bBuyingSteps__eTitle">
		                <span data-bind="text: box.deliveryName+' '+box.choosenDate().name"></span><span data-bind="visible: !hasPointDelivery">*</span>
		            </h2>

		            <div class="bSelectWrap mFastInpSmall">
		                <span class="bSelectWrap_eText" data-bind="text: 'c '+ box.choosenInterval().start + ' до ' + box.choosenInterval().end"></span>
		                <select class="bSelect" data-bind="options: box.choosenDate().intervals,
															value: box.choosenInterval,
															optionsText: function(item) {
																return 'c '+ item.start + ' до ' + item.end;
															}">
		                </select>
		            </div>

		            <div class="bDeliveryPrice" data-bind="visible: !hasPointDelivery">
		                <span class="bDeliveryPrice__eItem mTextColor">Стоимость доставки
		                    <span data-bind="text: box.deliveryPrice === 0 ? 'Бесплатно' : box.deliveryPrice"></span>
		                    <span class="rubl" data-bind="visible: box.deliveryPrice">p</span>
		                </span>

		                <span class="bDeliveryPrice__eItem mFootnote"><em class="bStar">*</em> Дату доставки уточнит специалист<br />Контакт-сENTER</span>
		            </div>

		            <!-- текущий выбранный магазин и кнопка сменить магазин -->
		            <p data-bind="visible: box.hasPointDelivery, text: box.choosenPoint().name"></p>
					<a class="bBigOrangeButton mSelectShop" href="#" data-bind="visible: box.hasPointDelivery,
												text: 'Сменить магазин',
												click: box.changePoint">
					</a>
					<!-- / текущий выбранный магазин и кнопка сменить магазин -->

		        </div>

		        <div class="bBuyingLine__eRight">
		            <!-- Celendar -->
		            <div class="bBuyingDates clearfix">
			            <div class="bBuyingDatesItem mLeft" data-bind="click: box.calendarLeftBtn">
			                <span class="bArrow"></span>
			            </div>

			            <div class="bBuyingDatesWrap">
				            <ul class="bBuyingDatesList" data-bind="foreach: { data: allDatesForBlock, as: 'calendarDay' }, calendarSlider: box.calendarSliderLeft()">
				                <li class="bBuyingDatesItem mDisable" data-bind="css: { mCurrent: calendarDay.value == box.choosenDate().value,
				                												mDisable: !calendarDay.avalible,
				                												mEnable: calendarDay.avalible },
				                												click: function(data){
				                													box.clickCalendarDay(data);
				                												}">
				                    <span class="bBuyingDatesItem__eDayNumber" data-bind="text: ( calendarDay.day === 0 ) ? '' : calendarDay.day"></span> 
				                    <span class="bBuyingDatesItem__eDay" data-bind="text: calendarDay.humanDayOfWeek"></span>
				                </li>
				            </ul>
				        </div>

			            <div class="bBuyingDatesItem mRight" data-bind="click: box.calendarRightBtn">
			                <span class="bArrow"></span>
			            </div>
			        </div>
		            <!-- /Celendar -->

		            <!-- Products -->
		            <!-- ko foreach: { data: products, as: 'product' } -->
		            <div class="bBuyingLine mProductsLine">
		                <div class="bBuyingLine__eLeft"></div>

		                <div class="bBuyingLine__eRight">
		                    <div class="bOrderItems">
		                        <div class="bItemsRow mItemImg">
		                        	<img data-bind="attr: { src: product.productImg }" />
		                        </div>

		                        <div class="bItemsRow mItemInfo">
		                            <a target="_blank" data-bind="text: product.name, attr: { href: product.productUrl }"></a>
		                            <span class="bCountItem">(<span data-bind="text: product.quantity"></span> шт.)</span>
		                        </div>

		                        <div class="bItemsRow mItemRight">
		                        	<a data-bind="attr: { href: product.deleteUrl }, text: 'Удалить'"></a>
		                        </div>

		                        <div class="bItemsRow mItemRight"> <span data-bind="text: window.printPrice(product.price)"></span> <span class="rubl">p</span></div>
		                    </div>
		                </div>
		            </div>
		            <!-- /ko -->
		            <!-- /Products -->
		        </div>
		    </div>

		    <!-- Points popup -->
		    <div class="bPointPopup popup" data-bind="popupShower: box.showPopupWithPoints">
		    	<i class="close" title="Закрыть">Закрыть</i>
				<ul data-bind="foreach: { data: box.pointList, as: 'point'}">
					<li>
						<a data-bind="text: point.name,
									click: function(data) {
										box.selectPoint(data);
									}"></a>
						<span data-bind="text: point.regime"></span>
					</li>
				</ul>
			</div>
			<!-- /Points popup -->

		    <!-- Sum -->
		    <div class="bBuyingLineWrap__eSum">
		        Итого с доставкой:
		        <strong class="bSumPrice"><span data-bind="text: window.printPrice( box.fullPrice + box.deliveryPrice )"></span> <span class="rubl">p</span></strong>
		    </div>
		</div>
	</div>
	<!-- /Delivery boxes -->

	<div class="bBuyingLine mSumm clearfix" data-bind="visible: deliveryBoxes().length">
		<a href="<?= $page->url('cart') ?>" class="bBackCart mOrdeRead">&lt; Редактировать товары</a>

		<div class="bTotalSumm">
			Сумма всех заказов:
			<span class="bTotalSumm__ePrice" data-bind="text: window.printPrice( totalSum() )"></span><span class="rubl">p</span>
		</div>
	</div>



	<!-- Форма заказа -->
	<div class="bBuyingInfo" data-bind="visible: deliveryBoxes().length">
        <h2 class="bBuyingSteps__eTitle">Информация о счастливом получателе</h2>

        <div class="bHeadnote">
            Уже покупали у нас?
            <strong><a id="auth-link" class="underline" href="#">Авторизуйтесь</a></strong>
            и вы сможете использовать ранее введенные данные
        </div>

        <!-- Info about customer -->
        <div class="bBuyingLine mBuyingFields">
            <label for="" class="bBuyingLine__eLeft">Имя получателя*</label>

            <div class="bBuyingLine__eRight">
                <input type="text" id="" class="bBuyingLine__eText mInputLong" name="order[recipient_first_name]" value="" />
            </div>

            <label for="" class="bBuyingLine__eLeft">Фамилия получателя</label>

            <div class="bBuyingLine__eRight">
                <input type="text" id="" class="bBuyingLine__eText mInputLong" name="order[recipient_last_name]" value="" />
            </div>

            <label for="" class="bBuyingLine__eLeft">E-mail</label>

            <div class="bBuyingLine__eRight">
                <input type="text" id="" class="bBuyingLine__eText mInputLong mInput265" name="order[recipient_email]" value="" />

                <div class="bSubscibeCheck bInputList" style="visibility:visible;">
                    <input type="checkbox" name="subscribe" id="subscribe" class="jsCustomRadio bCustomInput mCustomCheckBig" checked hidden />
                    <label class="bCustomLabel mCustomLabelBig" for="subscribe">Хочу знать об интересных<br/>предложениях</label>                 
                </div>
            </div>

            <label for="" class="bBuyingLine__eLeft">Телефон для связи*</label>

            <div class="bBuyingLine__eRight mPhone">
                <span class="bPlaceholder">+7</span> 
                <input id="" class="bBuyingLine__eText mInputLong" name="order[recipient_phonenumbers]" value="" />
            </div>

            <!-- Address customer -->
            <label class="bBuyingLine__eLeft">Адрес доставки*</label>

            <div class="bBuyingLine__eRight" style="width: 640px;">
                <div>
                    <strong>Москва</strong> ( <a id="jsregion" href="#">изменить</a> )
                </div>

                <div class="bInputAddress">
                    <label class="bPlaceholder">Метро</label>

                    <input class="bBuyingLine__eText mInputLong" id="" type="text" title="Метро" name="order[address_metro]" />
                    <input type="hidden" name="order[subway_id]" />
                </div>

                <div class="bInputAddress">
                    <label class="bPlaceholder">Улица</label>

                    <input type="text" class="bBuyingLine__eText mInputLong mInputStreet" name="order[address_street]" value="" />
                </div>

                <div class="bInputAddress">
                    <label class="bPlaceholder">Дом</label>

                    <input type="text" class="bBuyingLine__eText mInputShort mInputBuild" name="order[address_building]" value="" />
                </div>

                <div class="bInputAddress">
                    <label class="bPlaceholder">Корпус</label>

                    <input type="text" class="bBuyingLine__eText mInputShort mInputNumber" name="order[address_number]" value="" />
                </div>

                <div class="bInputAddress">
                    <label class="bPlaceholder">Квартира</label>

                    <input type="text" class="bBuyingLine__eText mInputShort mInputApartament" name="order[address_apartment]" value="" />
                </div>

                <div class="bInputAddress">
                    <label class="bPlaceholder">Этаж</label>

                    <input type="text" class="bBuyingLine__eText mInputShort mInputFloor" name="order[address_floor]" value="" />
                </div>
            </div>

            <label class="bBuyingLine__eLeft">Пожелания и дополнения</label>

            <div class="bBuyingLine__eRight">
                <textarea id="" class="bBuyingLine__eTextarea" name="[extra]" cols="30" rows="4"></textarea>
            </div>
        </div>

        <!-- Methods of payment -->
        <h2 class="bBuyingSteps__eTitle">Оплата</h2>

        <div class="bBuyingLine mPayMethods">
            <div class="bBuyingLine__eLeft"></div>
            <div class="bBuyingLine__eRight bInputList">

                <h2 class="bTitle">При получении заказа</h2>

                <div class="bPayMethod">
                    <input class="jsCustomRadio bCustomInput mCustomRadioBig" id="cash_pay" type="radio" name="radio_pay" checked hidden />

                    <label for="cash_pay" class="bCustomLabel mCustomLabelRadioBig">
                        Оплата наличными
                    </label>

                    <div class="bPayMethodDesc">Оплатить товар наличными вы можете и при курьерской доставке, и если решили забрать товар из ближайшего магазина Enter самостоятельно.</div>
                </div>

                <div class="bPayMethod">
                    <input class="jsCustomRadio bCustomInput mCustomRadioBig" id="card_pay" type="radio" name="radio_pay" hidden />

                    <label for="card_pay" class="bCustomLabel mCustomLabelRadioBig">
                        Оплата банковской картой
                    </label>

                    <div class="bPayMethodDesc">Наши курьеры принимают к оплате не только наличные, но и банковские карты. Также картой можно заплатить при получении заказа в магазине.</div>
                </div>

                <h2 class="bTitle">Прямо сейчас</h2>

                <div class="bPayMethod">
                    <input class="jsCustomRadio bCustomInput mCustomRadioBig" id="online_pay" name="order[payment_method_id]" type="radio" hidden />

                    <label for="online_pay" class="bCustomLabel mCustomLabelRadioBig">
                        Онлайн оплата
                    </label>

                    <div class="bPayMethodDesc">Вы можете оплатить ваш заказ прямо сейчас. К оплате принимаются банковские карты платежных систем Visa, MasterCard, Diners Club, JCB. Услуга бесплатная, никаких дополнительных процентов вы не платите.</div>
                </div>

                <div class="bPayMethod">
                    <input class="jsCustomRadio bCustomInput mCustomRadioBig" id="qiwi_pay" name="order[payment_method_id]" type="radio" hidden />

                    <label for="qiwi_pay" class="bCustomLabel mCustomLabelRadioBig">
                        Qiwi
                    </label>

                    <div class="bPayMethodDesc">Вы можете оплатить ваш заказ прямо сейчас.</div>

                    <div style="display: none;" class="bPayMethodAction">
                        <input name="order[cardnumber]" type="text" class="bBuyingLine__eText cardNumber" placeholder="Номер телефона">
                    </div>
                </div>

                <div class="bPayMethod">
                    <input class="jsCustomRadio bCustomInput mCustomRadioBig" id="electron_pay" name="order[payment_method_id]" type="radio" hidden />

                    <label for="electron_pay" class="bCustomLabel mCustomLabelRadioBig">
                        Выставить электронный счёт в личный кабинет Промсвязьбанка
                    </label>

                    <div class="bPayMethodDesc">Вы можете оплатить Ваш заказ путём выставления счёта в Ваш личный кабинет. Данная услуга доступна только для клиентов Промсвязьбанка.</div>
                </div>

                <div class="bPayMethod mMethodOption">
                    <input class="jsCustomRadio bCustomInput mCustomRadioBig" id="on_credit" name="order[payment_method_id]" type="radio" hidden />

                    <label for="on_credit" class="bCustomLabel mCustomLabelRadioBig"> 
                        Покупка в кредит (онлайн)
                    </label>

                    <div class="bPayMethodDesc">Оформление онлайн-кредита без залога и поручителей, в любое время дня и ночи. Решение банка будет выслано SMS  в течение 2 минут. Товар и документы мы доставим сами!</div>

                    <div style="display: none;" class="bPayMethodAction">
                        <div>Выберите банк:</div>

                        <div class="bBankWrap">
                            <div class="bSelectWrap mFastInpSmall">
                                <span class="bSelectWrap_eText">Тинькофф</span>
                                <select class="bSelect mFastInpSmall">
                                    <option ref="1" class="bSelect_eItem" selected>Тинькофф</option>
                                    <option ref="2" class="bSelect_eItem">Ренессанс</option>
                                </select>
                            </div>

                            <a class="bBankLink" target="_blank" href="#">Условия кредита <span>(Тинькофф)</span></a>
                        </div>

                        <strong>Ежемесячный платеж<sup>**</sup>:
                            <span>406</span> <span class="rubl"> p</span>
                        </strong>

                        <div class="bFootenote">
                            <sup>**</sup> Кредит не распространяется на услуги F1 и доставку. Сумма платежей предварительная и уточняется банком в процессе принятия кредитного решения.
                        </div>
                    </div>
                </div>
                
                <div class="bPayMethod mMethodOption">
                    <input class="jsCustomRadio bCustomInput mCustomRadioBig" id="serteficat_pay" name="order[payment_method_id]" type="radio" hidden />

                    <label for="serteficat_pay" class="bCustomLabel mCustomLabelRadioBig">
                        Подарочный сертификат
                    </label>

                    <div class="bPayMethodDesc">Вы можете оплатить ваш заказ путем погашения подарочного сертификата.</div>
                    <div class="orderFinal__certificate bPayMethodAction hidden innerType">
                        <div id="sertificateFields">
                            <input type="text" class="bBuyingLine__eText cardNumber" placeholder="Номер" />
                            <input type="text" class="bBuyingLine__eText cardPin" placeholder="ПИН" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bBuyingLine">
            <div class="bBuyingLine__eLeft"></div>

            <div class="bBuyingLine__eRight bInputList">

                <!-- Privacy and policy -->
                <input class="jsCustomRadio bCustomInput mCustomCheckBig" type="checkbox" name="radio" hidden id="pp"/>
                <label class="bCustomLabel mCustomLabelBig" for="pp">
                    Я ознакомлен и согласен с «<a href="/terms" target="_blank">Условиями продажи</a>» и «<a href="/legal" target="_blank">Правовой информацией</a>
                </label>

                <p class="bFootenote">* Поля обязательные для заполнения</p>

                <div><a class="bBigOrangeButton" href="#">Завершить оформление</a></div>
            </div>
        </div>
    </div>
	<!-- /Форма заказа -->
	


	<!-- Point popup -->
	<div class="bPointPopup popup" data-bind="popupShower: showPopupWithPoints">
		<i class="close" title="Закрыть">Закрыть</i>
		<h2 data-bind="text: popupWithPoints().header"></h2>
		<ul data-bind="foreach: { data: popupWithPoints().points }">
			<li>
				<a data-bind="text: $data.name, click: $root.selectPoint"></a>
				<span data-bind="text: $data.regime"></span>
			</li>
		</ul>
	</div>
	<!-- /Point popup -->

</div>
<!-- /Общая обертка оформления заказа -->

<div id="jsOrderDelivery" data-value="<?= $page->json($deliveryData) ?>"></div>
