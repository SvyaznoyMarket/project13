<?php

return function(
    \Helper\TemplateHelper $helper,
    array $orders,
    array $productsById = null,
    $id = 'jsOrder'
) {
?>

<div id="<?= $id ?>" data-value="<?= $helper->json(\Util\Analytics::getForOrder($orders, $productsById)) ?>"></div>

<? };
