<?php
/**
 * @var $page               \View\Order\CreatePage
 * @var $user               \Session\User
 * @var $orders             \Model\Order\Entity[]
 * @var $productsById       \Model\Product\Entity[]
 */
?>

<? if (\App::config()->googleAnalytics['enabled']): ?>
    <?
    switch (\App::partner()->getName()) {
        case \Partner\Counter\Admitad::NAME:
            echo $page->render('order/partner-counter/_admitad-complete', array('orders' => $orders, 'productsById' => $productsById));
            break;
    }
    ?>
<? endif ?>
