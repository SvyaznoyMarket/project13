<?php

return function(
    \Helper\TemplateHelper $helper,
    array $creditData,
    \Model\Product\BasicEntity $product
) {
    $user = \App::user();

    if (!($creditData['creditIsAllowed'] && !$user->getRegion()->getHasTransportCompany())) {
        return '';
    }
?>

<div class="creditbox">
    <label class="bigcheck" for="creditinput"><b></b>
        <span class="dotted">Купи в кредит</span>
        <input id="creditinput" type="checkbox" name="creditinput" autocomplete="off">
    </label>

    <div class="creditbox__sum"><span class="bJustText">от</span> <strong></strong> <span class="rubl">p</span> <span class="bJustText">в месяц</span></div>
    <input data-model="<?= $helper->escape($creditData['creditData']) ?>" id="dc_buy_on_credit_<?= $product->getArticle(); ?>" name="dc_buy_on_credit" type="hidden" />
</div><!--/credit box -->
<? };