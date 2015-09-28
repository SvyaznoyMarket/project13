<?php

$f = function (
    \Helper\TemplateHelper $helper,
    \Model\OrderDelivery\Entity\Order $order
) {
    $isOrderWithCart = \App::abTest()->isOrderWithCart();

    $paymentMethods = $order->possible_payment_methods;
    $onlinePaymentMethodIds = ['2', '5', '8'];
?>

    <div class="paymentMethods <?= ($isOrderWithCart ? 'order-payment' : '') ?>">
        <strong>Способы оплаты</strong>

        <div class="payment-methods__discount discount">
        <? if (in_array($order->payment_method_id, \App::config()->payment['discountIds'])): ?>
            <span class="discount__pay-type">Онлайн-оплата</span>
            <span class="discount__val">Скидка 15%</span>
        <? endif ?>
        </div>

        <ul class="payment-methods__lst">
        <? foreach ($paymentMethods as $paymentMethod): ?>
        <?
            if (in_array($paymentMethod->id, ['10'])) continue;
            $elementId = sprintf('paymentMethod-%s', $paymentMethod->id);
            $checked = $order->payment_method_id == $paymentMethod->id;
        ?>
            <li class="payment-methods__i">
                <input
                    id="<?= $elementId ?>"
                    type="radio"
                    name="payment-type[]"
                    value="<?= $paymentMethod->id ?>"
                    <? if (in_array($paymentMethod->id, $onlinePaymentMethodIds)): ?>data-online="true"<? endif ?>
                    data-value="<?= $helper->json([
                        'block_name'        => $order->block_name,
                        'payment_method_id' => $paymentMethod->id,
                    ]) ?>"
                    class="customInput customInput-defradio2 js-order-paymentMethod js-customInput"
                    <?= $checked ? 'checked' : '' ?>
                />
                <label for="<?= $elementId ?>" class="customLabel customLabel-defradio2 <?= $checked ? 'mChecked' : '' ?>">
                    <?= $paymentMethod->name ?>
                    <? if ($image = $paymentMethod->icon): ?>
                        <img class="payment-methods__img" src="<?= $image ?>">
                    <? endif ?>
                </label>
            </li>
        <? endforeach ?>
        </ul>
    </div>

<? };

return $f;