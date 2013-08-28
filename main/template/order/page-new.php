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
$paypalECS = isset($paypalECS) && (true === $paypalECS);
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
<div class="mLoader" data-bind="visible: !prepareData()"></div>
<!-- /loader -->

<!-- Общая обертка оформления заказа -->
<div class="bBuyingSteps clearfix" style="display:none" data-bind="style: { display: prepareData() ? 'block' : 'none'}">

	<div class="bBuyingLine"><a class="bBackCart" href="<?= $backLink ?>">&lt; Вернуться к покупкам</a></div>


	 <!-- Order Method -->
	<div class="bBuyingLine clearfix mOrderMethod">
		<h2 class="bBuyingSteps__eTitle">Информация о заказе</h2>

		<div class="bBuyingLine__eLeft">Выберите предпочтительный способ</div>

		<div class="bBuyingLine__eRight bInputList" data-bind="foreach: { data: deliveryTypes }">
			<input class="jsCustomRadio bCustomInput mCustomCheckBig" type="radio" name="radio" hidden data-bind="attr: { 'id': 'method_'+$data.id }" />
			<label class="bCustomLabel mCustomLabelBig mLabelStrong" data-bind="
									text: $data.name,
									states: $data.states,
									click: $root.chooseDeliveryTypes,
									attr: { 'for': 'method_'+$data.id }">
			</label>
			<p class="bBuyingLine__eDesc" data-bind="text: $data.description"></p>
		</div>
	</div>
	<!-- Order Method -->

	<!-- Delivery boxes -->
	<div data-bind="foreach: { data: deliveryBoxes, as: 'box' }">
		<div class="bBuyingLineWrap clearfix">
			<div class="bBuyingLine clearfix">
				<div class="bBuyingLine__eLeft">
					<h2 class="bBuyingSteps__eTitle">
						<span data-bind="text: box.deliveryName"></span>
					</h2>

					<div class="bDeliverySelf"><span data-bind="visible: box.hasPointDelivery, text: box.choosenPoint().name"></span></div>

					<!-- кнопка сменить магазин -->
					<a class="bBigOrangeButton mSelectShop" href="#" data-bind="visible: box.hasPointDelivery,
												text: 'Сменить магазин',
												click: box.changePoint">
					</a>
					<!-- /кнопка сменить магазин -->
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
						<span data-bind="text: box.deliveryName"></span> <strong data-bind="text:box.choosenDate().name"></strong>, <span data-bind="text: box.choosenNameOfWeek"></span> <span data-bind="visible: !hasPointDelivery">*</span>
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
									<img class="bItemsRow__eImgProd" data-bind="attr: { src: product.productImg }" />
								</div>

								<div class="bItemsRow mItemInfo">
									<a target="_blank" data-bind="text: product.name, attr: { href: product.productUrl }"></a>
								</div>

								<div class="bItemsRow mCountItem">
									<span data-bind="text: product.quantity"></span> шт.
								</div>

								<div class="bItemsRow mDelItem">
									<a class="bDelItem" data-bind="attr: { href: product.deleteUrl }, text: 'удалить', click: $root.deleteItem"></a>
								</div>

								<div class="bItemsRow mItemRight"> <span data-bind="text: window.printPrice(product.price)"></span> <span class="rubl">p</span></div>
							</div>
						</div>
					</div>
					<!-- /ko -->
					<!-- /Products -->
				</div>
			</div>

			<div class="bFootnote" data-bind="visible: !hasPointDelivery">* Дату доставки уточнит специалист Контакт-сENTER</div>

			<!-- Sum -->
			<ul class="bSumOrderInfo">
				<li class="bSumOrderInfo__eLine">
					<span data-bind="text: hasPointDelivery ? 'Самовывоз:&nbsp;&nbsp;': 'Доставка:&nbsp;&nbsp;' "></span>
					
					<span class="bDelivery">
						<span data-bind="text: box.deliveryPrice === 0 ? 'Бесплатно' : box.deliveryPrice"></span>
						<span class="rubl" data-bind="visible: box.deliveryPrice">p</span>
					</span>
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

    <? if (\App::config()->coupon['enabled'] || \App::config()->blackcard['enabled']): ?>
	<!-- Sale section -->
	<div class="bBuyingLineWrap bBuyingSale clearfix" data-bind="visible: deliveryBoxes().length, css: { hidden: paypalECS }">
		<div class="bBuyingLine">
			<div class="bBuyingLine__eLeft">
				<h2 class="bBuyingSteps__eTitle">
					Скидки
				</h2>

				Если у вас есть
                <? if (\App::config()->blackcard['enabled']): ?> карта Enter SPA <? endif ?>
                <? if (\App::config()->coupon['enabled'] && \App::config()->blackcard['enabled']): ?> или<? endif ?>
                <? if (\App::config()->coupon['enabled']): ?> купон, <? endif ?>
				укажите номер и получите скидку.
			</div>

			<div class="bBuyingLine__eRight">
				<div class="bSaleData" data-bind="couponsVisible: couponsBox()">

					<div class="bTitle">Вид скидки:</div>
					
					<div class="bSaleData__eEmptyBlock">Скидок больше нет</div>

					<ul class="bSaleList bInputList clearfix">
                        <? if (\App::config()->coupon['enabled']): ?>
						<li class="bSaleList__eItem" data-type="coupon">
							<input value="<?= $page->url('cart.coupon.apply') ?>" class="jsCustomRadio bCustomInput mCustomRadioBig" type="radio" id="svz_club" name="add_sale" hidden data-bind="checked: couponUrl" />
							<label class="bCustomLabel mCustomLabelRadioBig" for="svz_club">Купон</label>
						</li>
                        <? endif ?>

                        <? if (\App::config()->blackcard['enabled']): ?>
						<li class="bSaleList__eItem mEnterSpa" data-type="blackcard">
							<input value="<?= $page->url('cart.blackcard.apply') ?>" class="jsCustomRadio bCustomInput mCustomRadioBig" type="radio" id="black_card" name="add_sale" hidden data-bind="checked: couponUrl" />
							<label class="bCustomLabel mCustomLabelRadioBig" for="black_card">Enter Spa</label>
						</li>
                        <? endif ?>
					</ul>

					<input class="bBuyingLine__eText mSaleInput" type="text" id="" data-bind="value: couponNumber, valueUpdate: 'afterkeydown' " />

					<button class="bBigOrangeButton mSaleBtn" data-bind="click: checkCoupon">Применить</button>

					<p class="bSaleError" data-bind="text: couponError"></p>
				</div>

				<div class="bSaleCheck"></div>

				 <!-- Coupons -->
				<div class="bBuyingLine mCouponsLine" data-bind="foreach: { data: couponsBox(), as: 'coupon' }">
					<div class="bOrderItems">
						<div class="bItemsRow mItemImg" data-bind="css: { mError: coupon.error }"></div>

						<div class="bItemsRow mItemInfo" data-bind="text: (coupon.error && coupon.error.message) || coupon.name"></div>

						<div class="bItemsRow mCountItem"></div>

						<div class="bItemsRow mDelItem">
							<a class="bDelItem" data-bind="attr: { 'href': coupon.deleteUrl }, click: $root.deleteItem">удалить</a>
						</div>

						<div class="bItemsRow mItemRight" data-bind="visible: !coupon.error"><span data-bind="text: coupon.sum"></span> <span class="rubl">p</span></div>
					</div>
				</div>
				<!-- /Coupons -->
			</div>

			<!-- Sum -->
			<!-- <ul class="bSumOrderInfo">
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
			</ul> -->
			<!-- /Sum -->
		</div>
	</div>
	<!-- /Sale section -->
    <? endif ?>

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
		
		<form id="order-form" action="<?= $paypalECS ? $page->url('order.paypal.create') : $page->url('order.create') ?>" method="post">
			<!-- Info about customer -->
			<div class="bBuyingLine mBuyingFields">
				<label for="" class="bBuyingLine__eLeft">Имя получателя*</label>
				<div class="bBuyingLine__eRight">
					<input type="text" id="order_recipient_first_name" class="bBuyingLine__eText mInputLong" name="order[recipient_first_name]" value="" />
				</div>

				<label for="" class="bBuyingLine__eLeft">Фамилия получателя</label>
				<div class="bBuyingLine__eRight">
					<input type="text" id="order_recipient_last_name" class="bBuyingLine__eText mInputLong" name="order[recipient_last_name]" value="" />
				</div>

				<label for="" class="bBuyingLine__eLeft">E-mail<? if ('emails' == \App::abTest()->getCase()->getKey()): ?>*<? endif ?></label>
				<div class="bBuyingLine__eRight">
					<input type="text" id="order_recipient_email" class="bBuyingLine__eText mInputLong mInput265" name="order[recipient_email]" value="" />

					<div class="bSubscibeCheck bInputList">
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
				<label class="bBuyingLine__eLeft" style="min-height: 10px;" data-bind="style: { display: hasHomeDelivery() ? 'block' : 'none'}">Адрес доставки*</label>
				<div class="bBuyingLine__eRight" style="width: 640px;" data-bind="style: { display: hasHomeDelivery() ? 'block' : 'none'}">
					<div class="bSelectedCity">
						<strong><?= $region->getName() ?></strong> (<a class="jsChangeRegion" href="<?= $page->url('region.change', ['regionId' => $region->getId()]) ?>">изменить</a>)
					</div>

					<? if ((bool)$subways): ?>
					<div class="bInputAddress ui-css">
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

				<div class="<? if ($isCorporative): ?> hidden<? endif ?>">
					<div class="bBuyingLine__eLeft">Если у вас есть карта &laquo;Связной-Клуб&raquo;, вы можете указать ее номер</div>
					<div class="bBuyingLine__eRight mSClub">
						<input id="sclub-number" type="text" class="bBuyingLine__eText" name="order[sclub_card_number]" />
						<div class="bText">Чтобы получить 1% от суммы заказа<br/>плюсами на карту, введите ее номер,<br/>расположенный на обороте под штрихкодом</div>
					</div>
				</div>
			</div>

			<!-- Methods of payment -->
			<h2 class="bBuyingSteps__eTitle" data-bind="css: { hidden: paypalECS }">Оплата</h2>

			<div class="bBuyingLine clearfix mPayMethods" data-bind="css: { hidden: paypalECS }">
				<div class="bBuyingLine__eLeft"></div>
				<div class="bBuyingLine__eRight bInputList">
					<?= $helper->render('order/newForm/__paymentMethod', ['form' => $form, 'paymentMethods' => $paymentMethods, 'banks' => $banks, 'creditData' => $creditData]) ?>
				</div>
			</div>

			<div class="bBuyingLine clearfix">
				<div class="bBuyingLine__eLeft"></div>

				<div class="bBuyingLine__eRight bInputList mRules">

					<!-- Privacy and policy -->
					<input class="jsCustomRadio bCustomInput mCustomCheckBig" type="checkbox" name="order[agreed]" hidden id="order_agreed"/>

					<label class="bCustomLabel mCustomLabelBig" for="order_agreed">
						Я ознакомлен и согласен с «<a href="<?= $isCorporative ? '/corp-terms' : '/terms' ?>" target="_blank">Условиями продажи</a>» и «<a href="/legal" target="_blank">Правовой информацией</a>»*
					</label>

					<p class="bFootenote">* Поля обязательные для заполнения</p>

					<div>
						<a
                            id="completeOrder"
                            class="bBigOrangeButton"
                            href="#"
                            <? if ($paypalECS): ?>data-alt-text="Подтвердить сумму"<? endif ?>
                        >Завершить оформление</a>
					</div>
				</div>
			</div>
		</form>
	</div>
	<!-- /Форма заказа -->
	
	<div id="mapInfoBlock" style="display: none;">
		<div class="bMapShops__ePopupRel">
			<h3>$[properties.name]</h3>
			<span>Работает </span>
			<span>$[properties.regtime]</span>
			<br/>
			<a class="bGrayButton shopchoose" href="#" data-pointid="$[properties.id]" data-parentbox="$[properties.parentBoxToken]">Забрать из этого магазина</a>
		</div>
	</div>

	<!-- Point popup -->
	<div class="bPointPopup popup" data-bind="popupShower: showPopupWithPoints">
		<i class="close" title="Закрыть">Закрыть</i>
		<h2 data-bind="text: popupWithPoints().header"></h2>
        <div>Регион <strong><?= $user->getRegion()->getName() ?></strong> (<a class="jsChangeRegion" href="<?= $page->url('region.change', ['regionId' => $region->getId()]) ?>">изменить</a>)</div>
		<ul class="bPointList" data-bind="foreach: { data: popupWithPoints().points }">
			<li class="bPointInPopup" data-bind="click: $root.selectPoint">
				<div class="bMapShops__eListNum"><img alt="" src="/images/shop.png"></div>
				<div class="bPointInPopup__eName" data-bind="text: $data.name"></div>
				<span data-bind="text: $data.regtime"></span>
			</li>
		</ul>
		<div class="bPointPopupMap" id="pointPopupMap"></div>
	</div>
	<!-- /Point popup -->

</div>
<!-- /Общая обертка оформления заказа -->

<div id="jsOrderDelivery" data-url="<?= $page->url('order.delivery', $paypalECS ? ['paypalECS' => 1] : []) ?>" data-value="<?= $page->json($deliveryData) ?>"></div>
<div id="jsOrderForm" data-value="<?= $page->json([
	'order[recipient_first_name]'   => $form->getFirstName(),
	'order[recipient_last_name]'    => $form->getLastName(),
	'order[recipient_email]'        => $form->getEmail(),
	'order[recipient_phonenumbers]' => substr($form->getMobilePhone(), -10),
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