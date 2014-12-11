<?php

return function(
    $id,
    $productId,
    $value = 1,
    $disabled = false,
    $location = null
) {

    $domId = 'spinner-' . $productId . '-' . md5(json_encode([$location]));
?>

<div class="bCountSection clearfix<? if ($disabled): ?> mDisabled<? endif ?>" data-spinner-for="<?= $id ?>" data-product-id="<?= $productId ?>" data-bind="buySpinnerBinding: cart">
    <button id="<?= $domId . '-dec' ?>" class="bCountSection__eM">-</button>
    <input id="<?= $domId . '-value' ?>" class="bCountSection__eNum" type="text" value="<?= $value ?>" <? if ($disabled): ?>disabled="disabled"<? endif ?> />
    <button id="<?= $domId . '-inc' ?>" class="bCountSection__eP">+</button>
    <span>шт.</span>
</div><!--/counter -->

<? };