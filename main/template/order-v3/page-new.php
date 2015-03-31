<?php

return function(
    \Helper\TemplateHelper $helper,
    \Session\User $user,
    array $bonusCards,
    $error = null
) {
    /** @var $bonusCards \Model\Order\BonusCard\Entity[] */
    $userEntity = $user->getEntity();

    $userBonusCards = $userEntity ? $userEntity->getBonusCard() : null;
    $userBonusCard = null;
?>

<?= $helper->render('order-v3/__head', ['step' => 1]) ?>

    <section class="orderCnt jsOrderV3PageNew">
        <h1 class="orderCnt_t">Оформление заказа</h1>

        <?= $helper->render('order-v3/__error', ['error' => $error]) ?>

        <form class="orderU clearfix" action="" method="POST" accept-charset="utf-8">
            <input type="hidden" value="changeUserInfo" name="action" />

            <fieldset class="orderU_flds">
                <div>
                    <div class="orderU_fld">
                        <label class="orderU_lbl orderU_lbl-str" for="">Телефон</label>
                        <input class="orderU_tx textfield jsOrderV3PhoneField" type="text" name="user_info[phone]" value="<?= $userEntity ? preg_replace('/^8/', '+7', $userEntity->getMobilePhone()) : '' ?>" placeholder="+7 (___) ___-__-__" data-mask="+7 (xxx) xxx-xx-xx">
                        <span class="orderU_hint">Для смс о состоянии заказа</span>
                    </div>

                    <div class="orderU_fld">
                        <label class="orderU_lbl" for="">E-mail</label>
                        <input class="orderU_tx textfield jsOrderV3EmailField" type="text" name="user_info[email]" value="<?= $userEntity ? $userEntity->getEmail() : '' ?>" placeholder="mail@domain.com">
                        <span class="orderU_hint">
                            <input type="checkbox" name="" id="subscribe" class="customInput customInput-defcheck">
                            <label for="subscribe" class="customLabel">Подписаться на рассылку и получить купон со скидкой 300 рублей на следующую покупку</label>
                        </span>
                    </div>

                    <div class="orderU_fld">
                        <label class="orderU_lbl orderU_lbl-str" for="">Имя</label>
                        <input class="orderU_tx textfield jsOrderV3NameField" type="text" name="user_info[first_name]" value="<?= $userEntity ? $userEntity->getFirstName() : '' ?>" placeholder="">
                        <span class="orderU_hint">Как к вам обращаться?</span>
                    </div>
                </div>

                <? if ($bonusCards) : ?>
                <div>
                    <div class="bonusCnt">
                        <div class="bonusCnt_t">Начислить баллы</div>

                            <div class="bonusCnt_lst">
                                <? foreach ($bonusCards as $key => $card) : ?>

                                    <div class="bonusCnt_i" data-eq="<?= $key ?>">
                                        <img class="bonusCnt_img" src="/styles/order/img/sClub.png" alt="" />
                                        <span class="bonusCnt_tx"><span class="brb-dt"><?= $card->getName() ?></span></span>
                                    </div>
                                <? endforeach ?>
                            </div>

                        <? foreach ($bonusCards as $card) : ?>
                        <? if ($userBonusCards) $userBonusCard = array_filter($userBonusCards, function($arr) use (&$card) {
                                /** @var $card \Model\Order\BonusCard\Entity */
                                return $card->getId() == $arr['bonus_card_id']; })
                            ?>
                            <div class="bonusCnt_it clearfix" style="display: <?= (bool)$userBonusCard ? 'block' : 'none' ?>">
                                <div class="fl-l">
                                    <div class="orderU_fld">
                                        <label class="orderU_lbl" for="">Карта</label>
                                        <input class="orderU_tx textfield jsOrderV3BonusCardField" type="text" name="user_info[bonus_card_number]" value="<?= (bool)$userBonusCard ? $userBonusCard[0]['number'] : '' ?>" placeholder="<?= $card->getMask() ?>" data-mask="<?= $card->getMask() ?>">
                                    </div>

                                    <div class="bonusCnt_descr"><?= $card->getDescription() ?></div>
                                </div>

                                <img class="fl-r" src="<?= $card->getImage() ?>" alt="" />
                            </div>
                        <? endforeach ; ?>
                    </div>
                </div>
                <? endif ?>
            </fieldset>

            <? if (!$userEntity) : ?>

                <div class="orderAuth">
                    <div class="orderAuth_t">Уже заказывали у нас?</div>
                    <a class="orderAuth_btn btnLightGrey bAuthLink jsOrderV3AuthLink" href="<?= \App::router()->generate('user.login') ?>">Войти с паролем</a>
                </div>

            <? endif ?>

            <div class="orderCompl clearfix">
                <button class="orderCompl_btn btnsubmit" type="submit">Далее ➜</button>
            </div>

        </form>

    </section>

<? };

