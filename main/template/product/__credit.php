<?php

return function(
    \Helper\TemplateHelper $helper,
    array $creditData,
    \Model\Product\Entity $product
) {
    $user = \App::user();

    if (!($creditData['creditIsAllowed'] && !$user->getRegion()->getHasTransportCompany())) {
        return '';
    }
?>

<div class="creditbox bInputList">
    <input id="creditinput" class="jsCustomRadio bCustomInput mCustomRadioBig jsProductCreditRadio" type="radio" name="product_credit_payment" autocomplete="off" value="on" />

    <label class="bCustomLabel mCustomLabelRadioBig" for="creditinput">
        В кредит
        <span class="creditbox__sum">от <strong></strong> <span class="rubl">p</span> в месяц</span>
    </label>
    
    <input data-model="<?= $helper->escape($creditData['creditData']) ?>" id="dc_buy_on_credit_<?= $product->getArticle(); ?>" name="dc_buy_on_credit" type="hidden" />
</div><!--/credit box -->
<? };