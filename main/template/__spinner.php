<?php

return function($id, $value = 1) { ?>

<div class="bCountSection clearfix" data-spinner-for="<?= $id ?>">
    <button class="bCountSection__eM">-</button>
    <input class="bCountSection__eNum" type="text" value="<?= $value ?>" />
    <button class="bCountSection__eP">+</button>
    <span>шт.</span>
</div><!--/counter -->

<? };