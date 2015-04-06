<?php
return function(
    \Helper\TemplateHelper $helper,
    \Model\OrderDelivery\Entity\Order $order
) {

    // не показываем поля дискаунта, если заказ партнерский (Связной - исключение)
    if ($order->isPartnerOffer() && !$order->seller->isSvyaznoy()) return;

    $couponNumber = null;

    if ((bool)array_filter($order->errors, function( \Model\OrderDelivery\Error $error) { return isset($error->details['coupon_number']); })) {
        $couponErrors = array_filter($order->errors, function( \Model\OrderDelivery\Error $error) { return isset($error->details['coupon_number']); });
        $couponNumber = $couponErrors[0]->details['coupon_number'];
    }

    ?>

    <div class="orderCol_f_l">
        <span class="orderCol_f_t brb-dt jsShowDiscountForm" style="display: <?= $couponNumber !== null ? 'none' : 'inline-block' ?>">Ввести код скидки</span>
    </div>

    <div class="orderCol_f_l" style="display: <?= $couponNumber === null ? 'none' : 'block' ?>">
        <div class="orderCol_f_t">Код скидки, подарочный сертификат</div>

        <input class="cuponField textfieldgrey" type="text" name="" value="<?= $couponNumber ?>" />

        <div class="cuponPin" style="display: none">
            <label class="cuponLbl">PIN:</label>
            <input class="cuponField cuponPin_it textfieldgrey jsCertificatePinInput" type="text" name="" value="" />
        </div>

        <? if ($couponNumber !== null) : ?>
            <div class="cuponErr">Скидки с таким кодом не существует</div>
        <? endif; ?>

        <div><button class="cuponBtn btnLightGrey jsApplyDiscount">Применить</button></div>
    </div>

<? } ?>