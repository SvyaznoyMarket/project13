<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product,
    $sender = [], // Поставщик товара: обычно retail rocket
    $sender2 = ''
) {
    $title = 'Купить быстро в 1 клик';
    $user = \App::user()->getEntity();
?>
<div class="orderOneClick">
    <span class="orderOneClick_t jsOneClickTitle"><?= $title ?></span>

    <?= $helper->render('order-v3/__error', ['error' => null]) ?>

    <div class="orderOneClick_hd">
        <img class="orderOneClick_hd_l" src="<?= $product->getImageUrl(1) ?>" />
        <div class="orderOneClick_hd_r">
            <div class="orderOneClick_hd_n">
                <? if ($product->getPrefix()): ?>
                    <?= $product->getPrefix() ?><br/>
                <? endif ?>
                <?= $product->getWebName() ?>
            </div>

            <div class="orderOneClick_hd_pr"><strong><?= $helper->formatPrice($product->getPrice()) ?></strong> <span class="rubl">p</span></div>
        </div>
    </div>

    <form class="orderU orderU-v2 clearfix jsOrderV3OneClickForm" action="<?= $helper->url('orderV3OneClick.create') ?>" method="POST" accept-charset="utf-8">
        <input type="hidden" value="changeUserInfo" name="action" />

        <fieldset class="orderU_flds">
            <legend class="orderU_lgnd">Покупатель</legend>

            <div>
                <div class="orderU_fld">
                    <input class="orderU_tx textfield jsOrderV3PhoneField" type="text" name="user_info[mobile]" value="<?= $user ? $helper->escape($user->getMobilePhone()) : '' ?>" placeholder="8 (___) ___-__-__" data-mask="8 (xxx) xxx-xx-xx">
                    <label class="orderU_lbl orderU_lbl-str" for="">Телефон</label>
                    <span class="errTx" style="display: none">Неверный формат телефона</span>
                </div>

                <div class="orderU_fld">
                    <input class="orderU_tx textfield jsOrderV3EmailField" type="text" name="user_info[email]" value="<?= $user ? $helper->escape($user->getEmail()) : '' ?>" placeholder="mail@domain.com">
                    <label class="orderU_lbl" for="">E-mail</label>
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
                <div
                    id="js-order-content"
                    class="orderOneClick_dlvr orderCnt jsOrderV3PageDelivery"
                    data-url="<?= $helper->url('orderV3OneClick.delivery') ?>"
                    data-param="<?= $helper->json([
                        'products' => [
                            ['id' => $product->getId(), 'quantity' => 1],
                        ],
                        'shopId'   => null, // устанавливается на стороне javascript
                    ]) ?>
                "></div>
            </div>
        </fieldset>
        
        <fieldset class="orderU_fldsbottom">
            <input type="hidden" name="sender" value="<?= $helper->json($sender) ?>" />
            <input type="hidden" name="sender2" value="<?= $helper->escape($sender2) ?>" />

            <button type="submit" class="orderCompl_btn btnsubmit">Оформить</button>
        </fieldset>
    </form>
</div>
<? };
