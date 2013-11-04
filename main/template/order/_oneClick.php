<?php
/**
 * @var $page               \View\Layout
 * @var $order              \Model\Order\Entity
 * @var $orderData          array
 * @var $shop               \Model\Shop\Entity
 * @var $orderProduct       \Model\Order\Product\Entity
 * @var $product            \Model\Product\Entity
 */

$analyticsData = null;
$categoryName = false;
$orderSum = str_replace(',', '.', $order->getSum()); // Полная сумма заказа (дроби через точку)

if ($orderProduct && $product){
    $categories = $product->getCategory();
    /** @var $rootCategory \Model\Product\Category\Entity */
    $rootCategory = array_shift($categories);
    /** @var $category \Model\Product\Category\Entity */
    $category = array_pop($categories);
    $categoryName = $rootCategory ? ($rootCategory->getName() . ($category ? (' - ' . $category->getName()) : '')) : '';
}

?>

<div style="width: 900px;">

    <div class="bFormSave">
        <h2>Ваш заказ принят, спасибо!</h2>
        <p class="font19">Номер заказа: <?= $order->getNumber() ?></p>
        <p class="font16">Дата заказа: <?= $page->helper->humanizeDate(new \DateTime()) ?></p>
        <p class="font16">Сумма заказа: <?= $page->helper->formatPrice($order->getSum()) ?> <span class="rubl">p</span></p>
        <div class="line"></div>
        <p class="font14">Спасибо за размещение заказа! Оператор подтвердит его звонком или смс на Ваш контактный номер!</p>
    </div>
    <?

    if (\App::config()->analytics['enabled']) {

        ?>
        <div id="adriverOrder" data-vars="<?= $page->json($orderData) ?>" class="jsanalytics"></div>
        <div id="adblenderOrder" data-vars="<?= $page->json($orderData) ?>" class="jsanalytics"></div>
        <!-- Efficient Frontiers -->
        <img src="http://pixel.everesttech.net/3252/t?ev_Orders=0&amp;ev_Revenue=0&amp;ev_Quickorders=1&amp;ev_Quickrevenue=<?= $order->getSum() ?>&amp;ev_transid=<?= $order->getNumber() ?>" width="1" height="1" />
        <img src="http://rs.mail.ru/g632.gif" style="width:0;height:0;position:absolute;" alt=""/>
        <? /* adriverOrder: */ ?>
        <noscript>
            <img src="http://ad.adriver.ru/cgi-bin/rle.cgi?sid=182615&sz=order&bt=55&pz=0&rnd=1086697038&custom=150=<?= $order->getNumber() ?>" border="0" width="1" height="1" alt="" />
        </noscript>
        <?


        if ( \App::config()->googleAnalytics['enabled'] ) {

            // code for _gaq.push( ['_addTrans'], )
            $analyticsData = [
                '_addTrans',
                $order->getNumber() . '_F', //  ID транзакции, Номер заказа
                $shop ? $page->escape( $shop->getName() ) : '', // Название магазина (Необязательно)
                $orderSum, // Полная сумма заказа (дроби через точку)
                //'0',                  // old // Стоимость доставки (дроби через точку)
                $orderSum*0.18,         // new // Налог. Общая сумма из пукнта выше умноженная на 0,18.
                $order->getCity() ? $page->escape( $order->getCity()->getName() ) : '', // Город доставки (Необязательно)
                '', // Область (необязательно)
                '' // Страна (нобязательно)
            ];
            ?>
            <div id="GA_addTransJS" class="" data-vars="<?= $page->json( $analyticsData ) ?>"></div>
            <?



            if ( $categoryName ) {
                // code for _gaq.push( ['_addItem'], )
                $analyticsData = [
                    '_addItem',
                    $order->getNumber() . '_F', // Номер заказа
                    $product->getArticle(), // Артикул
                    $page->escape( $product->getName() ), // Название товара
                    $page->escape( $categoryName ), // Категория товара
                    str_replace( ',', '.', $orderProduct->getPrice() ), // Стоимость 1 единицы товара
                    str_replace( ',', '.', $orderProduct->getQuantity() ), // Количество товара
                ];
                ?>
                <div id="GA_addItemJS" class="" data-vars="<?= $page->json( $analyticsData ) ?>"></div>
                <?
            }

        } // end of If googleAnalytics enabled





        if ( \App::config()->yandexMetrika['enabled'] ) {
            $analyticsData = [
                'order_id' => $order->getNumber(),
                'order_price' => $orderSum,
                'currency' => 'RUR',
                'exchange_rate' => 1,
            ];
            if ( $orderProduct && $product ) {
                $analyticsData['goods'] = [
                    'id' => $product->getArticle(),
                    'name' => $page->escape( $product->getName() ),
                    'price' => str_replace( ',', '.', $orderProduct->getPrice() ),
                    'quantity' => $orderProduct->getQuantity(),
                ];
            }
            ?>
            <div id="YA_paramsJS" class="" data-vars="<?= $page->json( $analyticsData ) ?>"></div>
            <?
        } // end of If yandexMetrika enabled




        ?>
        <div id="adBelnderJS" class="" data-vars="<?= $page->json( $orderSum ) ?>"></div>
        <?


    }// end of If analytics enabled


    /* <!-- <div class="line"></div> --> */
    ?>
    <div class="bFormB2">
        <div class="ac mb25">
            <a class="bBigOrangeButton" href="<?= $page->url('homepage') ?>" onclick="$('#order1click-container-new').trigger('close'); return false">Продолжить покупки</a>
        </div>
    </div>

    <?= $page->tryRender('order/partner-counter/_reactive-oneClick', ['orderSum' => $orderSum, 'orderNum' => $order->getNumber()]) ?>
</div>
<?
