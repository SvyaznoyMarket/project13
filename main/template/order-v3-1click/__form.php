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

    <h1 class="orderCnt_t">Купить быстро в 1 клик</h1>

    <?//= $helper->render('order-v3/__error', ['error' => $error]) ?>

    <form class="orderU clearfix" action="" method="POST" accept-charset="utf-8">
        <input type="hidden" value="changeUserInfo" name="action" />

        <fieldset class="orderU_flds">
            <div>
                <div class="orderU_fld">
                    <label class="orderU_lbl orderU_lbl-str" for="">Телефон</label>
                    <input class="orderU_tx textfield jsOrderV3PhoneField" type="text" name="user_info[phone]" value="<?= $userEntity ? $userEntity->getMobilePhone() : '' ?>" placeholder="8 (___) ___-__-__" data-mask="8 (xxx) xxx-xx-xx">
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

        <div
            id="js-order-content"
            class="orderCnt jsOrderV3PageDelivery"
            data-url="<?= $helper->url('orderV3OneClick.delivery') ?>"
            data-param="<?= $helper->json([
                'products' => [
                    ['id' => $product->getId(), 'quantity' => 1],
                ],
            ]) ?>
        "></div>

    </form>

<? };