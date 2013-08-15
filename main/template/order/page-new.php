<?php
/**
 * @var $page           \View\Order\CreatePage
 * @var $user           \Session\User
 * @var $form           \View\Order\NewForm\Form
 * @var $deliveryData   array
 * @var $productsById   \Model\Product\Entity[]
 * @var $paymentMethods \Model\PaymentMethod\Entity[]
 * @var $subways        \Model\Subway\Entity[]
 * @var $banks          \Model\CreditBank\Entity[]
 * @var $creditData     array
 */
?>

<?
$helper = new \Helper\TemplateHelper();
$region = $user->getRegion();
$isCorporative = $user->getEntity() && $user->getEntity()->getIsCorporative();

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
		.mError{
			border-color: red;
		}
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
		<div class="bBuyingLineWrap clearfix">
			<div class="bBuyingLine">
				<div class="bBuyingLine__eLeft">

					<h2 class="bBuyingSteps__eTitle">
						<span data-bind="text: box.deliveryName+' '+box.choosenDate().name"></span><span data-bind="visible: !hasPointDelivery">*</span>
					</h2>

					<!-- текущий выбранный магазин и кнопка сменить магазин -->
					
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

					<div class="bDeliveryDate">
						1 августа, четверг<em class="bStar">*</em>
					</div>

					<div class="bSelectWrap mFastInpSmall">
						<span class="bSelectWrap_eText" data-bind="text: 'c '+ box.choosenInterval().start + ' до ' + box.choosenInterval().end"></span>
						<select class="bSelect" data-bind="options: box.choosenDate().intervals,
															value: box.choosenInterval,
															optionsText: function(item) {
																return 'c '+ item.start + ' до ' + item.end;
															}">
						</select>
					</div>

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
								</div>

								<div class="bItemsRow mCountItem">
									<span data-bind="text: product.quantity"></span> шт.
								</div>

								<div class="bItemsRow mDelItem">
									<a class="bDelItem" data-bind="attr: { href: product.deleteUrl }, text: 'удалить'"></a>
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

			<div class="bFootnote">* Дату доставки уточнит специалист Контакт-сENTER</div>

			<!-- Sum -->
			<ul class="bSumOrderInfo">
				<li class="bSumOrderInfo__eLine">
					Доставка:&nbsp;&nbsp;
					
					<span class="bDelivery" data-bind="visible: !hasPointDelivery">
						<span data-bind="text: box.deliveryPrice === 0 ? 'Бесплатно' : box.deliveryPrice"></span>
						<span class="rubl" data-bind="visible: box.deliveryPrice">p</span>
					</span>

					<span class="bDelivery" data-bind="visible: box.hasPointDelivery, text: box.choosenPoint().name"></span>
				</li>

				<li class="bSumOrderInfo__eLine">
					Итого с доставкой:&nbsp;&nbsp;

					<span class="bDelivery">
						<span data-bind="text: window.printPrice( box.fullPrice + box.deliveryPrice )"></span> 
						<span class="rubl">p</span>
					</span>
				</li>
			</ul>
			<!-- /Sum -->
		</div>
	</div>
	<!-- /Delivery boxes -->

    <!-- Sale section -->
    <div class="bBuyingLineWrap  bBuyingSale clearfix">
	    <div class="bBuyingLine">
	        <div class="bBuyingLine__eLeft">
	        	<h2 class="bBuyingSteps__eTitle">
					Скидки
				</h2>

	            Если у вас есть карта<br/>
				Enter SPA или купон,<br/>
				укажите номер и получите<br/>
				скидку.
	        </div>

	        <div class="bBuyingLine__eRight">
	            <div class="bSaleData">

	                <div class="bTitle">Вид скидки:</div>

	                <ul class="bSaleList bInputList clearfix">
	                    <li class="bSaleList__eItem">
	                        <input class="jsCustomRadio bCustomInput mCustomRadioBig" type="radio" id="svz_club" name="add_sale" hidden />
	                        <label class="bCustomLabel mCustomLabelRadioBig" for="svz_club">Купон</label>
	                    </li>

	                    <li class="bSaleList__eItem mEnterSpa">
	                        <input class="jsCustomRadio bCustomInput mCustomRadioBig" type="radio" id="black_card" name="add_sale" hidden />
	                        <label class="bCustomLabel mCustomLabelRadioBig" for="black_card">Enter Spa</label>
	                    </li>
	                </ul>

	                <input class="bBuyingLine__eText mSaleInput" type="text" id="" />

	                <button class="bBigOrangeButton mSaleBtn">Применить</button>

	                <p class="bSaleError"> Невозможно применить скидку:<br/>Слишком низкая общая стоимость товаров в корзине </p>
	            </div>

	            <div class="bSaleCheck"></div>

	             <!-- Products -->
				<div class="bBuyingLine mProductsLine">
					<div class="bOrderItems">
						<div class="bItemsRow mItemImg"></div>

						<div class="bItemsRow mItemInfo">
							Для заказа действует скидка «КЦ 300»
						</div>

						<div class="bItemsRow mCountItem"></div>

						<div class="bItemsRow mDelItem">
							<a class="bDelItem" href="">удалить</a>
						</div>

						<div class="bItemsRow mItemRight"> -300 <span class="rubl">p</span></div>
					</div>
				</div>
				<!-- /Products -->
	        </div>

	        <!-- Sum -->
			<ul class="bSumOrderInfo">
				<li class="bSumOrderInfo__eLine">
					<span class="bDelivery  mOldPrice">
						<span data-bind="">2 345</span> 
						<span class="rubl">p</span>
					</span>
				</li>

				<li class="bSumOrderInfo__eLine">
					Сумма заказа с учетом скидок:&nbsp;&nbsp;

					<span class="bDelivery">
						<span data-bind="">2 345</span> 
						<span class="rubl">p</span>
					</span>
				</li>
			</ul>
			<!-- /Sum -->
	    </div>
	</div>
	<!-- /Sale section -->

	<div class="bBuyingLine mSumm clearfix" data-bind="visible: deliveryBoxes().length">
		<a href="<?= $page->url('cart') ?>" class="bBackCart mOrdeRead">&lt; Редактировать товары</a>

		<div class="bTotalSumm">
			Сумма всех заказов:
			<span class="bTotalSumm__ePrice" data-bind="text: window.printPrice( totalSum() )"></span> <span class="rubl">p</span>
		</div>
	</div>

	<!-- Форма заказа -->
	<div class="bBuyingInfo" data-bind="visible: deliveryBoxes().length">
		<h2 class="bBuyingSteps__eTitle">Информация о счастливом получателе</h2>

		<div class="bHeadnote">
			Уже покупали у нас?
			<strong><a id="auth-link" class="underline" href="<?= $page->url('user.login') ?>">Авторизуйтесь</a></strong>
			и вы сможете использовать ранее введенные данные
		</div>
		
		<form id="order-form" action="/orders/new" method="post">
			<!-- Info about customer -->
			<div class="bBuyingLine mBuyingFields">
				<label for="" class="bBuyingLine__eLeft">Имя получателя*</label>
				<div class="bBuyingLine__eRight">
					<input type="text" id="order_recipient_first_name" class="bBuyingLine__eText mInputLong" name="order[recipient_first_name]" value="" />
				</div>

				<label for="" class="bBuyingLine__eLeft">Фамилия получателя*</label>
				<div class="bBuyingLine__eRight">
					<input type="text" id="order_recipient_last_name" class="bBuyingLine__eText mInputLong" name="order[recipient_last_name]" value="" />
				</div>

				<label for="" class="bBuyingLine__eLeft">E-mail<? if ('emails' == \App::abTest()->getCase()->getKey()): ?>*<? endif ?></label>
				<div class="bBuyingLine__eRight">
					<input type="text" id="order_recipient_email" class="bBuyingLine__eText mInputLong mInput265" name="order[recipient_email]" value="" />

					<div class="bSubscibeCheck bInputList" style="visibility:visible;">
						<input type="checkbox" name="subscribe" id="subscribe" class="jsCustomRadio bCustomInput mCustomCheckBig" checked hidden />
						<label class="bCustomLabel mCustomLabelBig" for="subscribe">Хочу знать об интересных<br/>предложениях</label>                 
					</div>
				</div>

				<label for="" class="bBuyingLine__eLeft">Телефон для связи*</label>
				<div class="bBuyingLine__eRight mPhone">
					<span class="bPlaceholder">+7</span> 
					<input type="text" id="order_recipient_phonenumbers" class="bBuyingLine__eText mInputLong" name="order[recipient_phonenumbers]" value="" />
				</div>

				<!-- Address customer -->
				<label class="bBuyingLine__eLeft">Адрес доставки*</label>
				<div class="bBuyingLine__eRight" style="width: 640px;">
					<div>
						<strong><?= $region->getName() ?></strong> ( <a id="jsregion" href="<?= $page->url('region.change', ['regionId' => $region->getId()]) ?>">изменить</a> )
					</div>

	                <? if ((bool)$subways): ?>
					<div class="bInputAddress ui-css" data-bind="visible: hasHomeDelivery()">
						<label class="bPlaceholder">Метро*</label>
						<input type="text" class="bBuyingLine__eText mInputLong ui-autocomplete-input" id="order_address_metro" title="Метро" aria-haspopup="true" aria-autocomplete="list" role="textbox" autocomplete="off" name="order[address_metro]" />
						<div id="metrostations" data-name="<?= $page->json(array_map(function(\Model\Subway\Entity $subway) { return ['val' => $subway->getId(), 'label' => $subway->getName()]; }, $subways)) ?>"></div>
						<input type="hidden" id="order_subway_id" name="order[subway_id]" value="" />
					</div>
	                <? endif ?>
					
					<div class="bInputAddress">
						<label class="bPlaceholder">Улица*</label>
						<input type="text" id="order_address_street" class="bBuyingLine__eText mInputLong mInputStreet" name="order[address_street]" value="" />
					</div>

					<div class="bInputAddress">
						<label class="bPlaceholder">Дом*</label>
						<input type="text" id="order_address_building" class="bBuyingLine__eText mInputShort mInputBuild" name="order[address_building]" value="" />
					</div>

					<div class="bInputAddress">
						<label class="bPlaceholder">Корпус</label>
						<input type="text" id="order_address_number" class="bBuyingLine__eText mInputShort mInputNumber" name="order[address_number]" value="" />
					</div>

					<div class="bInputAddress">
						<label class="bPlaceholder">Квартира</label>
						<input type="text" id="order_address_apartment" class="bBuyingLine__eText mInputShort mInputApartament" name="order[address_apartment]" value="" />
					</div>

					<div class="bInputAddress">
						<label class="bPlaceholder">Этаж</label>
						<input type="text" id="order_address_floor" class="bBuyingLine__eText mInputShort mInputFloor" name="order[address_floor]" value="" />
					</div>
				</div>

				<label class="bBuyingLine__eLeft">Пожелания и дополнения</label>
				<div class="bBuyingLine__eRight">
					<textarea id="order_extra" class="bBuyingLine__eTextarea" name="order[extra]" cols="30" rows="4"></textarea>
				</div>
			</div>

			<!-- Methods of payment -->
			<h2 class="bBuyingSteps__eTitle">Оплата</h2>

			<div class="bBuyingLine mPayMethods">
				<div class="bBuyingLine__eLeft"></div>
				<div class="bBuyingLine__eRight bInputList">
	                <?= $helper->render('order/newForm/__paymentMethod', ['form' => $form, 'paymentMethods' => $paymentMethods, 'banks' => $banks, 'creditData' => $creditData]) ?>
				</div>
			</div>

			<div class="bBuyingLine">
				<div class="bBuyingLine__eLeft"></div>

				<div class="bBuyingLine__eRight bInputList">

					<!-- Privacy and policy -->
					<input class="jsCustomRadio bCustomInput mCustomCheckBig" type="checkbox" name="order[agreed]" hidden id="order_agreed"/>
					<label class="bCustomLabel mCustomLabelBig" for="order_agreed">
						Я ознакомлен и согласен с «<a href="<?= $isCorporative ? '/corp-terms' : '/terms' ?>" target="_blank">Условиями продажи</a>» и «<a href="/legal" target="_blank">Правовой информацией</a>»*
					</label>

					<p class="bFootenote">* Поля обязательные для заполнения</p>

					<div>
						<a id="completeOrder" class="bBigOrangeButton" href="#">Завершить оформление</a>
					</div>
				</div>
			</div>
		</form>
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
<div id="jsForm" data-value="<?= $page->json([
    'order[recipient_first_name]'   => $form->getFirstName(),
    'order[recipient_last_name]'    => $form->getLastName(),
    'order[recipient_email]'        => $form->getEmail(),
    'order[recipient_phonenumbers]' => $form->getMobilePhone(),
    'order[subway_id]'              => $form->getSubwayId(),
    'order[address_street]'         => $form->getAddressStreet(),
    'order[address_number]'         => $form->getAddressNumber(),
    'order[address_building]'       => $form->getAddressBuilding(),
    'order[address_floor]'          => $form->getAddressFloor(),
    'order[address_apartment]'      => $form->getAddressApartment(),
    'order[payment_method_id]'      => $form->getPaymentMethodId(),
]) ?>"></div>

<?php if (\App::config()->analytics['enabled']): ?>
    <div id="marketgidOrder" class="jsanalytics"></div>
    <?= $page->tryRender('order/_kissmetrics-create') ?>

    <?= $page->tryRender('order/partner-counter/_cityads-create') ?>
    <?= $page->tryRender('order/partner-counter/_reactive-create') ?>
    <?= $page->tryRender('order/partner-counter/_ad4u-create') ?>
<?php endif ?>