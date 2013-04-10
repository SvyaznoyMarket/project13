<?php
/**
 * @var $page               \View\Order\CreatePage
 * @var $user               \Session\User
 * @var $orders             \Model\Order\Entity[]
 * @var $productsById       \Model\Product\Entity[]
 */
?>

<? if (\App::config()->analytics['enabled']): ?>
    <?= $page->tryRender('order/partner-counter/_etargeting-complete', array('orders' => $orders, 'productsById' => $productsById)) ?>

    <?
    switch (\App::partner()->getName()) {
        case \Partner\Counter\Admitad::NAME:
            echo $page->tryRender('order/partner-counter/_admitad-complete', array('orders' => $orders, 'productsById' => $productsById));
            break;
        /*
        case \Partner\Counter\Etargeting::NAME:
            echo $page->tryRender('order/partner-counter/_etargeting-complete', array('orders' => $orders, 'productsById' => $productsById));
            break;
        */
        case \Partner\Counter\CityAds::NAME:
            echo $page->tryRender('order/partner-counter/_cityads-complete', array('orders' => $orders));
            break;
        case \Partner\Counter\Reactive::NAME:
            echo $page->tryRender('order/partner-counter/_reactive-complete', array('orders' => $orders));
            break;
        case \Partner\Counter\Recreative::NAME:
            echo $page->tryRender('order/partner-counter/_recreative-complete', array('orders' => $orders));
            break;
    }
    ?>
<? endif ?>
