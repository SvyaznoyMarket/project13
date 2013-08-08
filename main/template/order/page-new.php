<?php
/**
 * @var $page \View\Order\CreatePage
 * @var $user \Session\User
 */
?>

<!-- общая обертка оформления заказа -->
<div>

	<!-- выбор вариантов доставки -->
	<div data-bind="css: { mLoader: !prepareData() }">

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
			<h2 data-bind="text: box.deliveryName+' '+box.choosenDate.name"></h2>
			<select data-bind="options: box.choosenDate.intervals,
								value: box.choosenInterval,
								optionsText: function(item) {
									return 'c '+ item.start + ' до ' + item.end;
								}">
			</select>
			<ul data-bind="foreach: { data: products, as: 'product' }">
				<!-- перечисление продуктов в боксе -->
				<li style="border-bottom: 1px solid #e6e6e6; margin-bottom: 15px;">
					<img data-bind="attr: { src: product.productImg }" />
					<p><a target="_blank" data-bind="text: product.name, attr: { href: product.productUrl }"></a></p>
					<a data-bind="attr: { href: product.deleteUrl }">Удалить</a>
					<p data-bind="text: 'Количество: '+product.quantity"></p>
					<p data-bind="text: 'Стоимость: '+product.price"></p>
				</li>
			</ul>
			<span data-bind="text: 'Общая стоимость '+box.fullPrice"></span>
		</div>
	</div>

</div>