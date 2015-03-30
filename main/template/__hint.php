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
            <p><?= nl2br($value) ?></p>
        </div>
    </div>
</div>

<? };
