<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product
) {

?>

<div id="favourite-userbar-popup-widget" class="topbarfix_cmpr_popup topbarfix_cmpr_popup-add js-favourite-popup">
    <div class="topbarfix_cmpr_popup_inn">
        <div class="clsr2 js-compare-addPopup-closer"></div>
        <strong>Товар добавлен в избранное</strong>
        <div class="cmprAdd">
            <img src="<?= $product->getImageUrl() ?>" width="40" height="40" alt="" class="cmprAdd_img js-compare-addPopup-image" />

            <div class="cmprAdd_n">
                <span class="cmprAdd_n_t js-compare-addPopup-prefix"></span><?= $product->getPrefix() ?><br/>
                <span class="js-compare-addPopup-webName"><?= $product->getWebName() ?></span>
            </div>
        </div>
    </div>
</div>

<? };