<?php

/**
 * @param \Helper\TemplateHelper $helper
 * @param string $url
 * @param array $form
 * @param \Model\Order\Entity|null $order
 * @param bool $requireValidation
 * @param string $paymentMethodId
 * @return string
 */
$f = function(
    \Helper\TemplateHelper $helper,
    $url,
    array $form,
    \Model\Order\Entity $order = null,
    $requireValidation = false,
    $paymentMethodId = '',
    $orderAction = ''
) {
    // validation
    if (!$url || !$form) {
        return '';
    }
?>

<form action="<?= $url ?>" method="post" data-require-validation="<?=$requireValidation ? '1' : ''?>" data-payment-method-id="<?= $helper->escape($paymentMethodId) ?>" data-order-access-token="<?= $helper->escape($order->getAccessToken()) ?>" data-order-action="<?= $helper->escape($orderAction) ?>">
    <? foreach ($form as $key => $value): ?>
        <input name="<?= $key ?>" value="<?= $value ?>" type="hidden" />
    <? endforeach ?>

    <button id="pay-button" type="submit" class="orderPayment_btn btn3">Оплатить</button>
</form>

<? }; return $f;