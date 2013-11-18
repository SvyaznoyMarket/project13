<?php

return function (
    \Helper\TemplateHelper $helper
) {
?>

Автоматический пункт выдачи заказов

<ul class="bPickPointHint">
	<li class="bPickPointHint__eItem">Каждый товар будет ожидать вас в отдельной ячейке постамата.</li>

	<li class="bPickPointHint__eItem mPay">
		Оплатить выдачу товара из ячейки можно наличными или банковской картой.<br/>
		Каждая выдача оплачивается <strong>отдельно</strong>.<br/><br/>
		Постамат не выдает сдачу при оплате наличными.<br/>
		Остатком средств можно пополнить баланс мобильного телефона.
	</li>
</ul>

<? };