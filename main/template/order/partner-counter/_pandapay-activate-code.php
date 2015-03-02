<?php
/**
 * @var $productsById   \Model\Product\Entity[]
 * @var $orders         \Model\Order\Entity[]
 * @var $promocode      string
 */

$params = array(
    'shop'  => '3038',
    'aim'   => '3781',
    'code' => $promocode,
);

?>
<? foreach ($orders as $order) : ?>
    <? $url = "http://pandapay.ru/api/promocode/activate" . '?' . http_build_query($params + ['apid' => $order->getNumberErp(), 'price' => $order->getSum()]) ?>
    <img src="<?= $url ?>" width="1" height="1" alt="" />
<? endforeach ?>