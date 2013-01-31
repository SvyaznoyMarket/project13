<?php
/**
 * @var $page         \View\Layout
 * @var $orders       \Model\Order\Entity[]
 * @var $productsById \Model\Product\Entity[]
 * @var $servicesById \Model\Product\Service\Entity[]
 * @var $shopsById    \Model\Shop\Entity[]
 */
?>

<?php foreach ($orders as $order): ?>
    <div id="adblenderCost" data-vars="<?= $order->getSum() ?>" class="jsanalytics"></div>
<?php endforeach ?>


<script type="text/javascript">
    function orderAnalyticsRun() {
        <?php foreach ($orders as $order): ?>
            <?
                $shop = $order->getShopId() && isset($shopsById[$order->getShopId()]) ? $shopsById[$order->getShopId()] : null;
                $deliveries = $order->getDelivery();
                /** @var $delivery \Model\Order\Delivery\Entity */
                $delivery = reset($deliveries);
            ?>

            if (typeof _gaq != 'undefined') _gaq.push(['_addTrans',
                '<?= $order->getNumber() ?>', // Номер заказа
                '<?= $shop ? $page->escape($shop->getName()) : '' ?>', // Название магазина (Необязательно)
                '<?= str_replace(',', '.', $order->getSum()) ?>', // Полная сумма заказа (дроби через точку)
                '', // налог
                '<?= $delivery ? $delivery->getPrice() : 0 ?>', // Стоимость доставки (дроби через точку)
                '<?= $order->getCity() ? $page->escape($order->getCity()->getName()) : '' ?>', // Город доставки (Необязательно)
                '', // Область (необязательно)
                '' // Страна (нобязательно)
            ]);

            // _addItem: Номер заказа, Артикул, Название товара, Категория товара, Стоимость 1 единицы товара, Количество товара
            <? foreach ($order->getProduct() as $orderProduct): ?>
            <?
                $product = isset($productsById[$orderProduct->getId()]) ? $productsById[$orderProduct->getId()] : null;
                if (!$product) continue;

                $categories = $product->getCategory();
                $category = array_pop($categories);
                $rootCategory = array_shift($categories);

                $categoryName = ($rootCategory && ($rootCategory->getId() != $category->getId()))
                    ? ($rootCategory->getName() . ' - ' . $category->getName())
                    : $category->getName();
            ?>

                if (typeof _gaq != 'undefined') _gaq.push(['_addItem', '<?= implode("','", array($order->getNumber(), $product->getArticle(), $page->escape($product->getName()), $page->escape($categoryName), $orderProduct->getPrice(), $orderProduct->getQuantity())) ?>']);
            <?php endforeach ?>

            <? foreach ($order->getService() as $orderService): ?>
            <?
            $service = isset($servicesById[$orderService->getId()]) ? $servicesById[$orderService->getId()] : null;
            if (!$service) continue;

            $categories = $service->getCategory();
            $category = array_pop($categories);
            $rootCategory = array_shift($categories);

            $categoryName = ($rootCategory && ($rootCategory->getId() != $category->getId()))
                ? ($rootCategory->getName() . ' - ' . $category->getName())
                : $category->getName();
            ?>

                if (typeof _gaq != 'undefined') _gaq.push(['_addItem', '<?= implode("','", array($order->getNumber(), $service->getToken(), $page->escape($service->getName()), $page->escape($categoryName), $orderService->getPrice(), $orderService->getQuantity())) ?>']);
            <?php endforeach ?>

                if (typeof _gaq != 'undefined') _gaq.push(['_trackTrans']);
            <? endforeach ?>
    }
</script>


<script type="text/javascript">
    var yaParams =
            [
                <? foreach ($orders as $i => $order): ?>
                {
                    order_id:'<?= $order->getNumber() ?>',
                    order_price: <?= str_replace(',', '.', $order->getSum()) ?>,
                    currency:'RUR',
                    exchange_rate:1,
                    goods:[
                        <? foreach ($order->getProduct() as $j => $orderProduct): ?>
                        <?
                            $product = isset($productsById[$orderProduct->getId()]) ? $productsById[$orderProduct->getId()] : null;
                            if (!$product) continue;
                        ?>

                            {
                                id:'<?= $product->getArticle() ?>',
                                name:'<?= $page->escape($product->getName()) ?>',
                                price: <?= $orderProduct->getPrice() ?>,
                                quantity: <?= $orderProduct->getQuantity() ?>

                            }<?php echo ((bool)$order->getService() || ($j < (count($order->getProduct()) - 1))) ? ',' : "\n" ?>

                        <? endforeach ?>

                        <? foreach ($order->getService() as $j => $orderService): ?>
                            <?
                            $service = isset($servicesById[$orderService->getId()]) ? $servicesById[$orderService->getId()] : null;
                            if (!$service) continue;
                            ?>

                            {
                                id:'<?= $service->getToken() ?>',
                                name:'<?= $page->escape($service->getName()) ?>',
                                price: <?= $orderService->getPrice() ?>,
                                quantity: <?= $orderService->getQuantity() ?>

                            }<?php echo ($j < (count($order->getService()) - 1)) ? ',' : '' ?>

                        <? endforeach ?>
                    ]
                }<?= ($i < (count($orders) - 1)) ? ',' : "\n" ?>

                <? endforeach ?>
            ]
</script>


<div id="mixmarket" class="jsanalytics"></div>
<div id="gooReMaSuccess" class="jsanalytics"></div>
<div id="marketgidOrderSuccess" class="jsanalytics"></div>

<?php $myThingsData = []; ?>
<?php foreach ($orders as $i => $order):
    $jsonOrdr = array(
    'order_article'    => implode(',', array_map(function ($orderProduct) {
        /** @var $orderProduct \Model\Order\Product\Entity */
        return $orderProduct->getId();
    }, $order->getProduct())),
    'order_id'         => $order->getNumber(),
    'order_total'      => $order->getSum(),
    'product_quantity' => implode(',', array_map(function ($orderProduct) {
        /** @var $orderProduct \Model\Order\Product\Entity */
        return $orderProduct->getQuantity();
    }, $order->getProduct())),
    );
    $myThingsData[] = array(
        'EventType' => 'MyThings.Event.Conversion',
        'Action' => '9902',
        'TransactionReference' => $order->getNumber(),
        'TransactionAmount' => str_replace(',', '.', $order->getSum()), // Полная сумма заказа (дроби через точку
        'Products' => array_map(function($orderProduct){
            /** @var $orderProduct \Model\Order\Product\Entity  */
            return array('id' => $orderProduct->getId(), 'price' => $orderProduct->getPrice(), 'qty' => $orderProduct->getQuantity());
        }, $order->getProduct()),
    );
    ?>

    <div id="heiasComplete" data-vars="<?= $page->json($jsonOrdr) ?>" class="jsanalytics"></div>

    <div id="adriverOrder" data-vars="<?= $page->json($jsonOrdr) ?>" class="jsanalytics"></div>

    <!-- Efficient Frontiers -->
    <img src="http://pixel.everesttech.net/3252/t?ev_Orders=1&amp;ev_Revenue=<?= $order->getSum() ?>&amp;ev_Quickorders=0&amp;ev_Quickrevenue=0&amp;ev_transid=<?= $order->getNumber() ?>" width="1" height="1" />

<?php endforeach ?>

    <div id="myThingsTracker" data-value="<?= $page->json($myThingsData) ?>" class="jsanalytics"></div>
