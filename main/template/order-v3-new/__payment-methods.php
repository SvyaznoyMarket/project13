<?php

/**
 * @param \Helper\TemplateHelper $helper
 * @param \Model\OrderDelivery\Entity\Order $order
 */
return function (
    \Helper\TemplateHelper $helper,
    \Model\OrderDelivery\Entity\Order $order
) {
    $isOrderWithCart = \App::abTest()->isOrderWithCart();
    $paymentMethods = (new \View\Partial\PaymentMethods())->execute($helper, $order->possible_payment_methods, $order->payment_method_id);
    /** @var \Model\OrderDelivery\Entity\Order\Discount|null $onlineDiscount */
    $onlineDiscount = call_user_func(function() use($order) {
        foreach ($order->discounts as $discount) {
            if ($discount->type === 'online') {
                return $discount;
            }
        }

        return null;
    });
?>
    <div class="payments-types-table order-payment <?= ($isOrderWithCart ? 'order-payment' : '') ?>">
        <div class="payments-types-table__cell">
            <div class="payments-types-table__head">Выбрать способ оплаты</div>
            <div class="order-ctrl__custom-select js-order-payment-methods-dropbox-container">
                <span class="order-ctrl__custom-select-item_title js-order-payment-methods-dropbox-opener">
                    <? if (!empty($paymentMethods['selectedPaymentMethod']['name'])): ?>
                        <?= $helper->escape($paymentMethods['selectedPaymentMethod']['name']) ?>
                    <? else: ?>
                        ...
                    <? endif ?>
                </span>

                <ul class="paymentMethods order-ctrl__custom-select-list payment-methods__lst js-order-payment-methods-dropbox-content">
                    <? foreach ($paymentMethods['paymentMethods'] as $paymentMethod): ?>
                        <li class="payment-methods__i order-ctrl__custom-select-item <? if ($paymentMethod['discount']): ?><? endif ?> js-order-payment-methods-dropbox-item">
                            <?
                            $elementId = sprintf('order_%s-paymentMethod_%s', md5($order->block_name), $paymentMethod['id']);
                            ?>

                            <input
                                id="<?= $elementId ?>"
                                type="radio"
                                name="payment-type-<?= md5($order->block_name) ?>[]"
                                value="<?= $paymentMethod['id'] ?>"
                                <? if ($paymentMethod['isOnline']): ?>
                                    data-online="true"
                                <? endif ?>
                                data-value="<?= $helper->json([
                                    'block_name'        => $order->block_name,
                                    'payment_method_id' => $paymentMethod['id'],
                                ]) ?>"
                                class="customInput customInput-defradio2 js-order-paymentMethod js-customInput"
                                <?= $paymentMethod['selected'] ? 'checked' : '' ?>
                            />
                            <label for="<?= $elementId ?>" class="customLabel customLabel-defradio2 <?= $paymentMethod['selected'] ? 'mChecked' : '' ?>">
                                <span class="payment-methods__i-txt"><?= $helper->escape($paymentMethod['name']) ?></span>
                                <?= $helper->renderWithMustache('order-v3-new/paymentMethod/discount', ['discount' => $paymentMethod['discount'], 'single' => true]) ?>
                            </label>
                        </li>
                    <? endforeach ?>
                </ul>
            </div>
        </div>

        <div class="payments-types-table__cell payments-types-table__cell_discount">
            <? /* Скидка за онлайн-оплату */ ?>
            <? if ($onlineDiscount): ?>
                <div class="payments-types-table__discount payments-types-table__discount_online">
                    <?= $onlineDiscount->name . ' ' . $helper->formatPrice($onlineDiscount->discount) ?>

                    <? if ($onlineDiscount->unit === 'rub'): ?>
                        <span class="rubl">p</span>
                    <? else: ?>
                        <?= $helper->escape($onlineDiscount->unit) ?>
                    <? endif ?>
                </div>
            <? endif ?>

            <? if ($order->prepaid_sum): ?>
                <div class="payments-types-table__discount">Требуется предоплата</div>
            <? endif ?>
        </div>
    </div>
<? };