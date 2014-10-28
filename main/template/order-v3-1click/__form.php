<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\BasicEntity $product
) {
    /** @var $bonusCards \Model\Order\BonusCard\Entity[] */
    $userEntity = \App::user()->getEntity();

    $userBonusCards = $userEntity ? $userEntity->getBonusCard() : null;
    $userBonusCard = null;
?>
<div class="orderOneClick">
    <h1 class="orderOneClick_t">Купить быстро в 1 клик</h1>

    <?= $helper->render('order-v3/__error', ['error' => null]) ?>

    <div class="orderOneClick_hd">
        <img class="orderOneClick_hd_l" src="<?= $product->getImageUrl(1) ?>" />
        <div class="orderOneClick_hd_r">
            <div class="orderOneClick_hd_n">Отбойный молоток<br/>
            Калибр Мастер ОМ-1700/30М</div>

            <div class="orderOneClick_hd_pr"><strong>10 520</strong> <span class="rubl">p</span></div>
        </div>
    </div>

    <form class="orderU clearfix jsOrderV3OneClickForm" action="<?= $helper->url('orderV3OneClick.create') ?>" method="POST" accept-charset="utf-8">
        <input type="hidden" value="changeUserInfo" name="action" />

        <fieldset class="orderU_flds">
            <legend class="orderU_lgnd">Покупатель</legend>

            <div>
                <div class="orderU_fld">
                    <label class="orderU_lbl orderU_lbl-str" for="">Телефон</label>
                    <input class="orderU_tx textfield jsOrderV3PhoneField" type="text" name="user_info[phone]" value="<?= $userEntity ? $userEntity->getMobilePhone() : '' ?>" placeholder="+7 (___) ___-__-__" data-mask="8 (xxx) xxx-xx-xx">
                </div>

                <div class="orderU_fld">
                    <label class="orderU_lbl" for="">E-mail</label>
                    <input class="orderU_tx textfield jsOrderV3EmailField" type="text" name="user_info[email]" value="<?= $userEntity ? $userEntity->getEmail() : '' ?>" placeholder="mail@domain.com">
                </div>

                <div class="orderU_fld">
                    <label class="orderU_lbl" for="">Имя</label>
                    <input class="orderU_tx textfield jsOrderV3NameField" type="text" name="user_info[first_name]" value="<?= $userEntity ? $userEntity->getFirstName() : '' ?>" placeholder="">
                </div>
            </div>
        </fieldset>
        
        <fieldset class="orderU_flds">
            <legend class="orderU_lgnd orderU_lgnd-tggl js-order-oneclick-delivery-toggle-btn">Способ получения</legend>

            <div
                id="js-order-content"
                class="orderOneClick_dlvr orderCnt jsOrderV3PageDelivery js-order-oneclick-delivery-toggle"
                data-url="<?= $helper->url('orderV3OneClick.delivery') ?>"
                data-param="<?= $helper->json([
                    'products' => [
                        ['id' => $product->getId(), 'quantity' => 1],
                    ],
                ]) ?>
            " style="display: none;"></div>
        </fieldset>
        
        <fieldset class="orderU_fldsbottom">
            <button type="submit" class="orderCompl_btn btnsubmit">Оформить</button>
        </fieldset>
    </form>
</div>
<? };
