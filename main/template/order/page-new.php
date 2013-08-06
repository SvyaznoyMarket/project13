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
				<a href="#" data-bind="
										text: $data.name,
										states: $data.states,
										click: $root.chooseDeliveryTypes">
				</a>
			</li>
		</ul>

	</div>

</div>