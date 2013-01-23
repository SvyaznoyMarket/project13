<?php
/**
 * @var $page         \View\Layout
 * @var $order        \Model\Order\Entity
 * @var $orderData    array
 * @var $shop         \Model\Shop\Entity
 * @var $orderProduct \Model\Order\Product\Entity
 * @var $product      \Model\Product\Entity
 */
?>

<div style="width: 900px;">

    <div class="bFormSave">
        <h2>Номер вашего заказа: <?= $order->getNumber() ?></h2>

        <p>
            Дата заказа: <?= $page->helper->humanizeDate(new \DateTime()) ?>.
            <br>Сумма заказа: <?= $page->helper->formatPrice($order->getSum()) ?> <span class="rubl">p</span>
        </p>
        <span>Спасибо за размещение заказа! Оператор подтвердит его звонком или смс на Ваш контактный номер!</span>
    </div>

    <? if (\App::config()->analytics['enabled']): ?>
        <div id="gooReMaQuickOrder" class="jsanalytics"></div>
        <div id="adriverOrder" data-vars="<?= $page->json($orderData) ?>" class="jsanalytics"></div>
        <div id="heiasComplete" data-vars="<?= $page->json($orderData) ?>" class="jsanalytics"></div>
        <div id="myThingsOrderData" data-value="<?= $page->json($myThingsOrderData) ?>"></div>
        <!-- Efficient Frontiers -->
        <img src="http://pixel.everesttech.net/3252/t?ev_Orders=0&amp;ev_Revenue=0&amp;ev_Quickorders=1&amp;ev_Quickrevenue=<?= $order->getSum() ?>&amp;ev_transid=<?= $order->getNumber() ?>" width="1" height="1" />
    <? endif ?>


    <script type="text/javascript">
        function runAnalitics() {
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

            if (typeof(window.adBelnder) != 'undefined') window.adBelnder.addOrder(<?= str_replace(',', '.', $order->getSum()) ?>);

            if (typeof(MyThings) != "undefined" && typeof($('#myThingsOrderData').data('value')) == "object") {
                MyThings.Track($('#myThingsOrderData').data('value'))
            }
        }
    </script>

    <div class="line"></div>

    <div class="bFormB2">
        <div class="fr">
            <a href="<?= $page->url('homepage') ?>" onclick="$('#order1click-container-new').trigger('close'); return false">Продолжить покупки</a> <span>&gt;</span>
        </div>
    </div>

</div>