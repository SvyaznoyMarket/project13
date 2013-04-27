<?php
/**
 * @var $page          \View\Order\CreatePage
 * @var $user          \Session\User
 * @var $orders        \Model\Order\Entity[]
 * @var $productsById  \Model\Product\Entity[]
 */
?>

<? if ($link = \Partner\Counter\Actionpay::getOrderCompleteLink($orders, $productsById)): ?>
    <img src="<?= $link ?>" height="1" width="1" />
<? endif ?>