<?php

return function(
    $id,
    $value = 1,
    $disabled = false
) { ?>

<div class="bCountSection clearfix<? if ($disabled): ?> mDisabled<? endif ?>" data-spinner-for="<?= $id ?>">
    <button class="bCountSection__eM">-</button>
    <input class="bCountSection__eNum" type="text" value="<?= $value ?>" <? if ($disabled): ?>disabled="disabled"<? endif ?> />
    <button class="bCountSection__eP">+</button>
    <span>шт.</span>
</div><!--/counter -->

<? };