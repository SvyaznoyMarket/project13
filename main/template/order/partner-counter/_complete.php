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
        case \Partner\Counter\Actionpay::NAME:
            echo $page->tryRender('order/partner-counter/_actionpay-complete', array('orders' => $orders, 'productsById' => $productsById));
            break;
        case \Partner\Counter\CityAds::NAME:
            echo $page->tryRender('order/partner-counter/_cityads-complete', array('orders' => $orders));
            break;
        case \Partner\Counter\Recreative::NAME:
            echo $page->tryRender('order/partner-counter/_recreative-complete', array('orders' => $orders));
            break;
        case \Partner\Counter\MyThings::NAME:
            echo $page->tryRender('order/partner-counter/_mythings-complete', array('orders' => $orders, 'productsById' => $productsById));
            break;
    }
    ?>

    <?= $page->tryRender('order/partner-counter/_ad4u-complete', array('orders' => $orders)) ?>
<? endif ?>
