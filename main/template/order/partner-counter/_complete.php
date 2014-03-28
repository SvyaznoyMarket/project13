<?php
/**
 * @var $page               \View\Order\CreatePage
 * @var $user               \Session\User
 * @var $orders             \Model\Order\Entity[]
 * @var $productsById       \Model\Product\Entity[]
 */
?>

<? if (\App::config()->analytics['enabled']): ?>
    <?
    switch (\App::partner()->getName()) {
        case \Partner\Counter\Admitad::NAME:
        case \Partner\Counter\Admitad::NAME_SYNONYM:
            echo $page->tryRender('order/partner-counter/_admitad-complete', array('orders' => $orders, 'productsById' => $productsById));
            break;
        case \Partner\Counter\Actionpay::NAME:
            echo $page->tryRender('order/partner-counter/_actionpay-complete', array('orders' => $orders, 'productsById' => $productsById));
            break;
        case \Partner\Counter\CityAds::NAME:
            echo $page->tryRender('order/partner-counter/_cityads-complete-pixel', array('orders' => $orders, 'productsById' => $productsById));
            break;
        /*
        case \Partner\Counter\Reactive::NAME:
            echo $page->tryRender('order/partner-counter/_reactive-complete', array('orders' => $orders));
            break;
        */
        case \Partner\Counter\Recreative::NAME:
            echo $page->tryRender('order/partner-counter/_recreative-complete', array('orders' => $orders));
            break;
        /*case \Partner\Counter\MyThings::NAME:
            echo $page->tryRender('order/partner-counter/_mythings-complete', array('orders' => $orders, 'productsById' => $productsById));
            break;*/
    }
    ?>
    <?
    if (\Partner\Counter\MyThings::isTracking()) {
        echo $page->tryRender('order/partner-counter/_mythings-complete', array('orders' => $orders, 'productsById' => $productsById));
    }
    ?>
    <?= $page->tryRender('order/partner-counter/_ad4u-complete', array('orders' => $orders)) ?>
    <?= $page->tryRender('order/partner-counter/_reactive-complete', array('orders' => $orders)) ?>
    <?= $page->tryRender('order/partner-counter/_cityads-complete-counter', array('orders' => $orders)) ?>
    <? foreach ($orders as $order) { ?>
        <?= $page->tryRender('order/partner-counter/_reactive-oneClick', ['orderSum' => str_replace(',', '.', $order->getPaySum()), 'orderNum' => $order->getNumber()]) ?>
    <? } ?>
<? endif ?>
