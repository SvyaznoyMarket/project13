<?php

return function (
    //\Helper\TemplateHelper $helper,
    //\Model\PaymentMethod\Entity $paymentMethod
) {

?>

<div class="orderFinal__certificate bPayMethodAction innerType">
    <div id="sertificateFields">
        <input name="order[cardnumber]" type="text" class="bBuyingLine__eText cardNumber" placeholder="Номер" />
        <input name="order[cardpin]" type="text" class="bBuyingLine__eText cardPin" placeholder="ПИН" />
    </div>
</div>

<? };