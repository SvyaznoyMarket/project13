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

            <div class="orderOneClick_hd_pr"><strong><?= $helper->formatPrice($product->getPrice()) ?></strong> <span class="rubl">p</span></div>
        </div>
    </div>

    <form class="orderU orderU-v2 clearfix jsOrderV3OneClickForm" action="<?= $helper->url('orderV3OneClick.create') ?>" method="POST" accept-charset="utf-8">
        <input type="hidden" value="changeUserInfo" name="action" />

        <fieldset class="orderU_flds">

            <div>
                <div class="orderU_fld">
                    <input class="orderU_tx textfield jsOrderV3PhoneField" type="text" name="user_info[mobile]" value="<?= $user ? $helper->escape(preg_replace('/^8/', '+7', $user->getMobilePhone())) : '' ?>" placeholder="+7 (___) ___-__-__" data-mask="+7 (xxx) xxx-xx-xx">
                    <label class="orderU_lbl orderU_lbl-str" for="">Телефон</label>
                    <span class="errTx" style="display: none">Неверный формат телефона</span>
                </div>

                <div class="orderU_fld">
                    <input class="orderU_tx textfield jsOrderV3EmailField jsOrderV3EmailRequired" type="text" name="user_info[email]" value="<?= $user ? $helper->escape($user->getEmail()) : '' ?>" placeholder="mail@domain.com">
                    <label class="orderU_lbl orderU_lbl-str" for="">E-mail</label>
                    <span class="errTx" style="display: none">Неверный формат email</span>
                </div>

                <div class="orderU_fld">
                    <label class="orderU_lbl" for="">Имя</label>
                    <input class="orderU_tx textfield jsOrderV3NameField" type="text" name="user_info[first_name]" value="<?= $user ? $helper->escape($user->getName()) : '' ?>" placeholder="">
                </div>
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
                <a class="brb-dt order-agreement__check-link " href="/termsgit " target="_blank">
                    с информацией о продавце и его офертой
                </a>
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
