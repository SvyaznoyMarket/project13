<?php
/**
 * @var $page               \Templating\HtmlLayout
 * @var $user               \Session\User
 * @var $orders             \Model\Order\Entity[]
 * @var $productsById       \Model\Product\Entity[]
 */
?>

<? foreach ($orders as $order): ?>
    <? if ($orderLink = \Partner\Counter\CityAds::getLink($order)): // Старый код, возможно нужно удалить ?>
        <noscript ><img src="<?= $orderLink ?>" alt="" /></noscript>
    <? endif ?>
    <? if ($link = \Partner\Counter\CityAds::getCityAdspixLink($order, $productsById, $page)): ?>
        <script type="text/javascript" async="async" src="<?= $link ?>&md=2"></script>
        <noscript ><img src="<?= $link ?>" width="1" height="1" alt="" /></noscript>
    <? endif ?>
<? endforeach ?>

