<?php

return function(
    \Helper\TemplateHelper $helper,
    $step,
    $error
) {

 ?>

    <?= $helper->render('order-v3-new/__head', ['step' => $step]) ?>

    <section class="orderCnt">
        <h1 class="orderCnt_t">Ошибка</h1>
        <?= $error ?>
    </section>

<? } ?>