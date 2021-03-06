<?php

/**
 * @param \Helper\TemplateHelper $helper
 * @param \Model\OrderDelivery\Entity\Order $order
 */
return function (
    \Helper\TemplateHelper $helper,
    \Model\OrderDelivery\Entity\Order $order
) {
    $paymentMethods = (new \View\Partial\PaymentMethods())->execute($helper, $order->possible_payment_methods, $order->payment_method_id);
?>
    <div class="payments-types-table order-payment">
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
                                    <span class="payment-methods__i-txt <? if ($paymentMethod['id'] == 22): ?>payment-methods__i-robokassa<? endif ?>"><?= $helper->escape($paymentMethod['name']) ?></span>
                                    <?= $helper->renderWithMustache('order-v3-new/paymentMethod/discount', ['discount' => $paymentMethod['discount'], 'single' => true]) ?>
                                </label>
                        </li>
                    <? endforeach ?>
                </ul>
            </div>
        </div>

        <? if ($order->prepaid_sum): ?>
            <div class="payments-types-table__prepayment">Требуется предоплата</div>
        <? endif ?>
    </div>
<? };