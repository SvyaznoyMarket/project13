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
        $index = $paymentMethod->discount ? 0 : 1;
        if (in_array($paymentMethod->id, ['1', '2'])) {
            $paymentMethodsByDiscount[$index]['При получении'][$paymentMethod->id] = $paymentMethod;
            if ('1' == $paymentMethod->id) {
                $paymentMethod->name = 'наличными';
            } else if ('2' == $paymentMethod->id) {
                $paymentMethod->name = 'банковской картой';
            }
        } else {
            $paymentMethodsByDiscount[$index][][$paymentMethod->id] = $paymentMethod;
        }
    }
    ksort($paymentMethodsByDiscount);
?>
    <div class="payments-types-table table <?= ($isOrderWithCart ? 'order-payment' : '') ?>">

        <div class="payments-types-table__head"><strong>Способы оплаты</strong></div>

        <div class="paymentMethods payments-types-table__types table-cell">
            <ul class="payment-methods__lst">
            <? foreach ($paymentMethodsByDiscount as $paymentMethodChunk): ?>
                <? foreach ($paymentMethodChunk as $groupIndex => $paymentMethods): ?>
                <?
                    $paymentMethod = reset($paymentMethods);

                    $elementId = sprintf('paymentMethod-%s', md5($groupIndex));
                    $checked = in_array($order->payment_method_id, array_keys($paymentMethods));
                ?>
                    <li class="payment-methods__i">
                    <? if (count($paymentMethods) > 1): ?>
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
                            <label for="<?= $elementId ?>" class="customLabel customLabel-defradio2 <?= $checked ? 'mChecked' : '' ?>"><?= $groupIndex ?></label>
                            <select class="customSel-inner js-order-paymentMethod">
                            <? foreach ($paymentMethods as $paymentMethod): ?>
                            <?
                                $checked = $order->payment_method_id == $paymentMethod->id;
                            ?>
                                <option
                                    value="<?= $paymentMethod->id ?>"
                                    <?= $checked ? 'selected' : '' ?>
                                    <? if ($paymentMethod->is_online): ?>data-online="true"<? endif ?>
                                    data-value="<?= $helper->json([
                                        'block_name'        => $order->block_name,
                                        'payment_method_id' => $paymentMethod->id,
                                    ]) ?>"
                                ><?= $paymentMethod->name ?></option>
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
                
            <? endforeach ?>
            </ul>
        </div>

        <div class="payment-methods__discount discount payments-types-table__motivation table-cell">
            <span class="discount__pay-type">Онлайн-оплата</span>
            <span class="discount__val">Скидка 15%</span>
        </div>
    </div>

<? };

return $f;