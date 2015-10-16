<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product,
    $actionChannelName
) {

    if (!(\App::config()->product['lowerPriceNotification'] && $product->getRootCategory() && $product->getRootCategory()->getPriceChangeTriggerEnabled())) {
        return '';
    }

    $price = ($product->getRootCategory() && $product->getRootCategory()->getPriceChangePercentTrigger())
        ? round($product->getPrice() * $product->getRootCategory()->getPriceChangePercentTrigger())
        : 0;
?>

    <!--noindex-->
    <div class="priceSale js-lowPriceNotifier" data-values="<?= $helper->json([
        'price' => $price && $price < $product->getPrice() ? $helper->formatPrice($price) : null,
        'actionChannelName' => $actionChannelName,
        'userOfficeUrl' => $helper->url(\App::config()->user['defaultRoute']),
        'submitUrl' => $helper->url('product.notification.lowerPrice', ['productId' => $product->getId()]),
    ]) ?>">
        <span class="dotted js-lowPriceNotifier-opener">Узнать о снижении цены</span>
    </div>
    <!--/noindex-->

    <script id="tpl-lowPriceNotifier-popup" type="text/html" data-partial="<?= $helper->json([]) ?>">
        <?= file_get_contents(\App::config()->templateDir . '/product/lowPriceNotifier/popup.mustache') ?>
    </script>

<? };