<?php return function($value){ ?>
    <? if ($value): ?>
        <div class="prop-hint info-popup js-product-properties-hint-popup">
            <i class="closer js-product-properties-hint-popup-closer">×</i>
            <div class="info-popup__inn"><?= nl2br($value) ?></div>
        </div>
    <? endif ?>
<? };