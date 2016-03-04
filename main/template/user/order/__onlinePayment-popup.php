<?php

/**
 * @param \Helper\TemplateHelper $helper
 * @param \Model\Order\Entity $order
 * @param \Model\PaymentMethod\PaymentEntity $paymentEntity
 * @return string
 */
$f = function(
    \Helper\TemplateHelper $helper,
    \Model\Order\Entity $order,
    \Model\PaymentMethod\PaymentEntity $paymentEntity = null
) {

if (!$paymentEntity) {
    return '';
}

$formUrl = \App::router()->generate('orderV3.paymentForm');

$containerId = sprintf('id-paymentForm-%s-container', md5($order->id . '-' . $order->numberErp));
$sumContainerId = sprintf('id-onlineSum-%s-container', md5($order->id . '-' . $order->numberErp));

$onlinePaymentMethods = array_filter($paymentEntity->methods, function(\Model\PaymentMethod\PaymentMethod\PaymentMethodEntity $paymentMethod) {
    return $paymentMethod->isOnline;
});

$isOnlinePaymentMethodDiscountExists = (bool)array_filter($onlinePaymentMethods, function(\Model\PaymentMethod\PaymentMethod\PaymentMethodEntity $paymentMethod) {
    return $paymentMethod->discount;
});
?>

<div class="payments-popup js-payment-popup">
        <div class="js-payment-popup-closer payments-popup__closer"></div>

        <div class="orderPayment_msg_head">
            Оплатить онлайн
            <? if ($isOnlinePaymentMethodDiscountExists): ?>
                со скидкой
            <? endif ?>
        </div>
        <div class="order-payment__sum-msg">
        <?
            $sum = $paymentEntity ? ($paymentEntity->getPaymentSumByMethodId($order->paymentId)) : null;
            if (!$sum) {
                $sum = $order->paySum;
            }
        ?>
            К оплате <span class="order-payment__sum"><span class="<?= $sumContainerId ?>"><?= $helper->formatPrice($sum) ?></span> <span class="rubl">p</span></span>
        </div>

        <? foreach ((new \View\Partial\PaymentMethods())->execute($helper, $onlinePaymentMethods, $order->paymentId)['paymentMethodGroups'] as $paymentMethodGroup): ?>
            <ul class="payment-methods__lst <? if ($paymentMethodGroup['discount']): ?>payment-methods__lst_discount<? endif ?>">
                <? foreach ($paymentMethodGroup['paymentMethodGroups'] as $paymentMethodGroup2): ?>
                    <? if (count($paymentMethodGroup2['paymentMethods']) == 1): ?>
                    <?
                        $elementId = sprintf('order_%s-paymentMethod_%s', $order->id, $paymentMethodGroup2['paymentMethods'][0]['id']);
                        $name = sprintf('paymentMethodId_%s', $order->id);
                    ?>

                        <li class="payment-methods__i">
                            <input
                                id="<?= $elementId ?>"
                                type="radio"
                                name="<?= $name ?>"
                                value="<?= $paymentMethodGroup2['paymentMethods'][0]['id'] ?>"
                                data-url="<?= $formUrl ?>"
                                data-value="<?= $helper->json([
                                    'action' => isset($paymentMethodGroup['discount']) ? $paymentMethodGroup['discount']['action'] : null,
                                    'method' => $paymentMethodGroup2['paymentMethods'][0]['id'],
                                    'order'  => $order->id,
                                    'token'  => $order->getAccessToken(),
                                    'number' => $order->number,
                                    'mobile' => $order->mobilePhone,
                                    'url'    => \App::router()->generate('user.orders', [], true),
                                ]) ?>"
                                <? if ($sum = (empty($paymentMethodGroup['discount']['sum']) ? $order->paySum : $paymentMethodGroup['discount']['sum'])): ?>
                                    data-sum="<?= $helper->json([
                                        'value' => $helper->formatPrice($sum)
                                    ])?>"
                                <? endif ?>
                                data-relation="<?= $helper->json([
                                    'formContainer'     => '.' . $containerId,
                                    'sumContainer'      => '.' . $sumContainerId,
                                ]) ?>"
                                class="customInput customInput-defradio2 js-customInput js-order-onlinePaymentMethod"
                                <? if ($paymentMethodGroup2['paymentMethods'][0]['selected']): ?>
                                    checked="checked"
                                    data-checked="true"
                                <? endif ?>
                                />
                            <label for="<?= $elementId ?>" class="customLabel customLabel-defradio2<? if ($paymentMethodGroup2['paymentMethods'][0]['selected']): ?> mChecked<? endif ?>">
                                <?= $helper->escape($paymentMethodGroup2['paymentMethods'][0]['name']) ?>
                                <? if ($paymentMethodGroup2['paymentMethods'][0]['icon']): ?>
                                    <img class="payment-methods__img" src="<?= $helper->escape($paymentMethodGroup2['paymentMethods'][0]['icon']) ?>" alt="<?= $helper->escape($paymentMethodGroup2['paymentMethods'][0]['name']) ?>" />
                                <? endif ?>
                            </label>
                        </li>
                    <? endif ?>
                <? endforeach ?>
            </ul>

            <?= $helper->renderWithMustache('order-v3-new/paymentMethod/discount', ['discount' => $paymentMethodGroup['discount']]) ?>
        <? endforeach ?>

        <div class="payments-popup__pay <?= $containerId ?>"></div>
        <p class="orderPayment_msg_hint">Вы будете перенаправлены на сайт платежной системы.</p>
    </div>

<? }; return $f;