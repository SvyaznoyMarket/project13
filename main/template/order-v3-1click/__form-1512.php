<?php

/**
 * @param \Helper\TemplateHelper $helper
 * @param \Model\Product\Entity $product
 * @param array $sender
 * @param string $sender2
 */
$f = function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product,
    $sender = [], // Поставщик товара: обычно retail rocket
    $sender2 = ''
) {
    $user = \App::user()->getEntity();

    $inputSelectorId = 'id-discountInput-' . md5($product->id . '-' . $product->ui);

    $hasDiscountField = 'new_with_discount' === \App::abTest()->getOneClickView();
?>
<div class="orderOneClick">
    <span class="orderOneClick_t">Оформление заказа</span>

    <?= $helper->render('order-v3/__error', ['error' => null]) ?>

    <div class="orderOneClick_hd">
        <span class="orderOneClick_hd_wr"><img class="orderOneClick_hd_l" src="<?= $product->getMainImageUrl('product_120') ?>" /></span>
        <div class="orderOneClick_hd_r">
            <div class="orderOneClick_hd_n">
                <? if ($product->getPrefix()): ?>
                    <?= $helper->escape($product->getPrefix()) ?><br/>
                <? endif ?>
                <?= $helper->escape($product->getWebName()) ?>
            </div>

            <div class="orderOneClick_hd_pr"><?= $helper->formatPrice($product->getPrice()) ?> <span class="rubl">p</span></div>
        </div>
    </div>

    <form class="orderU orderU-v2 clearfix jsOrderV3OneClickForm" action="<?= $helper->url('orderV3OneClick.create') ?>" method="POST" accept-charset="utf-8">
        <input type="hidden" value="changeUserInfo" name="action" />

        <fieldset class="orderU_flds">
            <div class="">
                <div class="order-ctrl order-ctrl_width required" data-field-container="phone">
                    <input class="order-ctrl__input order-ctrl__input_float-label js-order-ctrl__input jsOrderV3PhoneField" name="user_info[mobile]" value="<?= $user ? $helper->escape(preg_replace('/^8/', '+7', $user->getMobilePhone())) : '' ?>" placeholder="+7 (___) ___-__-__" data-field="phone" data-text-default="*Телефон" value="+71111111111" data-mask="+7 (xxx) xxx-xx-xx" required="required">
                    <span class="errTx errTx_onclick" style="display: none">Неверный формат телефона</span>
                </div>

                <div class="order-ctrl order-ctrl_width required" data-field-container="email">
                    <input class="order-ctrl__input order-ctrl__input_float-label js-order-ctrl__input jsOrderV3EmailField jsOrderV3EmailRequired" name="user_info[email]" value="<?= $user ? $helper->escape($user->getEmail()) : '' ?>" placeholder="*Email" data-field="email" data-text-default="*E-mail" required="required">
                    <span class="errTx errTx_onclick" style="display: none">Неверный формат email</span>
                </div>

                <div class="order-ctrl order-ctrl_width" data-field-container="first_name">
                    <input name="user_info[first_name]" class="order-ctrl__input order-ctrl__input_float-label js-order-ctrl__input jsOrderV3NameField" placeholder="Имя" data-field="first_name" name="user_info[first_name]" value="<?= $user ? $helper->escape($user->getName()) : '' ?>">
                </div>

                <? if ($hasDiscountField): ?>
                <div class="order-discount order-discount_inline">
                    <span class="order-discount__tl">Код скидки/фишки, подарочный сертификат</span>

                    <div class="order-ctrl">
                        <input class="order-ctrl__input id-discountInput-standarttype3 <?= $inputSelectorId ?>" value="">
                        <label class="order-ctrl__lbl nohide"></label>
                    </div>

                    <button
                        class="order-btn order-btn--default jsApplyDiscount-1509"
                        data-relation="<?= $helper->json([
                            'number' => '.' . $inputSelectorId,
                        ]) ?>"
                    >Применить</button>
                
                    <div class="jsCertificatePinField order-discount__pin" style="display: none;">
                        <label class="order-discount__pin-label">Пин код</label>
                        <input class="order-discount__pin-input order-ctrl__input jsCertificatePinInput" type="text" name="" value="">
                    </div>
                </div>
                <? endif ?>
            </div>
        </fieldset>

        <fieldset class="orderU_flds">
            <legend class="orderU_lgnd orderU_lgnd-tggl js-order-oneclick-delivery-toggle-btn">Способ получения<span class="orderU_lgnd_tgglnote js-order-oneclick-delivery-toggle-btn-note">скрыть</span></legend>

            <div class="js-order-oneclick-delivery-toggle" style="display: none;">
                <div id="js-order-content" class="orderOneClick_dlvr orderCnt jsOrderV3PageDelivery"></div>
            </div>
        </fieldset>
        <fieldset class="order-agreement__check jsAcceptAgreementContainer">
            <input type="checkbox" class="customInput customInput-checkbox js-customInput jsAcceptAgreement" id="accept" name="" value="" required="required">

            <label class="customLabel customLabel-checkbox jsAcceptTerms" for="accept">Я ознакомлен и согласен <br>
            <? if ($link = $product->getPartnerOfferLink()): ?>
                <a class="brb-dt order-agreement__check-link" href="<?= $link ?>" target="_blank">с информацией о продавце и его офертой</a>
            <? else: ?>
                с <a class="brb-dt order-agreement__check-link" href="/terms" target="_blank">условиями продажи</a> и <a class="brb-dt order-agreement__check-link" href="/legal" target="_blank">правовой информацией</a>
            <? endif ?>
            </label>
        </fieldset>
        <fieldset class="orderU_fldsbottom">
            <input type="hidden" name="sender" value="<?= $helper->json($sender) ?>" />
            <input type="hidden" name="sender2" value="<?= $helper->escape($sender2) ?>" />

            <button type="submit" class="orderCompl_btn btnsubmit">Оформить</button>
        </fieldset>
    </form>
</div>
<? }; return $f;
