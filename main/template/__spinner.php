<?php

return function($id) { ?>

<div class="bCountSection clearfix" data-for="<?= $id ?>">
    <button class="bCountSection__eM">-</button>
    <input class="bCountSection__eNum" type="text" value="1" />
    <button class="bCountSection__eP">+</button>
    <span>шт.</span>
</div><!--/counter -->

<? };