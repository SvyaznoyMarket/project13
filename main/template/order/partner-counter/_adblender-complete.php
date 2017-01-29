<?php
/**
 * @var $page          \Templating\HtmlLayout
 * @var $user          \Session\User
 * @var $orders        \Model\Order\Entity[]
 * @var $productsById  \Model\Product\Entity[]
 */
?>

<?
$dataValue = [
    'type'   => 'orderV3.complete',
    'orders' => [],
];

foreach ($orders as $order) {
    $dataValue['orders'][] = [
        $order->numberErp,
        $order->sum,
    ];
}
?>

<div id="adblenderJS" class="jsanalytics" data-value="<?= $page->json($dataValue) ?>"></div>