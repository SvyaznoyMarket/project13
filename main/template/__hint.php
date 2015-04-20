<?php

return function($name, $value) {

if (strpos($value, '<p') === false) {
    $value = "<p>$value</p>";
}

?>

<div class="bHint">
    <a class="bHint_eLink"><?= $name ?></a>
    <div class="bHint_ePopup popup">
        <div class="close"></div>
        <div class="bHint-text">
            <?= nl2br($value) ?>
        </div>
    </div>
</div>

<? };
