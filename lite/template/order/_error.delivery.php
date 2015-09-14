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
        <h2><a href="/">Вернуться к покупкам</a></h2>
    </section>



<? } ?>