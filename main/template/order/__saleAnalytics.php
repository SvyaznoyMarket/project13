<?php

/**
 * @param \Helper\TemplateHelper $helper
 * @param \Model\Order\Entity[] $orders
 */
$f = function(
    \Helper\TemplateHelper $helper,
    array $orders
) {

$data = [];
foreach ($orders as $order) {
    $meta = $order->getMeta();

    $data[] = [
        'category' => 'slices_sale',
        'action'   => 'buy',
        //'label'    => $order,
    ];
}
?>


<? }; return $f;