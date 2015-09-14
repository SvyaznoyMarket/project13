<?php

return function(
    \Helper\TemplateHelper $helper,
    $step,
    $error
) {

    ?>

    <section class="checkout">
        <h1 class="checkout__title">Ошибка</h1>
        <?= $error ?>
        <div class="back-main" style="margin: 40px 0;"><a class="underline" href="/?parent_ri=55f6cc9a0a787">Вернуться на главную</a></div>
    </section>



<? } ?>