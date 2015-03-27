<?php
/**
 * @var $page           \View\Layout
 * @var $orders         \Model\Order\Entity[]
 * @var $productsById   \Model\Product\Entity[]
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

        <?php endforeach ?>
<? endif;