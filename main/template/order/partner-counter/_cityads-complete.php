<?php
/**
 * @var $page   \View\Order\CreatePage
 * @var $user   \Session\User
 * @var $orders \Model\Order\Entity[]
 */
?>

<? foreach ($orders as $order): ?>
    <? if ($link = \Partner\Counter\CityAds::getLink($order)): ?>
        <img src="<?= $link ?>" />
    <? endif ?>
<? endforeach ?>

<?
$productIds = [];
$productQuantities = [];
$ordersSum = 0;

foreach ($orders as $order) {
    foreach ($order->getProduct() as $orderProduct) {
        $productIds[] = $orderProduct->getId();
        $productQuantities[] = $orderProduct->getQuantity();
    }
    $ordersSum += $order->getSum();
}

/** @var $order \Model\Order\Entity */
$order = reset($orders);
?>

<script id="xcntmyAsync" type="text/javascript">
    /*<![CDATA[*/
    // стр. успешного оформления заказа
    var xcnt_order_products = '<?= implode(',', $productIds) ?>'; // где XX,YY,ZZ – это ID товаров в корзине через запятую.
    var xcnt_order_quantity = '<?= implode(',', $productQuantities) ?>'; // где X,Y,Z – это количество соответствующих товаров (опционально).
    var xcnt_order_id = '<?= $order->getId() ?>'; // где XXXYYY – это ID заказа (желательно, можно  шифровать значение в MD5)
    var xcnt_order_total = '<?= $ordersSum ?>'; // сумма заказа (опционально)
    /*]]>*/
    (function(){
        var xscr = document.createElement( 'script' );
        var xcntr = escape(document.referrer); xscr.async = true;
        xscr.src = ( document.location.protocol === 'https:' ? 'https:' : 'http:' )
            + '//x.cnt.my/async/track/?r=' + Math.random();
        var x = document.getElementById( 'xcntmyAsync' );
        x.parentNode.insertBefore( xscr, x );
    }());
</script>