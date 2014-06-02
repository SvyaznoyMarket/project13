<?php
/**
 * @var $page         \View\Order\SvyaznoyServiceMixFailPage
 * @var $paymentData  array
 */

$paymentData = isset($paymentData) && is_array($paymentData) ? $paymentData : [];
?>

<!-- Header -->
<div class='bBuyingHead'>
    <span>Ваш заказ не оплачен</span>
</div>
<!-- /Header -->

<p class="title-font16 font16">
Произошла ошибка !<br>
Если Вы все еще желаете получить заказ, подождите и мы вас автоматически перенаправим на страницу оплаты !
</p>

<p>Через <span class="timer">60</span> сек. мы автоматически перенаправим Вас на страницу оплаты, если этого не произойдет, пожалуйста, нажмите на кнопку "Оплатить заказ".</p>
<div class="pt10">
    <form class="form" method="get" action="<?= $page->url('order.svyaznoyClub.complete') ?>">
        <input type="hidden" name="OrderId" value="<?= isset($paymentData['OrderId']) ? $paymentData['OrderId'] : null ?>" />
        <input type="hidden" name="Status" value="<?= isset($paymentData['Status']) ? $paymentData['Status'] : null ?>" />
        <input type="hidden" name="Discount" value="<?= isset($paymentData['Discount']) ? $paymentData['Discount'] : null ?>" />
        <input type="hidden" name="CardNumber" value="<?= isset($paymentData['CardNumber']) ? $paymentData['CardNumber'] : null ?>" />
        <input type="hidden" name="Error" value="<?= isset($paymentData['Error']) ? $paymentData['Error'] : null ?>" />
        <input type="hidden" name="Signature" value="<?= isset($paymentData['Signature']) ? $paymentData['Signature'] : null ?>" />
    </form>
</div>

<div class="line pb15"></div>

<div class="mt32" style="text-align: center">
    <a class='bBigOrangeButton' href="<?= $page->url('order.svyaznoyClub.complete', $paymentData) ?>">Оплатить заказ</a>
</div>
