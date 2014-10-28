<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product,
    $title = null
) {
    /** @var $bonusCards \Model\Order\BonusCard\Entity[] */
    $userEntity = \App::user()->getEntity();

    $userBonusCards = $userEntity ? $userEntity->getBonusCard() : null;
    $userBonusCard = null;

    if (null === $title) {
        $title = 'Купить быстро в 1 клик';
    }
?>
<div class="orderOneClick">
    <h1 class="orderOneClick_t"><?= $title ?></h1>

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

    <form class="orderU clearfix jsOrderV3OneClickForm" action="<?= $helper->url('orderV3OneClick.create') ?>" method="POST" accept-charset="utf-8">
        <input type="hidden" value="changeUserInfo" name="action" />

        <fieldset class="orderU_flds">
            <legend class="orderU_lgnd">Покупатель</legend>

            <div>
                <div class="orderU_fld">
                    <label class="orderU_lbl orderU_lbl-str" for="">Телефон</label>
                    <input class="orderU_tx textfield jsOrderV3PhoneField" type="text" name="user_info[mobile]" value="<?= $userEntity ? $userEntity->getMobilePhone() : '' ?>" placeholder="8 (___) ___-__-__" data-mask="8 (xxx) xxx-xx-xx">
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

            <div class="js-order-oneclick-delivery-toggle" style="display: none;">
                <div
                    id="js-order-content"
                    class="orderOneClick_dlvr orderCnt jsOrderV3PageDelivery"
                    data-url="<?= $helper->url('orderV3OneClick.delivery') ?>"
                    data-param="<?= $helper->json([
                        'products' => [
                            ['id' => $product->getId(), 'quantity' => 1],
                        ],
                    ]) ?>
                "></div>
            </div>
        </fieldset>
        
        <fieldset class="orderU_fldsbottom">
            <button type="submit" class="orderCompl_btn btnsubmit">Оформить</button>
        </fieldset>
    </form>
</div>
<? };
