<?php

return function (
    \Helper\TemplateHelper $helper
    //\Model\PaymentMethod\Entity $paymentMethod
) {

?>
<script type="text/html" id="processBlock">
    <div class="process">
        <div class="img <%=typeNum%>"></div>
        <p><%=text%></p>
        <div class="clear"></div>
    </div>
</script>
<div class="bPayMethodAction" data-url="<?= $helper->url('certificate.check') ?>">
    <input name="order[cardnumber]" type="text" class="bBuyingLine__eText mCardNumber" placeholder="Номер" />
    <input name="order[cardpin]" type="text" class="bBuyingLine__eText mCardPin" placeholder="ПИН" />
</div>

<? };