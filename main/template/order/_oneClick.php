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
$orderSum = str_replace(',', '.', $order->getPaySum()); // Полная сумма заказа (дроби через точку)

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
        <p class="font16">Сумма заказа: <?= $page->helper->formatPrice($order->getPaySum()) ?> <span class="rubl">p</span></p>
        <div class="line"></div>
        <p class="font14">Спасибо за размещение заказа! Оператор подтвердит его звонком или смс на Ваш контактный номер!</p>
    </div>
    <?

    if (\App::config()->analytics['enabled']) {

        ?>
        <img src="http://rs.mail.ru/g632.gif" style="width:0;height:0;position:absolute;" alt=""/>
        <?


        if ( \App::config()->googleAnalytics['enabled'] ) {

            // code for _gaq.push( ['_addTrans'], )
            $analyticsData = [
                '_addTrans',
                $order->getNumber() . '_F', //  ID транзакции, Номер заказа
                $shop ? $page->escape( $shop->getName() ) : '', // Название магазина (Необязательно)
                $orderSum, // Полная сумма заказа (дроби через точку)
                $orderSum*0.18,         // new // Налог. Общая сумма из пукнта выше умноженная на 0,18.
                '0',                  // old // Стоимость доставки (дроби через точку)
                $order->getCity() ? $page->escape( $order->getCity()->getName() ) : '', // Город доставки (Необязательно)
                '', // Область (необязательно)
                '' // Страна (нобязательно)
            ];
            ?>
            <div id="GA_addTransJS" data-vars="<?= $page->json( $analyticsData ) ?>"></div>
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
                <div id="GA_addItemJS" data-vars="<?= $page->json( $analyticsData ) ?>"></div>
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
            <div id="YA_paramsJS" data-vars="<?= $page->json( $analyticsData ) ?>"></div>
            <?
        } // end of If yandexMetrika enabled


    }// end of If analytics enabled


    /* <!-- <div class="line"></div> --> */
    ?>
    <div class="bFormB2">
        <div class="ac mb25">
            <a class="bBigOrangeButton" href="<?= $page->url('homepage') ?>" onclick="$('#order1click-container-new').trigger('close'); return false">Продолжить покупки</a>
        </div>
    </div>

</div>
<?
