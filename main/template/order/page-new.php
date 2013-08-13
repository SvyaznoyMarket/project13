<?php
/**
 * @var $page           \View\Order\CreatePage
 * @var $user           \Session\User
 * @var $deliveryData   array
 * @var $productsById   \Model\Product\Entity[]
 * @var $paymentMethods \Model\PaymentMethod\Entity[]
 * @var $banks          \Model\CreditBank\Entity[]
 * @var $creditData     array
 */
?>

<?
$helper = new \Helper\TemplateHelper();

$region = $user->getRegion();

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
			<strong><a id="auth-link" class="underline" href="<?= $page->url('user.login') ?>">Авторизуйтесь</a></strong>
			и вы сможете использовать ранее введенные данные
		</div>

		<!-- Info about customer -->
		<div class="bBuyingLine mBuyingFields">
			<label for="" class="bBuyingLine__eLeft">Имя получателя*</label>
			<div class="bBuyingLine__eRight">
				<input type="text" id="order_recipient_first_name" class="bBuyingLine__eText mInputLong" name="order[recipient_first_name]" value="" />
			</div>

			<label for="" class="bBuyingLine__eLeft">Фамилия получателя*</label>
			<div class="bBuyingLine__eRight">
				<input type="text" id="order_recipient_last_name" class="bBuyingLine__eText mInputLong" name="order[recipient_first_name]" value="" />
			</div>

			<label for="" class="bBuyingLine__eLeft">E-mail*</label>
			<div class="bBuyingLine__eRight">
				<input type="text" id="order_recipient_email" class="bBuyingLine__eText mInputLong mInput265" name="order[recipient_last_name]" value="" />

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
				
				<div class="bInputAddress ui-css" data-bind="visible: hasHomeDelivery()">
					<label class="bPlaceholder">Метро*</label>

					<input type="text" class="bBuyingLine__eText mInputLong ui-autocomplete-input" id="order_address_metro" title="Метро" aria-haspopup="true" aria-autocomplete="list" role="textbox" autocomplete="off" name="order[address_metro]" />
					<div id="metrostations" data-name='[{"val":117,"label":"\u0410\u0432\u0438\u0430\u043c\u043e\u0442\u043e\u0440\u043d\u0430\u044f"},{"val":116,"label":"\u0410\u0432\u0442\u043e\u0437\u0430\u0432\u043e\u0434\u0441\u043a\u0430\u044f"},{"val":6,"label":"\u0410\u043a\u0430\u0434\u0435\u043c\u0438\u0447\u0435\u0441\u043a\u0430\u044f"},{"val":7,"label":"\u0410\u043b\u0435\u043a\u0441\u0430\u043d\u0434\u0440\u043e\u0432\u0441\u043a\u0438\u0439 \u0441\u0430\u0434"},{"val":8,"label":"\u0410\u043b\u0435\u043a\u0441\u0435\u0435\u0432\u0441\u043a\u0430\u044f"},{"val":171,"label":"\u0410\u043b\u043c\u0430-\u0410\u0442\u0438\u043d\u0441\u043a\u0430\u044f"},{"val":9,"label":"\u0410\u043b\u0442\u0443\u0444\u044c\u0435\u0432\u043e"},{"val":10,"label":"\u0410\u043d\u043d\u0438\u043d\u043e"},{"val":11,"label":"\u0410\u0440\u0431\u0430\u0442\u0441\u043a\u0430\u044f"},{"val":12,"label":"\u0410\u044d\u0440\u043e\u043f\u043e\u0440\u0442"},{"val":13,"label":"\u0411\u0430\u0431\u0443\u0448\u043a\u0438\u043d\u0441\u043a\u0430\u044f"},{"val":14,"label":"\u0411\u0430\u0433\u0440\u0430\u0442\u0438\u043e\u043d\u043e\u0432\u0441\u043a\u0430\u044f"},{"val":15,"label":"\u0411\u0430\u0440\u0440\u0438\u043a\u0430\u0434\u043d\u0430\u044f"},{"val":16,"label":"\u0411\u0430\u0443\u043c\u0430\u043d\u0441\u043a\u0430\u044f"},{"val":17,"label":"\u0411\u0435\u0433\u043e\u0432\u0430\u044f"},{"val":18,"label":"\u0411\u0435\u043b\u043e\u0440\u0443\u0441\u0441\u043a\u0430\u044f"},{"val":19,"label":"\u0411\u0435\u043b\u044f\u0435\u0432\u043e"},{"val":20,"label":"\u0411\u0438\u0431\u0438\u0440\u0435\u0432\u043e"},{"val":21,"label":"\u0411\u0438\u0431\u043b\u0438\u043e\u0442\u0435\u043a\u0430 \u0438\u043c.\u041b\u0435\u043d\u0438\u043d\u0430"},{"val":155,"label":"\u0411\u043e\u0440\u0438\u0441\u043e\u0432\u043e"},{"val":23,"label":"\u0411\u043e\u0440\u043e\u0432\u0438\u0446\u043a\u0430\u044f"},{"val":24,"label":"\u0411\u043e\u0442\u0430\u043d\u0438\u0447\u0435\u0441\u043a\u0438\u0439 \u0441\u0430\u0434"},{"val":25,"label":"\u0411\u0440\u0430\u0442\u0438\u0441\u043b\u0430\u0432\u0441\u043a\u0430\u044f"},{"val":26,"label":"\u0411\u0443\u043b\u044c\u0432\u0430\u0440 \u0430\u0434\u043c\u0438\u0440\u0430\u043b\u0430 \u0423\u0448\u0430\u043a\u043e\u0432\u0430"},{"val":27,"label":"\u0411\u0443\u043b\u044c\u0432\u0430\u0440 \u0414\u043c\u0438\u0442\u0440\u0438\u044f \u0414\u043e\u043d\u0441\u043a\u043e\u0433\u043e"},{"val":28,"label":"\u0411\u0443\u043d\u0438\u043d\u0441\u043a\u0430\u044f \u0430\u043b\u043b\u0435\u044f"},{"val":29,"label":"\u0412\u0430\u0440\u0448\u0430\u0432\u0441\u043a\u0430\u044f"},{"val":30,"label":"\u0412\u0414\u041d\u0425"},{"val":31,"label":"\u0412\u043b\u0430\u0434\u044b\u043a\u0438\u043d\u043e"},{"val":32,"label":"\u0412\u043e\u0434\u043d\u044b\u0439 \u0441\u0442\u0430\u0434\u0438\u043e\u043d"},{"val":33,"label":"\u0412\u043e\u0439\u043a\u043e\u0432\u0441\u043a\u0430\u044f"},{"val":34,"label":"\u0412\u043e\u043b\u0433\u043e\u0433\u0440\u0430\u0434\u0441\u043a\u0438\u0439 \u043f\u0440\u043e\u0441\u043f\u0435\u043a\u0442"},{"val":35,"label":"\u0412\u043e\u043b\u0436\u0441\u043a\u0430\u044f"},{"val":36,"label":"\u0412\u043e\u043b\u043e\u043a\u043e\u043b\u0430\u043c\u0441\u043a\u0430\u044f"},{"val":37,"label":"\u0412\u043e\u0440\u043e\u0431\u044c\u0435\u0432\u044b \u0433\u043e\u0440\u044b"},{"val":38,"label":"\u0412\u044b\u0441\u0442\u0430\u0432\u043e\u0447\u043d\u0430\u044f"},{"val":39,"label":"\u0412\u044b\u0445\u0438\u043d\u043e"},{"val":40,"label":"\u0414\u0438\u043d\u0430\u043c\u043e"},{"val":41,"label":"\u0414\u043c\u0438\u0442\u0440\u043e\u0432\u0441\u043a\u0430\u044f"},{"val":165,"label":"\u0414\u043e\u0431\u0440\u044b\u043d\u0438\u043d\u0441\u043a\u0430\u044f"},{"val":42,"label":"\u0414\u043e\u043c\u043e\u0434\u0435\u0434\u043e\u0432\u0441\u043a\u0430\u044f"},{"val":43,"label":"\u0414\u043e\u0441\u0442\u043e\u0435\u0432\u0441\u043a\u0430\u044f"},{"val":44,"label":"\u0414\u0443\u0431\u0440\u043e\u0432\u043a\u0430"},{"val":157,"label":"\u0417\u044f\u0431\u043b\u0438\u043a\u043e\u0432\u043e"},{"val":45,"label":"\u0418\u0437\u043c\u0430\u0439\u043b\u043e\u0432\u0441\u043a\u0430\u044f"},{"val":46,"label":"\u041a\u0430\u043b\u0443\u0436\u0441\u043a\u0430\u044f"},{"val":47,"label":"\u041a\u0430\u043d\u0442\u0435\u043c\u0438\u0440\u043e\u0432\u0441\u043a\u0430\u044f"},{"val":48,"label":"\u041a\u0430\u0445\u043e\u0432\u0441\u043a\u0430\u044f"},{"val":49,"label":"\u041a\u0430\u0448\u0438\u0440\u0441\u043a\u0430\u044f"},{"val":50,"label":"\u041a\u0438\u0435\u0432\u0441\u043a\u0430\u044f"},{"val":51,"label":"\u041a\u0438\u0442\u0430\u0439-\u0433\u043e\u0440\u043e\u0434"},{"val":52,"label":"\u041a\u043e\u0436\u0443\u0445\u043e\u0432\u0441\u043a\u0430\u044f"},{"val":53,"label":"\u041a\u043e\u043b\u043e\u043c\u0435\u043d\u0441\u043a\u0430\u044f"},{"val":54,"label":"\u041a\u043e\u043c\u0441\u043e\u043c\u043e\u043b\u044c\u0441\u043a\u0430\u044f"},{"val":55,"label":"\u041a\u043e\u043d\u044c\u043a\u043e\u0432\u043e"},{"val":56,"label":"\u041a\u0440\u0430\u0441\u043d\u043e\u0433\u0432\u0430\u0440\u0434\u0435\u0439\u0441\u043a\u0430\u044f"},{"val":57,"label":"\u041a\u0440\u0430\u0441\u043d\u043e\u043f\u0440\u0435\u0441\u043d\u0435\u043d\u0441\u043a\u0430\u044f"},{"val":58,"label":"\u041a\u0440\u0430\u0441\u043d\u043e\u0441\u0435\u043b\u044c\u0441\u043a\u0430\u044f"},{"val":59,"label":"\u041a\u0440\u0430\u0441\u043d\u044b\u0435 \u0432\u043e\u0440\u043e\u0442\u0430"},{"val":60,"label":"\u041a\u0440\u0435\u0441\u0442\u044c\u044f\u043d\u0441\u043a\u0430\u044f \u0437\u0430\u0441\u0442\u0430\u0432\u0430"},{"val":61,"label":"\u041a\u0440\u043e\u043f\u043e\u0442\u043a\u0438\u043d\u0441\u043a\u0430\u044f"},{"val":62,"label":"\u041a\u0440\u044b\u043b\u0430\u0442\u0441\u043a\u043e\u0435"},{"val":63,"label":"\u041a\u0443\u0437\u043d\u0435\u0446\u043a\u0438\u0439 \u043c\u043e\u0441\u0442"},{"val":64,"label":"\u041a\u0443\u0437\u044c\u043c\u0438\u043d\u043a\u0438"},{"val":65,"label":"\u041a\u0443\u043d\u0446\u0435\u0432\u0441\u043a\u0430\u044f"},{"val":66,"label":"\u041a\u0443\u0440\u0441\u043a\u0430\u044f"},{"val":67,"label":"\u041a\u0443\u0442\u0443\u0437\u043e\u0432\u0441\u043a\u0430\u044f"},{"val":68,"label":"\u041b\u0435\u043d\u0438\u043d\u0441\u043a\u0438\u0439 \u043f\u0440\u043e\u0441\u043f\u0435\u043a\u0442"},{"val":69,"label":"\u041b\u0443\u0431\u044f\u043d\u043a\u0430"},{"val":70,"label":"\u041b\u044e\u0431\u043b\u0438\u043d\u043e"},{"val":71,"label":"\u041c\u0430\u0440\u043a\u0441\u0438\u0441\u0442\u0441\u043a\u0430\u044f"},{"val":72,"label":"\u041c\u0430\u0440\u044c\u0438\u043d\u0430 \u0440\u043e\u0449\u0430"},{"val":159,"label":"\u041c\u0430\u0440\u044c\u0438\u043d\u043e"},{"val":73,"label":"\u041c\u0430\u044f\u043a\u043e\u0432\u0441\u043a\u0430\u044f"},{"val":74,"label":"\u041c\u0435\u0434\u0432\u0435\u0434\u043a\u043e\u0432\u043e"},{"val":75,"label":"\u041c\u0435\u0436\u0434\u0443\u043d\u0430\u0440\u043e\u0434\u043d\u0430\u044f"},{"val":76,"label":"\u041c\u0435\u043d\u0434\u0435\u043b\u0435\u0435\u0432\u0441\u043a\u0430\u044f"},{"val":77,"label":"\u041c\u0438\u0442\u0438\u043d\u043e"},{"val":78,"label":"\u041c\u043e\u043b\u043e\u0434\u0435\u0436\u043d\u0430\u044f"},{"val":79,"label":"\u041c\u044f\u043a\u0438\u043d\u0438\u043d\u043e"},{"val":80,"label":"\u041d\u0430\u0433\u0430\u0442\u0438\u043d\u0441\u043a\u0430\u044f"},{"val":81,"label":"\u041d\u0430\u0433\u043e\u0440\u043d\u0430\u044f"},{"val":82,"label":"\u041d\u0430\u0445\u0438\u043c\u043e\u0432\u0441\u043a\u0438\u0439 \u043f\u0440\u043e\u0441\u043f\u0435\u043a\u0442"},{"val":83,"label":"\u041d\u043e\u0432\u043e\u0433\u0438\u0440\u0435\u0435\u0432\u043e"},{"val":170,"label":"\u041d\u043e\u0432\u043e\u043a\u043e\u0441\u0438\u043d\u043e"},{"val":84,"label":"\u041d\u043e\u0432\u043e\u043a\u0443\u0437\u043d\u0435\u0446\u043a\u0430\u044f"},{"val":85,"label":"\u041d\u043e\u0432\u043e\u0441\u043b\u043e\u0431\u043e\u0434\u0441\u043a\u0430\u044f"},{"val":86,"label":"\u041d\u043e\u0432\u043e\u044f\u0441\u0435\u043d\u0435\u0432\u0441\u043a\u0430\u044f"},{"val":87,"label":"\u041d\u043e\u0432\u044b\u0435 \u0427\u0435\u0440\u0435\u043c\u0443\u0448\u043a\u0438"},{"val":88,"label":"\u041e\u043a\u0442\u044f\u0431\u0440\u044c\u0441\u043a\u0430\u044f"},{"val":89,"label":"\u041e\u043a\u0442\u044f\u0431\u0440\u044c\u0441\u043a\u043e\u0435 \u043f\u043e\u043b\u0435"},{"val":115,"label":"\u041e\u0440\u0435\u0445\u043e\u0432\u043e"},{"val":90,"label":"\u041e\u0442\u0440\u0430\u0434\u043d\u043e\u0435"},{"val":91,"label":"\u041e\u0445\u043e\u0442\u043d\u044b\u0439 \u0440\u044f\u0434"},{"val":92,"label":"\u041f\u0430\u0432\u0435\u043b\u0435\u0446\u043a\u0430\u044f"},{"val":93,"label":"\u041f\u0430\u0440\u043a \u043a\u0443\u043b\u044c\u0442\u0443\u0440\u044b"},{"val":94,"label":"\u041f\u0430\u0440\u043a \u041f\u043e\u0431\u0435\u0434\u044b"},{"val":95,"label":"\u041f\u0430\u0440\u0442\u0438\u0437\u0430\u043d\u0441\u043a\u0430\u044f"},{"val":96,"label":"\u041f\u0435\u0440\u0432\u043e\u043c\u0430\u0439\u0441\u043a\u0430\u044f"},{"val":97,"label":"\u041f\u0435\u0440\u043e\u0432\u043e"},{"val":98,"label":"\u041f\u0435\u0442\u0440\u043e\u0432\u0441\u043a\u043e-\u0420\u0430\u0437\u0443\u043c\u043e\u0432\u0441\u043a\u0430\u044f"},{"val":99,"label":"\u041f\u0435\u0447\u0430\u0442\u043d\u0438\u043a\u0438"},{"val":100,"label":"\u041f\u0438\u043e\u043d\u0435\u0440\u0441\u043a\u0430\u044f"},{"val":101,"label":"\u041f\u043b\u0430\u043d\u0435\u0440\u043d\u0430\u044f"},{"val":102,"label":"\u041f\u043b\u043e\u0449\u0430\u0434\u044c \u0418\u043b\u044c\u0438\u0447\u0430"},{"val":169,"label":"\u041f\u043b\u043e\u0449\u0430\u0434\u044c \u0420\u0435\u0432\u043e\u043b\u044e\u0446\u0438\u0438"},{"val":103,"label":"\u041f\u043e\u043b\u0435\u0436\u0430\u0435\u0432\u0441\u043a\u0430\u044f"},{"val":104,"label":"\u041f\u043e\u043b\u044f\u043d\u043a\u0430"},{"val":105,"label":"\u041f\u0440\u0430\u0436\u0441\u043a\u0430\u044f"},{"val":106,"label":"\u041f\u0440\u0435\u043e\u0431\u0440\u0430\u0436\u0435\u043d\u0441\u043a\u0430\u044f \u043f\u043b\u043e\u0449\u0430\u0434\u044c"},{"val":107,"label":"\u041f\u0440\u043e\u043b\u0435\u0442\u0430\u0440\u0441\u043a\u0430\u044f"},{"val":108,"label":"\u041f\u0440\u043e\u0441\u043f\u0435\u043a\u0442 \u0412\u0435\u0440\u043d\u0430\u0434\u0441\u043a\u043e\u0433\u043e"},{"val":109,"label":"\u041f\u0440\u043e\u0441\u043f\u0435\u043a\u0442 \u041c\u0438\u0440\u0430"},{"val":110,"label":"\u041f\u0440\u043e\u0444\u0441\u043e\u044e\u0437\u043d\u0430\u044f"},{"val":111,"label":"\u041f\u0443\u0448\u043a\u0438\u043d\u0441\u043a\u0430\u044f"},{"val":173,"label":"\u041f\u044f\u0442\u043d\u0438\u0446\u043a\u043e\u0435 \u0448\u043e\u0441\u0441\u0435"},{"val":112,"label":"\u0420\u0435\u0447\u043d\u043e\u0439 \u0432\u043e\u043a\u0437\u0430\u043b"},{"val":113,"label":"\u0420\u0438\u0436\u0441\u043a\u0430\u044f"},{"val":118,"label":"\u0420\u0438\u043c\u0441\u043a\u0430\u044f"},{"val":119,"label":"\u0420\u044f\u0437\u0430\u043d\u0441\u043a\u0438\u0439 \u043f\u0440\u043e\u0441\u043f\u0435\u043a\u0442"},{"val":120,"label":"\u0421\u0430\u0432\u0435\u043b\u043e\u0432\u0441\u043a\u0430\u044f"},{"val":121,"label":"\u0421\u0432\u0438\u0431\u043b\u043e\u0432\u043e"},{"val":122,"label":"\u0421\u0435\u0432\u0430\u0441\u0442\u043e\u043f\u043e\u043b\u044c\u0441\u043a\u0430\u044f"},{"val":123,"label":"\u0421\u0435\u043c\u0435\u043d\u043e\u0432\u0441\u043a\u0430\u044f"},{"val":124,"label":"\u0421\u0435\u0440\u043f\u0443\u0445\u043e\u0432\u0441\u043a\u0430\u044f"},{"val":125,"label":"\u0421\u043b\u0430\u0432\u044f\u043d\u0441\u043a\u0438\u0439 \u0431\u0443\u043b\u044c\u0432\u0430\u0440"},{"val":126,"label":"\u0421\u043c\u043e\u043b\u0435\u043d\u0441\u043a\u0430\u044f"},{"val":127,"label":"\u0421\u043e\u043a\u043e\u043b"},{"val":128,"label":"\u0421\u043e\u043a\u043e\u043b\u044c\u043d\u0438\u043a\u0438"},{"val":129,"label":"\u0421\u043f\u043e\u0440\u0442\u0438\u0432\u043d\u0430\u044f"},{"val":130,"label":"\u0421\u0440\u0435\u0442\u0435\u043d\u0441\u043a\u0438\u0439 \u0431\u0443\u043b\u044c\u0432\u0430\u0440"},{"val":131,"label":"\u0421\u0442\u0440\u043e\u0433\u0438\u043d\u043e"},{"val":132,"label":"\u0421\u0442\u0443\u0434\u0435\u043d\u0447\u0435\u0441\u043a\u0430\u044f"},{"val":133,"label":"\u0421\u0443\u0445\u0430\u0440\u0435\u0432\u0441\u043a\u0430\u044f"},{"val":134,"label":"\u0421\u0445\u043e\u0434\u043d\u0435\u043d\u0441\u043a\u0430\u044f"},{"val":135,"label":"\u0422\u0430\u0433\u0430\u043d\u0441\u043a\u0430\u044f"},{"val":136,"label":"\u0422\u0432\u0435\u0440\u0441\u043a\u0430\u044f"},{"val":137,"label":"\u0422\u0435\u0430\u0442\u0440\u0430\u043b\u044c\u043d\u0430\u044f"},{"val":138,"label":"\u0422\u0435\u043a\u0441\u0442\u0438\u043b\u044c\u0448\u0438\u043a\u0438"},{"val":139,"label":"\u0422\u0435\u043f\u043b\u044b\u0439 \u0441\u0442\u0430\u043d"},{"val":140,"label":"\u0422\u0438\u043c\u0438\u0440\u044f\u0437\u0435\u0432\u0441\u043a\u0430\u044f"},{"val":141,"label":"\u0422\u0440\u0435\u0442\u044c\u044f\u043a\u043e\u0432\u0441\u043a\u0430\u044f"},{"val":142,"label":"\u0422\u0440\u0443\u0431\u043d\u0430\u044f"},{"val":143,"label":"\u0422\u0443\u043b\u044c\u0441\u043a\u0430\u044f"},{"val":144,"label":"\u0422\u0443\u0440\u0433\u0435\u043d\u0435\u0432\u0441\u043a\u0430\u044f"},{"val":145,"label":"\u0422\u0443\u0448\u0438\u043d\u0441\u043a\u0430\u044f"},{"val":146,"label":"\u0423\u043b\u0438\u0446\u0430 1905 \u0433\u043e\u0434\u0430"},{"val":147,"label":"\u0423\u043b\u0438\u0446\u0430 \u0410\u043a\u0430\u0434\u0435\u043c\u0438\u043a\u0430 \u042f\u043d\u0433\u0435\u043b\u044f"},{"val":148,"label":"\u0423\u043b\u0438\u0446\u0430 \u0413\u043e\u0440\u0447\u0430\u043a\u043e\u0432\u0430"},{"val":149,"label":"\u0423\u043b\u0438\u0446\u0430 \u041f\u043e\u0434\u0431\u0435\u043b\u044c\u0441\u043a\u043e\u0433\u043e"},{"val":167,"label":"\u0423\u043b\u0438\u0446\u0430 \u0421\u043a\u043e\u0431\u0435\u043b\u0435\u0432\u0441\u043a\u0430\u044f"},{"val":150,"label":"\u0423\u043b\u0438\u0446\u0430 \u0421\u0442\u0430\u0440\u043e\u043a\u0430\u0447\u0430\u043b\u043e\u0432\u0441\u043a\u0430\u044f"},{"val":151,"label":"\u0423\u043d\u0438\u0432\u0435\u0440\u0441\u0438\u0442\u0435\u0442"},{"val":152,"label":"\u0424\u0438\u043b\u0435\u0432\u0441\u043a\u0438\u0439 \u043f\u0430\u0440\u043a"},{"val":153,"label":"\u0424\u0438\u043b\u0438"},{"val":2,"label":"\u0424\u0440\u0443\u043d\u0437\u0435\u043d\u0441\u043a\u0430\u044f"},{"val":5,"label":"\u0426\u0430\u0440\u0438\u0446\u044b\u043d\u043e"},{"val":160,"label":"\u0426\u0432\u0435\u0442\u043d\u043e\u0439 \u0431\u0443\u043b\u044c\u0432\u0430\u0440"},{"val":161,"label":"\u0427\u0435\u0440\u043a\u0438\u0437\u043e\u0432\u0441\u043a\u0430\u044f"},{"val":162,"label":"\u0427\u0435\u0440\u0442\u0430\u043d\u043e\u0432\u0441\u043a\u0430\u044f"},{"val":114,"label":"\u0427\u0435\u0445\u043e\u0432\u0441\u043a\u0430\u044f"},{"val":168,"label":"\u0427\u0438\u0441\u0442\u044b\u0435 \u043f\u0440\u0443\u0434\u044b"},{"val":22,"label":"\u0427\u043a\u0430\u043b\u043e\u0432\u0441\u043a\u0430\u044f"},{"val":164,"label":"\u0428\u0430\u0431\u043e\u043b\u043e\u0432\u0441\u043a\u0430\u044f"},{"val":156,"label":"\u0428\u0438\u043f\u0438\u043b\u043e\u0432\u0441\u043a\u0430\u044f"},{"val":166,"label":"\u0428\u043e\u0441\u0441\u0435 \u042d\u043d\u0442\u0443\u0437\u0438\u0430\u0441\u0442\u043e\u0432"},{"val":158,"label":"\u0429\u0435\u043b\u043a\u043e\u0432\u0441\u043a\u0430\u044f"},{"val":3,"label":"\u0429\u0443\u043a\u0438\u043d\u0441\u043a\u0430\u044f"},{"val":4,"label":"\u042d\u043b\u0435\u043a\u0442\u0440\u043e\u0437\u0430\u0432\u043e\u0434\u0441\u043a\u0430\u044f"},{"val":1,"label":"\u042e\u0433\u043e-\u0417\u0430\u043f\u0430\u0434\u043d\u0430\u044f"},{"val":163,"label":"\u042e\u0436\u043d\u0430\u044f"},{"val":154,"label":"\u042f\u0441\u0435\u043d\u0435\u0432\u043e"}]'></div>
					<input type="hidden" id="order_subway_id" name="order[subway_id]" value="" />
				</div>
				
				<div class="bInputAddress">
					<label class="bPlaceholder">Улица*</label>

					<input type="text" id="order_address_street" class="bBuyingLine__eText mInputLong mInputStreet" name="order[address_street]" value="" />
				</div>

				<div class="bInputAddress">
					<label class="bPlaceholder">Дом*</label>

					<input type="text" id="order_address_building" class="bBuyingLine__eText mInputShort mInputBuild" name="order[address_street]" value="" />
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
                <?= $helper->render('order/newForm/__paymentMethod', ['paymentMethods' => $paymentMethods, 'banks' => $banks, 'creditData' => $creditData]) ?>
			</div>
		</div>

		<div class="bBuyingLine">
			<div class="bBuyingLine__eLeft"></div>

			<div class="bBuyingLine__eRight bInputList">

				<!-- Privacy and policy -->
				<input class="jsCustomRadio bCustomInput mCustomCheckBig" type="checkbox" name="order[agreed]" hidden id="order_agreed"/>
				<label class="bCustomLabel mCustomLabelBig" for="order_agreed">
					Я ознакомлен и согласен с «<a href="/terms" target="_blank">Условиями продажи</a>» и «<a href="/legal" target="_blank">Правовой информацией</a>»*
				</label>

				<p class="bFootenote">* Поля обязательные для заполнения</p>

				<div>
					<a id="completeOrder" class="bBigOrangeButton" href="#">Завершить оформление</a>
				</div>
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
