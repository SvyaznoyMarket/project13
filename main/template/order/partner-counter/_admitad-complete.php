<?php
/**
 * @var $page               \View\Order\CreatePage
 * @var $user               \Session\User
 * @var $orders             \Model\Order\Entity[]
 * @var $productsById       \Model\Product\Entity[]
 */
?>

<? foreach ($orders as $order): ?>
    <? foreach (\PartnerCounter\Admitad::getLinks($order, $productsById) as $link): ?>
        <img src="<?= $link ?>" width="1" height="1" alt="" />
    <? endforeach ?>
<? endforeach ?>