<?php

/**
 * @param \Helper\TemplateHelper $helper
 * @param \Model\Order\CreatedEntity[] $orders
 */
$f = function(
    \Helper\TemplateHelper $helper,
    $orders
) {

    /** @var \Model\Order\CreatedEntity $order */
    $order = reset($orders);
?>

<? foreach ($orders as $order): ?>
<div class="orderOneClick jsOneClickCompletePage">
    <span class="orderOneClick_t">Оформление завершено</span>

    <div id="jsOrderV3OneClickOrder" data-url="<?= $helper->url('orderV3OneClick.get', ['accessToken' => $order->getAccessToken()]) ?>"></div>
	
	<div class="orderU_fldsbottom ta-c orderOneClick_cmpl">
    	<p class="orderOneClick_cmpl_t"><strong>Заявка</strong> <?= $order->getNumber() ?> <strong>оформлена!</strong></p>
    	<p style="margin-bottom: 20px;">Наш сотрудник позвонит Вам для уточнения деталей<br/> и зарегистрирует заказ.</p>
    	<a href="" class="orderCompl_btn btnsubmit">Продолжить покупки</a>
    </div>
</div>
<? endforeach ?>

<? }; return $f;
