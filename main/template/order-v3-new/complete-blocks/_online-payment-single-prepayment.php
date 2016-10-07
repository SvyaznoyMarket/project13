<?php

/**
 * @param \Helper\TemplateHelper $helper
 * @param \Model\Order\Entity $order
 * @param \Model\PaymentMethod\PaymentEntity|null $orderPayment
 * @param bool $onlineRedirect
 * @return string
 */
$f = function(
    \Helper\TemplateHelper $helper,
    \Model\Order\Entity $order,
    \Model\PaymentMethod\PaymentEntity $orderPayment = null,
    $onlineRedirect = false // SITE-6641
) {
    /** @var \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity|null $paymentMethod */
    $paymentMethod = null;
    foreach ($orderPayment->methods as $iPaymentMethod) {
        if ($iPaymentMethod->id == $order->paymentId) {
            $paymentMethod = $iPaymentMethod;
            break;
        }
    }
    if (!$paymentMethod) {
        return '';
    }

    $formUrl = \App::router()->generateUrl('orderV3.paymentForm');

    $checkedPaymentMethodId = $order->getPaymentId();

    $sumContainerId = 'id-onlineDiscountSum-container';
    $containerId = sprintf('id-order-%s-paymentMethod-container', $order->id);

    /** @var \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity[] $paymentMethods */
    $onlinePaymentMethods = array_filter($orderPayment->methods, function(\Model\PaymentMethod\PaymentMethod\PaymentMethodEntity $paymentMethod) {
        return $paymentMethod->isOnline;
    });
?>

    <!-- блок когда была выбран конкретный способ оплаты -->
    <div class="orderPayment orderPayment--static orderPaymentWeb">
        <!-- Заголовок-->
        <!-- Блок в обводке -->
        <div class="orderPayment_block orderPayment_noOnline">

            <div class="orderPayment_msg orderPayment_noOnline_msg">
                <div class="orderPayment_msg_head">
                    Требуется предоплата онлайн
                </div>

                <div class="">
                    <div class="order-payment__sum-msg">
                    <?
                        $sum = ($checkedPaymentMethodId && $orderPayment) ? ($orderPayment->getPaymentSumByMethodId($checkedPaymentMethodId)) : null;
                        if (empty($sum)) {
                            $sum = $order->paySum;
                        }
                    ?>
                        К оплате <span class="order-payment__sum"><span class="<?= $sumContainerId ?>"><?= $helper->formatPrice($sum) ?></span> <span class="rubl">p</span></span>
                    </div>

                    <? foreach ((new \View\Partial\PaymentMethods())->execute($helper, $onlinePaymentMethods, $checkedPaymentMethodId)['paymentMethodGroups'] as $paymentMethodGroup): ?>
                        <ul class="payment-methods__lst <? if ($paymentMethodGroup['discount']): ?>payment-methods__lst_discount<? endif ?>">
                            <? foreach ($paymentMethodGroup['paymentMethods'] as $paymentMethod): ?>
                                <?
                                    $elementId = sprintf('order_%s-paymentMethod_%s', $order->id, $paymentMethod['id']);
                                    $name = sprintf('paymentMethodId_%s', $order->id);
                                ?>

                                <li class="payment-methods__i">
                                    <input
                                        id="<?= $elementId ?>"
                                        type="radio"
                                        name="<?= $name ?>"
                                        value="<?= $paymentMethod['id'] ?>"
                                        data-url="<?= $formUrl ?>"
                                        data-value="<?= $helper->json([
                                            'action' => isset($paymentMethodGroup['discount']) ? $paymentMethodGroup['discount']['action'] : null,
                                            'method' => $paymentMethod['id'],
                                            'order'  => $order->id,
                                            'number' => $order->number,
                                            'url'    => \App::router()->generateUrl('orderV3.complete', ['context' => $order->context], true),
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
                                        <? if ($paymentMethod['selected']): ?>
                                            checked="checked"
                                            data-checked="true"
                                        <? endif ?>
                                    />
                                    <label for="<?= $elementId ?>" class="customLabel customLabel-defradio2<? if ($paymentMethod['selected']): ?> mChecked<? endif ?>">
                                        <?= $helper->escape($paymentMethod['name']) ?>
                                        <? if ($paymentMethod['icon']): ?>
                                            <img class="payment-methods__img" src="<?= $helper->escape($paymentMethod['icon']) ?>" alt="<?= $helper->escape($paymentMethod['name']) ?>" />
                                        <? endif ?>
                                    </label>
                                </li>
                            <? endforeach ?>
                        </ul>

                        <?= $helper->renderWithMustache('order-v3-new/paymentMethod/discount', ['discount' => $paymentMethodGroup['discount']]) ?>
                    <? endforeach ?>

                    <div class="payments-popup__pay <?= $containerId ?>" <? if ($onlineRedirect): ?>data-submit="on"<? endif ?>></div>
                    <p class="orderPayment_msg_hint">Вы будете перенаправлены на сайт платежной системы.</p>
                </div>
        </div>
    </div>
    <!-- END блок когда была выбран конкретный способ оплаты -->

<? }; return $f;