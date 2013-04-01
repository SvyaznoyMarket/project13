<?php
/**
 * @var $page   \View\Order\CreatePage
 * @var $user   \Session\User
 * @var $orders \Model\Order\Entity[]
 */
?>

<? foreach ($orders as $order): ?>
    <? if ($link = \Partner\Counter\CityAds::getLink($order)): ?>
        <img src="<?= $link ?>" width="1" height="1" alt="" />
    <? endif ?>
<? endforeach ?>
