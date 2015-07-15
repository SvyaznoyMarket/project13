<?php

return function($name, $value) {

if (strpos($value, '<p') === false) {
    $value = "<p>$value</p>";
}

?>

<? if (\App::abTest()->isNewProductPage()): ?>
    <div class="props-list__hint">
        <a class="i-product i-product--hint" href="" onclick="$('.info-popup--open').removeClass('info-popup--open');$(this).next().addClass('info-popup--open'); return false;"></a>
        <!-- попап с подсказкой, чтобы показать/скрыть окно необходимо добавить/удалить класс info-popup--open -->
        <div class="prop-hint info-popup">
            <i class="closer" onclick="$(this).parent().removeClass('info-popup--open')">×</i>
            <div class="info-popup__inn"><?= nl2br($value) ?></div>
        </div>
        <!--/ попап с подсказкой -->
    </div>
<? else: ?>
    <div class="bHint">
        <a class="bHint_eLink"><?= $name ?></a>
        <div class="bHint_ePopup popup">
            <div class="close"></div>
            <div class="bHint-text">
                <?= nl2br($value) ?>
            </div>
        </div>
    </div>
<? endif ?>
<? };
