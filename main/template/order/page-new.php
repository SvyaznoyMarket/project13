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
    if ($product->getParentCategory()) {
        $backLink = $product->getParentCategory()->getLink();
        break;
    }
}
?>


	<!-- temp styles -->
	<style type="text/css">
		.bCalendarDay {
			font-size: 12px;
			width: 25px;
			float: left;
		}
		.mDisabled {
			background: gray;
		}
		.mCurrenDay {
			color: red;
			background: orange;
		}
		.bPointPopup {
			background-color: #FFFFFF;
			border: 2px solid #FFA901;
			border-radius: 6px 6px 6px 6px;
			box-shadow: 0 0 7px #616161;
			color: #000000;
			cursor: default;
			overflow: hidden;
			padding: 20px 25px;
			position: fixed;
			top:50%;
			left:50%;

			width: 600px;
			margin-left: -300px;

			height: 400px;
			margin-top: -200px;

			text-align: left;

			z-index: 10000;
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

<!-- общая обертка оформления заказа -->
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
			            <div class="bBuyingDatesItem mLeft">
			                <span class="bArrow"></span>
			            </div>

			            <div class="bBuyingDatesWrap">
				            <ul class="bBuyingDatesList" data-bind="foreach: { data: allDatesForBlock, as: 'calendarDay' }">
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

			            <div class="bBuyingDatesItem mRight">
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
		    <div class="bPointPopup" data-bind="visible: box.showPopupWithPoints">
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
		        <strong class="bSumPrice"><span data-bind="text: window.printPrice(box.fullPrice + box.deliveryPrice)"></span> <span class="rubl">p</span></strong>
		    </div>
		</div>
	</div>
	


	<div class="bPointPopup" data-bind="visible: showPopupWithPoints">
		<h2 data-bind="text: popupWithPoints().header"></h2>
		<ul data-bind="foreach: { data: popupWithPoints().points }">
			<li>
				<a data-bind="text: $data.name, click: $root.selectPoint"></a>
				<span data-bind="text: $data.regime"></span>
			</li>
		</ul>
	</div>

</div>

<div id="jsOrderDelivery" data-value="<?= $page->json($deliveryData) ?>"></div>
