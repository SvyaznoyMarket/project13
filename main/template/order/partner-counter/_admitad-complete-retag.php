<?php
/**
 * @var $page          \Templating\HtmlLayout
 * @var $orders        \Model\Order\Entity[]
 * @var $productsById  \Model\Product\Entity[]
 */

$ordersIds = [];
$ordersSum = 0;
$ordersProducts = [];
foreach ($orders as $order) {
    $ordersIds[] = $order->numberErp;
    $ordersSum = bcadd($ordersSum, $order->sum, 2);
    foreach ($order->product as $orderProduct) {
        $ordersProducts[] = [
            'id' => $orderProduct->getId(),
            'number' => $orderProduct->getQuantity(),
        ];
    }
}
?>

<? if (\App::config()->partners['admitad']['enabled']): ?>
    <script type="text/javascript">
        !function(){
            window.ad_order = <?= json_encode(implode(',', $ordersIds), JSON_UNESCAPED_UNICODE) ?>;    // required
            window.ad_amount = <?= json_encode($ordersSum, JSON_UNESCAPED_UNICODE) ?>;
            window.ad_products = <?= json_encode($ordersProducts, JSON_UNESCAPED_UNICODE) ?>;

            window._retag = window._retag || [];
            window._retag.push({code: "9ce8887448", level: 4});
            (function () {
                var id = "admitad-retag";
                if (document.getElementById(id)) {return;}
                var s = document.createElement("script");
                s.async = true; s.id = id;
                var r = (new Date).getDate();
                s.src = (document.location.protocol == "https:" ? "https:" : "http:") + "//cdn.lenmit.com/static/js/retag.min.js?r="+r;
                var a = document.getElementsByTagName("script")[0]
                a.parentNode.insertBefore(s, a);
            })()
        }();
    </script>
<? endif ?>