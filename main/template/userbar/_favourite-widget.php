<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product
) {

?>

<div class="favourite-userbar-popup-widget topbarfix_cmpr_popup topbarfix_cmpr_popup-add js-favourite-popup">
    <div class="topbarfix_cmpr_popup_inn">
        <div class="clsr2 js-favourite-popup-closer"></div>
        <strong>Товар добавлен в избранное</strong>
        <div class="cmprAdd">
            <img src="<?= $product->getImageUrl() ?>" width="40" height="40" alt="" class="cmprAdd_img js-favourite-popup-image" />

            <div class="cmprAdd_n">
                <span class="cmprAdd_n_t js-favourite-popup-prefix"></span><?= $helper->escape($product->getPrefix()) ?><br/>
                <span class="js-favourite-popup-webName"><?= $helper->escape($product->getWebName()) ?></span>
            </div>
        </div>
    </div>
</div>

<? };