<?php return function(
    \Helper\TemplateHelper $helper,
    \Model\OrderDelivery\Entity\Order $order
) {
?>
    <? if ($order->delay_discount): ?>
        <div class="order-bill__delay-discount">
            <input id="delayDiscount" type="checkbox" class="customInput customInput-checkbox js-customInput js-order-changeHasDelayDiscount" name="" value="1" <? if ($order->has_delay_discount): ?>checked="checked"<? endif ?> />
            <label for="delayDiscount" class="customLabel customLabel-checkbox">Получить скидку за увеличение срока доставки (+3 дня)</label>
        </div>
    <? endif ?>
<? } ?>