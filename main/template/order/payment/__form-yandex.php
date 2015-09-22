<?php

$f = function(
    \Helper\TemplateHelper $helper,
    $url,
    \Payment\Yandex\Form $form,
    \Model\Order\Entity $order
) {

?>

<form action="<?= $url ?>" method="post">

    <!-- Обязательные поля -->
    <input name="shopId" value="<?= $form->shopId ?>" type="hidden" />
    <input name="scid" value="<?= $form->scid ?>" type="hidden" />
    <input name="sum" value="<?= $form->sum ?>" type="hidden" />
    <input name="customerNumber" value="<?= $form->customerNumber ?>" type="hidden" />

    <!-- Необязательные поля -->
    <input name="shopArticleId" value="<?= $form->shopArticleId ?>" type="hidden" />
    <input name="paymentType" value="<?= $form->paymentType ?>" type="hidden" />
    <input name="orderNumber" value="<?= $form->orderNumber ?>" type="hidden" />
    <input name="cps_phone" value="<?= $form->cps_phone ?>" type="hidden" />
    <input name="cps_email" value="<?= $form->cps_email ?>" type="hidden" />

    <button id="pay-button" type="submit" class="orderPayment_btn btn3">Оплатить</button>
</form>

<? }; return $f;