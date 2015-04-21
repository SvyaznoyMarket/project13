<?php
/**
 * @var $page   \Templating\HtmlLayout
 * @var $user   \Session\User
 * @var $orders \Model\Order\Entity[]
 */

$productIds = [];
$productQuantities = [];
$ordersSum = 0;

foreach ($orders as $order) {
    foreach ($order->getProduct() as $orderProduct) {
        $productIds[] = $orderProduct->getId();
        $productQuantities[] = $orderProduct->getQuantity();
    }
    $ordersSum += $order->getPaySum();
}

/** @var $order false|\Model\Order\Entity */
$order = reset($orders);

$data = [
    'page'              => 'order.complete',
    'productIds'        => implode(',', $productIds),        // где XX,YY,ZZ – это ID товаров в корзине через запятую.
    'productQuantities' => implode(',', $productQuantities), // где X,Y,Z – это количество соответствующих товаров (опционально).
    'orderId'           => $order ? $order->getId() : '',    // где XXXYYY – это ID заказа (желательно, можно  шифровать значение в MD5)
    'orderTotal'        => $ordersSum,                       // сумма заказа (опционально)
];
?>
<div id="xcntmyAsync" class="jsanalytics" data-value="<?= $page->json($data) ?>"></div>
