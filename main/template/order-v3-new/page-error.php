<?php

return function(
    \Helper\TemplateHelper $helper,
    $step,
    $error,
    $showHeader = true,
    $type = 'warning'
) {

    if (!in_array($type, ['error', 'warning'], true)) {
        $type = 'warning';
    }
 ?>
<div class="order__wrap order-page">
    <section class="orderCnt">
        <? if($showHeader): ?>
            <div class="pagehead">
                <h1 class="orderCnt_t">Ошибка</h1>
            </div>
            <br>
            <?= $error ?>
        <? else: ?>
            <div class="orderMessage <?= $type ?>"><?= $error ?></div>
        <? endif ?>
    </section>

    <a class="orderCnt_link" href="/">Вернуться на главную</a>
</div>
<? } ?>
