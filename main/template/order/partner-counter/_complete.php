<?php
/**
 * @var $page               \Templating\HtmlLayout
 * @var $user               \Session\User
 * @var $orders             \Model\Order\Entity[]
 * @var $productsById       \Model\Product\Entity[]
 */
?>

<? if (\App::config()->analytics['enabled']):

    $orderParams = [
        'orders' => $orders,
        'productsById' => $productsById
    ];

    // Если есть кука с промокодом Pandapay, то активируем этот код (через пиксель)
    $pandaPayPromoCode = \App::request()->cookies->get(\App::config()->partners['PandaPay']['cookieName']);
    if (!empty($pandaPayPromoCode)) {
        echo $page->tryRender('order/partner-counter/_pandapay-activate-code', $orderParams + ['promocode' => $pandaPayPromoCode]);
    }

    switch (\App::partner()->getName()) {
        case \Partner\Counter\Actionpay::NAME:
            echo $page->tryRender('order/partner-counter/_actionpay-complete', $orderParams);
            break;
        case \Partner\Counter\CityAds::NAME:
            echo $page->tryRender('order/partner-counter/_cityads-complete-pixel', $orderParams);
            break;
        case 'hubrus':
            echo $page->tryRender('order/partner-counter/_hubrus-complete', $orderParams);
            break;
        case 'advmaker':
            echo $page->tryRender('order/partner-counter/_advmaker', $orderParams);
            break;
        case 'admitad':
            echo $page->tryRender('order/partner-counter/_admitad-complete-pixel', $orderParams);
            break;
    }

    echo $page->tryRender('order/partner-counter/_admitad-complete-retag', array('orders' => $orders));

    echo $page->tryRender('order/partner-counter/_cityads-complete-counter', array('orders' => $orders));

    echo $page->tryRender('order/partner-counter/_adblender-complete', array('orders' => $orders));

 endif ?>
