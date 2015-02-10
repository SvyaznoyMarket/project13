<?php
/**
 * @var $productsById   \Model\Product\Entity[]
 * @var $orders         \Model\Order\Entity[]
 */
$advmakerCookie = \App::request()->cookies->get('ams2s');
$orderNumbers = join(', ', array_map(function(\Model\Order\Entity $order) { return $order->getNumberErp() ; }, $orders));

// формируем Request для обращения к трекеру
$link = \App::request()->create(
    'http://am15.net/s2s.php',
    'GET',
    [
        'ams2s'     => $advmakerCookie,
        'orders'    => $orderNumbers
    ]
);

?>
<!-- AdvMaker Tracker pixel -->
<img src="<?= $link->getUri() ?>" style="display: none" width="0" height="0" />