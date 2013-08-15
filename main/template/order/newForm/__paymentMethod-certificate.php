<?php

return function (
    //\Helper\TemplateHelper $helper,
    //\Model\PaymentMethod\Entity $paymentMethod
) {

?>

<div class="bPayMethodAction">
    <input name="order[cardnumber]" type="text" class="bBuyingLine__eText mCardNumber" placeholder="Номер" />
    <input name="order[cardpin]" type="text" class="bBuyingLine__eText mCardPin" placeholder="ПИН" />
</div>

<? };