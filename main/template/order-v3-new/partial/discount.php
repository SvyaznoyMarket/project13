<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\OrderDelivery\Entity\Order $order
) {

    // не показываем поля дискаунта, если заказ партнерский (Связной - исключение)
    if ($order->isPartnerOffer() && !$order->seller->isSvyaznoy()) return;

    $couponErrors = array_filter($order->errors, function( \Model\OrderDelivery\Error $error) { return isset($error->details['coupon_number']); });
    $couponNumber = $couponErrors ? $couponErrors[0]->details['coupon_number'] : null;

    $inputSelectorId = 'id-discountInput-' . $order->block_name;
?>
    <div class="order-discount">
        <span class="order-discount__tl">Код скидки/фишки,<br>подарочный сертификат</span>
        <div class="order-ctrl <?= ( $couponErrors ? 'error' : '' ) ?>">
            <input class="order-ctrl__input <?= $inputSelectorId ?>" value="<?= $couponNumber ?>" >
            <label class="order-ctrl__lbl nohide">
                    <? foreach ($couponErrors as $err) : ?>
                        <? if ($err->code == 404) : ?>
                            Скидки с таким кодом<br>не существует
                        <? elseif ($err->code == 1001) : ?>
                            Купон неприменим<br>к данному заказу
                        <? elseif ($err->code == 1022) : ?>
                           Купон уже был использован<br>или истек срок действия
                        <? else : ?>
                            <?= $err->message ?>
                        <? endif ?>
                    <? endforeach ?>
            </label>
        </div>
        <div class="order-discount__pin" style="display: none">
            <div class="order-ctrl">
                <label class="order-ctrl__lbl js-order-ctrl__lbl">PIN:</label>
                <input class="order-ctrl__input order-ctrl__input_float-label js-order-ctrl__input jsCertificatePinInput" type="text" name="" value="" placeholder="PIN">
            </div>
        </div>

        <button
            class="order-btn order-btn--default jsApplyDiscount-1509"
            data-value="<?= $helper->json([
                'block_name' => $order->block_name,
            ]) ?>"
            data-relation="<?= $helper->json([
                'number' => '.' . $inputSelectorId,
            ]) ?>"
        >Применить</button>
    </div>

<? };