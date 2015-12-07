<?php

/**
 * @param \Helper\TemplateHelper $helper
 * @param \Model\OrderDelivery\Entity\Order $order
 */
$f = function (
    \Helper\TemplateHelper $helper,
    \Model\OrderDelivery\Entity\Order $order
) {
    $isOrderWithCart = \App::abTest()->isOrderWithCart();
?>
    <div class="payments-types-table <?= ($isOrderWithCart ? 'order-payment' : '') ?>">
        <div class="payments-types-table__head"><strong>Оплата</strong></div>

        <div class="paymentMethods">
            <? foreach ((new \View\Partial\PaymentMethods())->execute($helper, $order->possible_payment_methods, $order->payment_method_id)['paymentMethodGroups'] as $paymentMethodGroup): ?>
                <ul class="payment-methods__lst <? if ($paymentMethodGroup['discount']): ?>payment-methods__lst_discount<? endif ?>">
                    <? foreach ($paymentMethodGroup['paymentMethodGroups'] as $paymentMethodGroup2): ?>
                        <li class="payment-methods__i">
                            <?
                            $elementId = sprintf('order_%s-paymentMethod_%s', md5($order->block_name), $paymentMethodGroup2['paymentMethods'][0]['id']);
                            ?>

                            <input
                                id="<?= $elementId ?>"
                                type="radio"
                                name="payment-type-<?= md5($order->block_name) ?>[]"
                                value="<?= $paymentMethodGroup2['paymentMethods'][0]['id'] ?>"
                                <? if ($paymentMethodGroup2['paymentMethods'][0]['isOnline']): ?>
                                    data-online="true"
                                <? endif ?>
                                data-value="<?= $helper->json([
                                    'block_name'        => $order->block_name,
                                    'payment_method_id' => $paymentMethodGroup2['paymentMethods'][0]['id'],
                                ]) ?>"
                                class="customInput customInput-defradio2 js-order-paymentMethod js-customInput"
                                <?= $paymentMethodGroup2['selected'] ? 'checked' : '' ?>
                                />

                            <? if (count($paymentMethodGroup2['paymentMethods']) > 1): ?>
                                <label for="<?= $elementId ?>" class="customLabel customLabel-defradio2 <?= $paymentMethodGroup2['selected'] ? 'mChecked' : '' ?>"><?= $helper->escape($paymentMethodGroup2['name']) ?></label>

                                <select class="customSel-inner js-order-paymentMethod">
                                    <? foreach ($paymentMethodGroup2['paymentMethods'] as $paymentMethod): ?>
                                        <option
                                            value="<?= $paymentMethod['id'] ?>"
                                            <?= $paymentMethod['selected'] ? 'selected' : '' ?>
                                            <? if ($paymentMethod['isOnline']): ?>
                                                data-online="true"
                                            <? endif ?>
                                            data-value="<?= $helper->json([
                                                'block_name'        => $order->block_name,
                                                'payment_method_id' => $paymentMethod['id'],
                                            ]) ?>"
                                            ><?= $helper->escape($paymentMethod['name']) ?></option>
                                    <? endforeach ?>
                                </select>
                            <? else: ?>
                                <label for="<?= $elementId ?>" class="customLabel customLabel-defradio2 <?= $paymentMethodGroup2['selected'] ? 'mChecked' : '' ?>">
                                    <?= $helper->escape($paymentMethodGroup2['paymentMethods'][0]['name']) ?>
                                    <? if ($paymentMethodGroup2['paymentMethods'][0]['icon']): ?>
                                        <img class="payment-methods__img" src="<?= $helper->escape($paymentMethodGroup2['paymentMethods'][0]['icon']) ?>" alt="<?= $helper->escape($paymentMethodGroup2['paymentMethods'][0]['name']) ?>" />
                                    <? endif ?>
                                </label>
                            <? endif ?>
                        </li>
                    <? endforeach ?>
                </ul>

                <?= $helper->renderWithMustache('order-v3-new/paymentMethod/discount', ['discount' => $paymentMethodGroup['discount']]) ?>
            <? endforeach ?>
        </div>
    </div>
<? };

return $f;