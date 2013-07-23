<?php
/**
 * @var $page          \View\Order\CreatePage
 * @var $user          \Session\User
 * @var $orders        \Model\Order\Entity[]
 * @var $productsById  \Model\Product\Entity[]
 */
?>

<? if ($link = \Partner\Counter\Actionpay::getOrderCompleteLink($orders, $productsById)): ?>
    <img src="http://n.actionpay.ru/ok/4207.png?<?= $link ?>" height="1" width="1" />
    <img src="http://n.actionpay.ru/ok/3781.png?<?= $link ?>" height="1" width="1" />
<? endif ?>