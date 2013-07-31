<?php
/**
 * @var $page         \View\Layout
 * @var $orders       \Model\Order\Entity[]
 * @var $productsById \Model\Product\Entity[]
 * @var $servicesById \Model\Product\Service\Entity[]
 * @var $shopsById    \Model\Shop\Entity[]
 */
?>
<? if (\App::config()->analytics['enabled']): ?>

<script type="text/javascript">
    var yaParams =
            [
                <? foreach ($orders as $i => $order): ?>
                {
                    order_id:'<?= $order->getNumber() ?>',
                    order_price: <?= str_replace(',', '.', $order->getSum()) ?>,
                    currency:'RUR',
                    exchange_rate:1,
                    goods:[
                        <? foreach ($order->getProduct() as $j => $orderProduct): ?>
                        <?
                            $product = isset($productsById[$orderProduct->getId()]) ? $productsById[$orderProduct->getId()] : null;
                            if (!$product) continue;
                        ?>

                            {
                                id:'<?= $product->getArticle() ?>',
                                name:'<?= $page->escape($product->getName()) ?>',
                                price: <?= $orderProduct->getPrice() ?>,
                                quantity: <?= $orderProduct->getQuantity() ?>

                            }<?php echo ((bool)$order->getService() || ($j < (count($order->getProduct()) - 1))) ? ',' : "\n" ?>

                        <? endforeach ?>

                        <? foreach ($order->getService() as $j => $orderService): ?>
                            <?
                            $service = isset($servicesById[$orderService->getId()]) ? $servicesById[$orderService->getId()] : null;
                            if (!$service) continue;
                            ?>

                            {
                                id:'<?= $service->getToken() ?>',
                                name:'<?= $page->escape($service->getName()) ?>',
                                price: <?= $orderService->getPrice() ?>,
                                quantity: <?= $orderService->getQuantity() ?>

                            }<?php echo ($j < (count($order->getService()) - 1)) ? ',' : '' ?>

                        <? endforeach ?>
                    ]
                }<?= ($i < (count($orders) - 1)) ? ',' : "\n" ?>

                <? endforeach ?>
            ];


    <? foreach ($orders as $i => $order): ?>
        // ;(function(){
        //     var toKISS_complete = {
        //         'Checkout Complete Order ID': <?= $order->getNumber() ?>,
        //         'Checkout Complete SKU Quantity': count($order->getProduct()),
        //         'Checkout Complete SKU Total':itemT,
        //         'Checkout Complete Delivery Total':parseInt(dlvr_total),
        //         'Checkout Complete Order Total': $order->getSum(),
        //         'Checkout Complete Order Type':'cart order',
        //         'Checkout Complete Delivery':nowDelivery,
        //         'Checkout Complete Payment':data.paymentMethodId
        //     };

        //     if ( (typeof(_kmq) !== 'undefined') && (KM !== 'undefined') ) {
        //         _kmq.push(['alias', phoneNumber, KM.i()]);
        //         _kmq.push(['alias', emailVal, KM.i()]);
        //         _kmq.push(['identify', phoneNumber]);
        //         _kmq.push(['record', 'Checkout Complete', toKISS_complete]);
        //     }
        // }());
    <? endforeach ?>
</script>


<div id="mixmarket" class="jsanalytics"></div>
<div id="marketgidOrderSuccess" class="jsanalytics"></div>
<img src="http://rs.mail.ru/g632.gif" style="width:0;height:0;position:absolute;" alt=""/>
    <?php foreach ($orders as $i => $order):
        $jsonOrdr = array(
            'order_article'    => implode(',', array_map(function ($orderProduct) {
                /** @var $orderProduct \Model\Order\Product\Entity */
                return $orderProduct->getId();
            }, $order->getProduct())),
            'order_id'         => $order->getNumber(),
            'order_total'      => $order->getSum(),
            'product_quantity' => implode(',', array_map(function ($orderProduct) {
                /** @var $orderProduct \Model\Order\Product\Entity */
                return $orderProduct->getQuantity();
            }, $order->getProduct())),
        );
        ?>

        <div id="adriverOrder" data-vars="<?= $page->json($jsonOrdr) ?>" class="jsanalytics"></div>
        
        <div id="adblenderOrder" data-vars="<?= $page->json($jsonOrdr) ?>" class="jsanalytics"></div>
        <!-- Efficient Frontiers -->
        <img src="http://pixel.everesttech.net/3252/t?ev_Orders=1&amp;ev_Revenue=<?= $order->getSum() ?>&amp;ev_Quickorders=0&amp;ev_Quickrevenue=0&amp;ev_transid=<?= $order->getNumber() ?>" width="1" height="1" />

    <?php endforeach ?>
<? endif ?>