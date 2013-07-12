<?php

return function(
    array $creditData,
    \Model\Product\BasicEntity $product,
    \Helper\TemplateHelper $helper
) {
    $user = \App::user();

    if (!($creditData['creditIsAllowed'] && !$user->getRegion()->getHasTransportCompany())) {
        return '';
    }
?>

<div class="creditbox">
    <label class="bigcheck" for="creditinput"><b></b>
        <span class="dotted">Беру в кредит</span>
        <input id="creditinput" type="checkbox" name="creditinput" autocomplete="off">
    </label>

    <div class="creditbox__sum">от <strong></strong> <span class="rubl">p</span> в месяц</div>
    <input data-model="<?= $helper->escape($creditData['creditData']) ?>" id="dc_buy_on_credit_<?= $product->getArticle(); ?>" name="dc_buy_on_credit" type="hidden" />
</div><!--/credit box -->

<? };