<?php

/**
 * @param \Helper\TemplateHelper $helper
 * @param \Model\Order\CreatedEntity[] $orders
 */
$f = function(
    \Helper\TemplateHelper $helper,
    $orders
) {
?>

<? foreach ($orders as $order): ?>
    <p>Заявка <?= $order->getNumber() ?> оформлена!</p>
<? endforeach ?>

<? }; return $f;
