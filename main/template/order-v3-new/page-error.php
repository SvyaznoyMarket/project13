<?php

return function(
    \Helper\TemplateHelper $helper,
    $step,
    $error
) {

 ?>
<div class="order__wrap order-page">
    <section class="orderCnt">
        <div class="pagehead">
            <h1 class="orderCnt_t">Ошибка</h1>
        </div>
        <br>
        <?= $error ?>
    </section>
</div>
<? } ?>