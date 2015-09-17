<?php

$f = function (
    \Helper\TemplateHelper $helper,
    \Model\OrderDelivery\Entity\Order $order
) {
    $isOrderWithCart = \App::abTest()->isOrderWithCart();

    $paymentMethods = $order->possible_payment_methods;
    $imagesByPaymentId = [
        '1' => null,
        '2' => 'pay-card.png',
        '8' => 'pay-psb.png',
        'yandex' => 'pay-yandex.png',
        'webmoney' => 'pay-webmoney.png',
        'qiwi' => 'pay-qiwi.png',
    ];

    $onlinePaymentMethodIds = ['2', '5', '8'];
?>

    <div class="paymentMethods <?= ($isOrderWithCart ? 'order-payment' : '') ?>">
        <strong>Способы оплаты</strong>

        <!-- Новые способы оплаты - статика -->
        <div class="payment-methods__discount discount">
            <span class="discount__pay-type">Онлайн-оплата</span>
            <span class="discount__val">Скидка 15%</span>
        </div>
        <ul class="payment-methods__lst">
        <? foreach ($paymentMethods as $paymentMethod): ?>
        <?
            if (in_array($paymentMethod->id, ['10'])) continue;
            $checked = $order->payment_method_id == $paymentMethod->id;
        ?>
            <li class="payment-methods__i">
                <input
                    id="<?= sprintf('paymentMethod-%s', $paymentMethod->id) ?>"
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
                <label for="<?= sprintf('paymentMethod-%s', $paymentMethod->id) ?>" class="customLabel customLabel-defradio2 <?= $checked ? 'mChecked' : '' ?>">
                    <?= $paymentMethod->name ?>
                    <? if ($image = (isset($imagesByPaymentId[$paymentMethod->id]) ? $imagesByPaymentId[$paymentMethod->id] : null)): ?>
                        <img class="payment-methods__img" src="/styles/order-new/img/payment/<?= $image ?>">
                    <? endif ?>
                </label>
            </li>
        <? endforeach ?>
        </ul>
    </div>

<? };

return $f;