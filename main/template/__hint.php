<?php

return function(
    $name,
    $value
) { ?>

<div class="bHint">
    <a class="bHint_eLink"><?= $name ?></a>
    <div class="bHint_ePopup popup">
        <div class="close"></div>
        <div class="bHint-text">
            <p><?= $value ?></p>
        </div>
    </div>
</div>

<? };
