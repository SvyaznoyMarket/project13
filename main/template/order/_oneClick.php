<?php
/**
 * @var $page               \View\Layout
 * @var $order              \Model\Order\Entity
 * @var $orderData          array
 * @var $shop               \Model\Shop\Entity
 * @var $orderProduct       \Model\Order\Product\Entity
 * @var $product            \Model\Product\Entity
 */
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

    <? if (\App::config()->analytics['enabled']): ?>
        <div id="adriverOrder" data-vars="<?= $page->json($orderData) ?>" class="jsanalytics"></div>
        <div id="adblenderOrder" data-vars="<?= $page->json($orderData) ?>" class="jsanalytics"></div>
        <!-- Efficient Frontiers -->
        <img src="http://pixel.everesttech.net/3252/t?ev_Orders=0&amp;ev_Revenue=0&amp;ev_Quickorders=1&amp;ev_Quickrevenue=<?= $order->getSum() ?>&amp;ev_transid=<?= $order->getNumber() ?>" width="1" height="1" />
        <img src="http://rs.mail.ru/g632.gif" style="width:0;height:0;position:absolute;" alt=""/> 
    <? endif ?>


    <script type="text/javascript">
        function runAnalitics() {
            <? if (\App::config()->googleAnalytics['enabled']): ?>
            if (typeof(_gaq) !== 'undefined') {
                _gaq.push(['_addTrans',
                    '<?= $order->getNumber() . '_F' ?>', // Номер заказа
                    '<?= $shop ? $page->escape($shop->getName()) : '' ?>', // Название магазина (Необязательно)
                    '<?= str_replace(',', '.', $order->getSum()) ?>', // Полная сумма заказа (дроби через точку)
                    '0', // Стоимость доставки (дроби через точку)
                    '<?= $order->getCity() ? $page->escape($order->getCity()->getName()) : '' ?>', // Город доставки (Необязательно)
                    '', // Область (необязательно)
                    '' // Страна (нобязательно)
                ]);
                _gaq.push(['_trackEvent', 'QuickOrder', 'Success']);

                <? if ($orderProduct && $product): ?>
                <?
                    $categories = $product->getCategory();
                    /** @var $rootCategory \Model\Product\Category\Entity */
                    $rootCategory = array_shift($categories);
                    /** @var $category \Model\Product\Category\Entity */
                    $category = array_pop($categories);
                    $categoryName = $rootCategory ? ($rootCategory->getName() . ($category ? (' - ' . $category->getName()) : '')) : '';
                ?>
                _gaq.push(['_addItem',
                    '<?= $order->getNumber() . '_F' ?>', // Номер заказа
                    '<?= $product->getArticle() ?>', // Артикул
                    '<?= $page->escape($product->getName()) ?>', // Название товара
                    '<?= $page->escape($categoryName) ?>', // Категория товара
                    '<?= str_replace(',', '.', $orderProduct->getPrice()) ?>', // Стоимость 1 единицы товара
                    '<?= str_replace(',', '.', $orderProduct->getQuantity()) ?>'               // Количество товара
                ]);
                <? endif ?>

                _gaq.push(['_trackTrans']);
            }
            <? endif ?>

            <? if (\App::config()->yandexMetrika['enabled']): ?>
            var yaParams = {
                order_id:'<?= $order->getNumber() ?>',
                order_price: <?= str_replace(',', '.', $order->getSum()) ?>,
                currency:'RUR',
                exchange_rate:1,
                goods:[
                    <? if ($orderProduct && $product): ?>
                    {
                        id:'<?= $product->getArticle() ?>',
                        name:'<?= $page->escape($product->getName()) ?>',
                        price: <?= str_replace(',', '.', $orderProduct->getPrice()) ?>,
                        quantity: <?= $orderProduct->getQuantity() ?>
                    }
                    <? endif ?>
                ]
            };
            if (typeof(yaCounter10503055) !== 'undefined')  yaCounter10503055.reachGoal('QORDER', yaParams);
            <? endif ?>

            <? if (\App::config()->analytics['enabled']): ?>
            if (typeof(window.adBelnder) != 'undefined') window.adBelnder.addOrder(<?= str_replace(',', '.', $order->getSum()) ?>);

            <? endif ?>
        }
    </script>

    <!-- <div class="line"></div> -->

    <div class="bFormB2">
        <div class="ac mb25">
            <a class="bBigOrangeButton" href="<?= $page->url('homepage') ?>" onclick="$('#order1click-container-new').trigger('close'); return false">Продолжить покупки</a>
        </div>
    </div>

</div>