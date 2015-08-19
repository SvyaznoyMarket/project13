<?php
/**
 * @var $page          \Templating\HtmlLayout
 * @var $user          \Session\User
 * @var $orders        \Model\Order\Entity[]
 * @var $productsById  \Model\Product\Entity[]
 */
?>
<? foreach ($orders as $order) : ?>
    <? if ($link = \Partner\Counter\Actionpay::getOrderCompleteLink($order, $productsById)): ?>
        <img src="http://n.actionpay.ru/ok/3781.png?<?= $link ?>" height="1" width="1" />
    <? endif ?>
<? endforeach ?>