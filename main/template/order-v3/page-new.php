<?php

return function(
    \Helper\TemplateHelper $helper,
    \Session\User $user,
    array $bonusCards,
    $error = null
) {
    /** @var $bonusCards \Model\Order\BonusCard\Entity[] */
    $userEntity = $user->getEntity();
?>

<?= $helper->render('order-v3/__head', ['step' => 1]) ?>

    <section class="orderCnt jsOrderV3PageNew">
        <h1 class="orderCnt_t">Оформление заказа</h1>

        <?= $helper->render('order-v3/__error', ['error' => $error]) ?>

        <form class="orderU" action="" method="POST" accept-charset="utf-8">
            <input type="hidden" value="changeUserInfo" name="action" />
            <fieldset class="orderU_flds">
                <div class="orderU_fld">
                    <label class="orderU_lbl orderU_lbl-str" for="">Телефон</label>
                    <input class="orderU_tx textfield jsOrderV3PhoneField" type="text" name="user_info[phone]" value="<?= $userEntity ? $userEntity->getMobilePhone() : '' ?>" placeholder="8 xxx xxx xx xx" data-mask="8 xxx xxx xx xx">
                    <span class="orderU_hint">Для смс о состоянии заказа</span>
                </div>

                <div class="orderU_fld">
                    <label class="orderU_lbl" for="">E-mail</label>
                    <input class="orderU_tx textfield" type="text" name="user_info[email]" value="<?= $userEntity ? $userEntity->getEmail() : '' ?>" placeholder="">
                </div>

                <div class="orderU_fld">
                    <label class="orderU_lbl" for="">Имя</label>
                    <input class="orderU_tx textfield" type="text" name="user_info[first_name]" value="<?= $userEntity ? $userEntity->getFirstName() : '' ?>" placeholder="">
                    <span class="orderU_hint">Как к вам обращаться?</span>
                </div>
            </fieldset>

            <? if ($bonusCards) : ?>

            <fieldset class="orderU_flds">
                <div class="bonusCnt">
                    <div class="bonusCnt_t">Начислить баллы</div>

                        <div class="bonusCnt_lst">
                            <? foreach ($bonusCards as $key => $card) : ?>

                                <div class="bonusCnt_i" data-eq="<?= $key ?>">
<!--                                    <img class="bonusCnt_img" src="/styles/order/img/sBank.png" alt="" />-->
                                    <img class="bonusCnt_img" src="/styles/order/img/sClub.png" alt="" />
                                    <span class="bonusCnt_tx"><?= $card->getName() ?></span>
                                </div>
                            <? endforeach ?>
                        </div>

                    <? foreach ($bonusCards as $card) : ?>
                        <div class="bonusCnt_it clearfix" style="display: none">
                            <div class="fl-l">
                                <div class="orderU_fld">
                                    <label class="orderU_lbl" for="">Карта</label>
                                    <input class="orderU_tx textfield" type="text" name="user_info[bonus_card_number]" value="" placeholder="<?= $card->getMask() ?>" data-mask="<?= $card->getMask() ?>">
                                </div>

                                <div class="bonusCnt_descr"><?= $card->getDescription() ?></div>
                            </div>

                            <img class="fl-r" src="<?= $card->getImage() ?>" alt="" />
                        </div>
                    <? endforeach ; ?>
                </div>
            </fieldset>

            <? endif ?>


            <? if (!$userEntity) : ?>

                <div class="orderAuth">
                    <div class="orderAuth_t">Уже заказывали у нас?</div>
                    <a class="orderAuth_btn btnLightGrey bAuthLink" href="<?= \App::router()->generate('user.login') ?>">Войти с паролем</a>
                </div>

            <? endif ?>

            <div class="orderCompl clearfix">
                <button class="orderCompl_btn btnsubmit" type="submit">Далее ➜</button>
            </div>

        </form>

    </section>

<? };

