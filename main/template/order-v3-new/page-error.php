<?php

return function(
    \Helper\TemplateHelper $helper,
    $step,
    $error
) {

 ?>

    <section class="orderCnt">
        <h1 class="orderCnt_t">Ошибка</h1>
        <?= $error ?>
    </section>

<? } ?>