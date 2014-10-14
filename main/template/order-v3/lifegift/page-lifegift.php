<?php

return function(
    \Helper\TemplateHelper $helper
) {
?>

    <div style="display: none" class="jsRegion" data-value="<?= \App::user()->getRegion() ? \App::user()->getRegion()->getName() : '' ?>"></div>

    <!-- шапка оформления заказа -->
    <header class="orderHd">
        <img class="orderHd_lg" src="/styles/order/img/logo.png" />

        <ul class="orderHd_stps">
                <li class="orderHd_stps_i">Оформление заказа</li>
        </ul>
    </header>
    <!-- /шапка оформления заказа -->

<? } ?>