<?php
/**
 * @var $page \View\Order\CreatePage
 * @var $user \Session\User
 */
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


<!-- общая обертка оформления заказа -->
<div data-bind="css: { mLoader: !prepareData() }"></div>

<div id="order" class="hidden">

	<!-- выбор вариантов доставки -->
	<div>
		<!-- отображение типов доставки -->
		<ul data-bind="foreach: { data: deliveryTypes }">
			<li>
				<h2><a href="#" data-bind="
										text: $data.name,
										states: $data.states,
										click: $root.chooseDeliveryTypes">
				</a></h2>
				<p data-bind="text: $data.description"></p>
			</li>
		</ul>
	</div>

	<div data-bind="foreach: { data: deliveryBoxes, as: 'box' }">
		<!-- боксы доставки -->
		<div style="margin: 20px 10px; border: 1px solid #000; padding: 15px 10px">
			<h2 data-bind="text: box.deliveryName+' '+box.choosenDate().name"></h2>
			<!-- интервалы доставки -->
			<select data-bind="options: box.choosenDate().intervals,
								value: box.choosenInterval,
								optionsText: function(item) {
									return 'c '+ item.start + ' до ' + item.end;
								}">
			</select>

			<p data-bind="visible: box.hasPointDelivery, text: box.choosenPoint().name"></p>
			<p><a href="#" data-bind="visible: box.hasPointDelivery,
										text: 'Сменить магазин',
										click: box.changePoint"></a>
			</p>

			<!-- календарик -->
			<div class="clearfix" data-bind="foreach: { data: allDatesForBlock, as: 'calendarDay' }">
				<div class="bCalendarDay" data-bind="css: { mCurrenDay: calendarDay.value == box.choosenDate().value, mDisabled: !calendarDay.avalible }">
					<p data-bind="text: calendarDay.humanDayOfWeek"></p>
					<a href="#" data-bind="text: ( calendarDay.day === 0 ) ? '' : calendarDay.day,
									click: box.choosenDate"></a>
				</div>
			</div>

			<ul data-bind="foreach: { data: products, as: 'product' }">
				<!-- перечисление продуктов в боксе -->
				<li style="border-bottom: 1px solid #e6e6e6; margin-bottom: 15px;">
					<img data-bind="attr: { src: product.productImg }" />
					<p><a target="_blank" data-bind="text: product.name, attr: { href: product.productUrl }"></a></p>
					<a data-bind="attr: { href: product.deleteUrl }, text: 'Удалить'"></a>
					<p data-bind="text: 'Количество: '+product.quantity"></p>
					<p data-bind="text: 'Стоимость: '+product.price"></p>
				</li>
			</ul>
			<span data-bind="text: 'Общая стоимость '+box.fullPrice"></span>
		</div>

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