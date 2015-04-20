<?php
/**
 * @var $page               \Templating\HtmlLayout
 * @var $user               \Session\User
 * @var $orders             \Model\Order\Entity[]
 * @var $productsById       \Model\Product\Entity[]
 */
?>
<noscript >
<? foreach ($orders as $order): ?>
    <? if ($link = \Partner\Counter\CityAds::getLink($order)): ?>
        <img src="<?= $link ?>" alt="" />
    <? endif ?>
    <? if ($link = \Partner\Counter\CityAds::getCityAdspixLink($order, $productsById, $page)): ?>
        <img src="<?= $link ?>" width="1" height="1" alt="" />
    <? endif ?>
<? endforeach ?>
</noscript>

<? foreach ($orders as $order): ?>
    <? if ($link = \Partner\Counter\CityAds::getCityAdspixLink($order, $productsById, $page, true)): ?>
        <script type="text/javascript" async="async" src="<?= $link ?>"></script>
    <? endif ?>
<? endforeach ?>

