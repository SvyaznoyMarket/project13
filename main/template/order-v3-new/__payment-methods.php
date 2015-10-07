<?php

/**
 * @param \Helper\TemplateHelper $helper
 * @param \Model\OrderDelivery\Entity\Order $order
 * @param \Model\OrderDelivery\Entity $orderDelivery
 */
$f = function (
    \Helper\TemplateHelper $helper,
    \Model\OrderDelivery\Entity\Order $order,
    \Model\OrderDelivery\Entity $orderDelivery
) {
    /** @var \Model\OrderDelivery\Entity\PaymentMethod $paymentMethod */

    $isOrderWithCart = \App::abTest()->isOrderWithCart();

    $paymentMethodsByDiscount = [];
    foreach ($order->possible_payment_methods as $paymentMethod) {
        $index = in_array($paymentMethod->id, \App::config()->payment['discountIds']) ? 0 : 1;
        if (in_array($paymentMethod->id, ['1', '2'])) {
            $paymentMethodsByDiscount[$index]['grouped'][] = $paymentMethod;
        } else {
            $paymentMethodsByDiscount[$index][][] = $paymentMethod;
        }
    }
    ksort($paymentMethodsByDiscount);
?>

    <div class="paymentMethods <?= ($isOrderWithCart ? 'order-payment' : '') ?>">
        <strong>Способы оплаты</strong>

        <div class="payment-methods__discount discount">
            <span class="discount__pay-type">Онлайн-оплата</span>
            <span class="discount__val">Скидка 15%</span>
        </div>

        <ul class="payment-methods__lst">
        <? foreach ($paymentMethodsByDiscount as $paymentMethodChunk): ?>
            <? foreach ($paymentMethodChunk as $groupIndex => $paymentMethods): ?>
            <?
                $elementId = sprintf('paymentMethod-%s', $groupIndex);
                $checked = in_array($order->payment_method_id, array_keys($paymentMethods));
            ?>
                <li class="payment-methods__i">
                <? if (count($paymentMethods) > 1): ?>
                        <input
                            id="<?= $elementId ?>"
                            type="radio"
                            name="payment-type[]"
                            value=""
                            class="customInput customInput-defradio2 js-customInput"
                            <?= $checked ? 'checked' : '' ?>
                        />
                        <label for="<?= $elementId ?>" class="customLabel customLabel-defradio2 <?= $checked ? 'mChecked' : '' ?>"></label>
                        <select class="customSel-inner">
                        <? foreach ($paymentMethods as $paymentMethod): ?>
                        <?
                            $checked = $order->payment_method_id == $paymentMethod->id;
                        ?>
                            <option value="<?= $paymentMethod->id ?>" <?= $checked ? 'selected' : '' ?>><?= $paymentMethod->name ?></option>
                        <? endforeach ?>
                        </select>
                <? else: ?>
                <?
                    $paymentMethod = reset($paymentMethods);

                    $elementId = sprintf('paymentMethod-%s', $paymentMethod->id);
                    $checked = $order->payment_method_id == $paymentMethod->id;
                ?>
                    <input
                        id="<?= $elementId ?>"
                        type="radio"
                        name="payment-type[]"
                        value="<?= $paymentMethod->id ?>"
                        <? if ($paymentMethod->is_online): ?>data-online="true"<? endif ?>
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
                <? endif ?>
                </li>
            <? endforeach ?>
            <li class="payment-methods__i"><br /></li>
        <? endforeach ?>
        </ul>
    </div>

<? };

return $f;