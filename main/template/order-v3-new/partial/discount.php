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
        <div class="order-ctrl">
            <input class="order-ctrl__input <?= $inputSelectorId ?>" value="<?= $couponNumber ?>" >
        </div>

        <div class="cuponPin" style="display: none">
            <label class="cuponLbl">PIN:</label>
            <input class="cuponField cuponPin_it textfieldgrey jsCertificatePinInput" type="text" name="" value="" />
        </div>

        <? foreach ($couponErrors as $err) : ?>
            <? if ($err->code == 404) : ?>
                <div class="cuponErr">Скидки с таким кодом не существует</div>
            <? elseif ($err->code == 1001) : ?>
                <div class="cuponErr">Купон неприменим к данному заказу</div>
            <? elseif ($err->code == 1022) : ?>
                <div class="cuponErr">Купон уже был использован или истек срок действия</div>
            <? else : ?>
                <div class="cuponErr"><?= $err->message ?></div>
            <? endif ?>
        <? endforeach ?>

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