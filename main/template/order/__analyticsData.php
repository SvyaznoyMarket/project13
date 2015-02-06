<?php

return function(
    \Helper\TemplateHelper $helper,
    array $orders,
    array $productsById
) {
?>

<div id="jsOrder" data-value="<?= $helper->json(\Util\Analytics::getForOrder($orders, $productsById)) ?>"></div>

<? };