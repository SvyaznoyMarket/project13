<?php return function(
    \Helper\TemplateHelper $helper,
    \Model\OrderDelivery\Entity\Order $order
) {
?>
    <? if ($order->delay_discount): ?>
        <div class="order-bill__delay-discount">
            <input id="delayDiscount-<?= $helper->escape($order->block_name) ?>" type="checkbox" class="customInput customInput-checkbox js-customInput js-order-changeHasDelayDiscount" name="" value="1" <? if ($order->has_delay_discount): ?>checked="checked"<? endif ?> />
            <label for="delayDiscount-<?= $helper->escape($order->block_name) ?>" class="customLabel customLabel-checkbox">Получить скидку за увеличение срока доставки (+3 дня)</label>
        </div>
    <? endif ?>
<? } ?>