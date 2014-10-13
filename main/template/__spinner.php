<?php

return function(
    $id,
    $productId,
    $value = 1,
    $disabled = false
) { ?>

<div class="bCountSection clearfix<? if ($disabled): ?> mDisabled<? endif ?>" data-spinner-for="<?= $id ?>" data-product-id="<?= $productId ?>" data-bind="buySpinnerBinding: cart">
    <button class="bCountSection__eM">-</button>
    <input class="bCountSection__eNum" type="text" value="<?= $value ?>" <? if ($disabled): ?>disabled="disabled"<? endif ?> />
    <button class="bCountSection__eP">+</button>
    <span>шт.</span>
</div><!--/counter -->

<? };