<?php
/**
 * @var $page           \View\Layout
 * @var $orders         \Model\Order\Entity[]
 * @var $productsById   \Model\Product\Entity[]
 * @var $servicesById   \Model\Product\Service\Entity[]
 * @var $shopsById      \Model\Shop\Entity[]
 * @var $paymentMethod  \Model\PaymentMethod\Entity
 */

if (\App::config()->analytics['enabled']) : ?>

    <div id="yandexOrderComplete" class="jsanalytics"></div>

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
            <img src="http://pixel.everesttech.net/245/t?ev_Orders=1&amp;ev_Revenue=<?= $order->getSum() ?>&amp;ev_Quickorders=0&amp;ev_Quickrevenue=0&amp;ev_transid=<?= $order->getNumber() ?>" width="1" height="1" />

            <?//= (new \View\Partners\VisualDna)->routeOrderComplete($orders, $productsById, $paymentMethod); // add VisualDNA pixel, SITE-2773; rm, SITE-3200 ?>

        <?php endforeach ?>
<? endif;