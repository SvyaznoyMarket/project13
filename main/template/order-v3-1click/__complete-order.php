<?php

/**
 * @param \Helper\TemplateHelper $helper
 * @param \Model\Order\Entity[] $orders
 * @param \Model\Product\Entity[] $productsById
 * @param \Model\Shop\Entity[] $shopsById
 * @return string
 */
$f = function(
    \Helper\TemplateHelper $helper,
    $orders,
    $productsById = [],
    $shopsById = []
) {
    /** @var \Model\Product\Entity $product */

    /** @var \Model\Order\Entity $order */
    $order = reset($orders);
    if (!$order) return '';

    $product = reset($productsById);

    $deliveries = $order->getDelivery();
    /** @var \Model\Order\Delivery\Entity|null $delivery */
    $delivery = reset($deliveries);

    /** @var \Model\Shop\Entity|null $shop */
    $shop = $order->getShopId() && isset($shopsById[$order->getShopId()]) ? $shopsById[$order->getShopId()] : null;

    // google analytics для _gaq.push
    $analyticsData = [];
    try {
        $analyticsData[] = [
            '_addTrans',
            $order->getNumberErp(), //  ID транзакции, Номер заказа
            $shop ? $helper->escape($shop->getName()) : '', // Название магазина (Необязательно)
            str_replace(',', '.', $order->getPaySum()), // Полная сумма заказа (дроби через точку)
            '',         // new // Налог. Общая сумма из пукнта выше умноженная на 0,18.
            $delivery ? str_replace(',', '.', $delivery->getPrice()) : '0', // Стоимость доставки (дроби через точку)
            $order->getCity() ? $helper->escape($order->getCity()->getName()) : '', // Город доставки (Необязательно)
            '', // Область (необязательно)
            '' // Страна (нобязательно)
        ];
        if ($product) {
            $categories = $product->getCategory();
            $category = array_pop($categories);
            $rootCategory = array_shift($categories);

            $categoryName = ($category && $rootCategory && ($rootCategory->getId() != $category->getId()))
                ? ($rootCategory->getName() . ' - ' . $category->getName())
                : $category->getName();
            $productName = $order->isPartner ? $product->getName() . ' (marketplace)' : $product->getName();

            $analyticsData[] = [
                '_addItem',
                "'" . implode("','", [ // Номер заказа, Артикул, Название товара, Категория товара, Стоимость 1 единицы товара, Количество товара
                    $order->getNumberErp(),
                    $product->getArticle(),
                    $helper->escape($productName),
                    $helper->escape($categoryName),
                    $order->getProduct()[0]->getPrice(),
                    $order->getProduct()[0]->getQuantity()
                ]) . "'",
            ];
        }
        $analyticsData[] = [
            '_trackTrans',
        ];
    } catch (\Exception $e) {
        \App::logger()->error(['message' => $e->getMessage(), 'sender' => __FILE__ . ' ' . __LINE__], ['template', 'analytics']);
    }
?>

    <div class="orderCol_cnt clearfix">
        <div class="orderCol_lk">
            <img class="orderCol_img" src="<?= $product->getImageUrl(1) ?>">
        </div>

        <? if ($product): ?>
            <div class="orderCol_n">
                <? if ($product->getPrefix()): ?>
                    <?= $product->getPrefix() ?><br/>
                <? endif ?>
                <?= $product->getWebName() ?>
            </div>
        <? endif ?>

        <span class="orderCol_data orderCol_data-price"><?= $helper->formatPrice($order->getProduct()[0]->getPrice()) ?> <span class="rubl">p</span></span>
    </div>

    <? if ($delivery): ?>
        <div class="orderCol_f clearfix">
            <div class="orderCol_f_r">
                <span class="orderCol_summ">
                    <? if ($delivery->getPrice()): ?>
                        <?= $helper->formatPrice($delivery->getPrice()) ?> <span class="rubl">p</span></span>
                    <? else: ?>
                        Бесплатно
                    <? endif ?>
                </span>
                <span class="orderCol_summt">
                    <? if ('1' == $delivery->getTypeId()): ?>
                        Доставка
                    <? else: ?>
                        Самовывоз:
                    <? endif ?>
                </span>

                <span class="orderCol_summ"><?= $helper->formatPrice($order->getSum()) ?> <span class="rubl">p</span></span>
                <span class="orderCol_summt">Итого:</span>
            </div>
        </div>
    <? endif ?>


    <? if (\App::config()->googleAnalytics['enabled']): ?>
        <div class="jsGoogleAnalytics-push" data-value="<?= $helper->json($analyticsData) ?>"></div>
    <? endif ?>

<? }; return $f;
