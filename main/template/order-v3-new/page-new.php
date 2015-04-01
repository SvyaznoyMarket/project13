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

    $isEmailRequired = \App::config()->order['emailRequired'];
    $config = \App::config();
?>

<?= $helper->render('order-v3-new/__head', ['step' => 1]) ?>

    <section class="orderCnt jsOrderV3PageNew">
        <h1 class="orderCnt_t">Получатель</h1>

        <?= $helper->render('order-v3-new/__error', ['error' => $error]) ?>

        <form class="orderU orderU-v2 clearfix" action="" method="POST" accept-charset="utf-8">
            <input type="hidden" value="changeUserInfo" name="action" />

            <fieldset class="orderU_flds">
                <div>
                    <div class="orderU_fld">
                        <input class="orderU_tx textfield jsOrderV3PhoneField" type="text" name="user_info[phone]" value="<?= $userEntity ? preg_replace('/^8/', '+7', $userEntity->getMobilePhone()) : '' ?>" placeholder="+7 (___) ___-__-__" data-mask="+7 (xxx) xxx-xx-xx">
                        <label class="orderU_lbl orderU_lbl-str" for="">Телефон</label>
                        <span class="errTx" style="display: none">Неверный формат телефона</span>
                        <span class="orderU_hint">Для смс о состоянии заказа</span>
                    </div>

                    <div class="orderU_fld">
                        <input class="orderU_tx textfield jsOrderV3EmailField <?= $isEmailRequired ? 'jsOrderV3EmailRequired' : '' ?>" type="text" name="user_info[email]" value="<?= $userEntity ? $userEntity->getEmail() : '' ?>" placeholder="mail@domain.com">
                        <label class="orderU_lbl <?= $isEmailRequired ? 'orderU_lbl-str' : '' ?>" for="">E-mail</label>
                        <span class="errTx" style="display: none">Неверный формат email</span>
                        <? if (!$user->isSubscribed()) : ?>
                            <? if ($userEntity && $userEntity->isEnterprizeMember()) : ?>
                            <? else : ?>
                            <span class="orderU_hint">
                                <input type="checkbox" name="" value="" id="subscribe" class="customInput customInput-checkbox customInput-defcheck js-customInput jsOrderV3SubscribeCheckbox" <?= \App::abTest()->getTest('order_email') ? 'checked' : '' ?>>
                                <label for="subscribe" class="customLabel customLabel-checkbox customLabel-defcheck jsOrderV3SubscribeLabel mChecked">Подписаться на рассылку и получить купон со скидкой 300 рублей на следующую покупку</label>
                            </span>
                            <? endif ?>
                        <? endif ?>
                    </div>

                    <div class="orderU_fld">
                        <label class="orderU_lbl" for="">Имя</label>
                        <input class="orderU_tx textfield jsOrderV3NameField" type="text" name="user_info[first_name]" value="<?= $userEntity ? $userEntity->getFirstName() : '' ?>" placeholder="">
                        <span class="orderU_hint">Как к вам обращаться?</span>
                    </div>
                </div>

                <? if ($bonusCards) : ?>
                <div>
                    <div class="bonusCnt bonusCnt-v2">
                            <div class="bonusCnt_lst">
                                <? foreach ($bonusCards as $key => $card) : ?>

                                    <div class="bonusCnt_i" data-eq="<?= $key ?>">
                                        <img class="bonusCnt_img" src="/styles/order/img/sClub.jpg" alt="" />
                                        <span class="bonusCnt_tx">
                                            <span id="bonusCardLink-<?= md5(json_encode([$card->getName()])) ?>" class="brb-dt">Карта <?= $card->getName() ?></span><!-- что бы убрать бордер можно удалить класс brb-dt -->
                                            <span id="bonusCardCode-<?= md5(json_encode([$card->getName()])) ?>" class="bonusCnt_tx_code"><span class="brb-dt"></span></span>
                                        </span>
                                    </div>

                                <? endforeach ?>



                            </div>

                        <? foreach ($bonusCards as $card) : ?>
                        <? if ($userBonusCards) $userBonusCard = array_filter($userBonusCards, function($arr) use (&$card) {
                                /** @var $card \Model\Order\BonusCard\Entity */
                                return $card->getId() == $arr['bonus_card_id']; })
                            ?>
                            <div class="bonusCnt_it clearfix" style="display: <?= (bool)$userBonusCard ? 'none' : 'none' ?>">
                                <div class="orderU_fld">
                                    <input class="orderU_tx textfield jsOrderV3BonusCardField" type="text" name="user_info[bonus_card_number]" value="<?= (bool)$userBonusCard ? $userBonusCard[0]['number'] : '' ?>" placeholder="<?= $card->getMask() ?>" data-mask="<?= $card->getMask() ?>">
                                    <label class="orderU_lbl" for="">Номер</label>
                                    <span class="errTx" style="display: none">Неверный код карты лояльности</span>
                                    <span class="orderU_inf jsShowBonusCardHint"></span>
                                </div>

                                <div class="bonusCnt_popup" style="display: none">
                                    <div class="bonusCnt_descr"><?= $card->getDescription() ?></div>
                                    <img src="<?= $card->getImage() ?>" alt="" />
                                </div>
                            </div>
                        <? endforeach ; ?>

                        <? if ($config->partners['MnogoRu']['enabled']) : ?>
                            <!-- Карта Много.ру -->
                            <div class="bonusCnt_i" data-eq="<?= count($bonusCards) ?>">
                                <img class="bonusCnt_img" src="/styles/order/img/mnogoru-mini.png" alt="mnogo.ru" />
                                <span class="bonusCnt_tx">
                                    <span id="bonusCardLink-<?= md5(json_encode(['mnogoru'])) ?>" class="brb-dt">Карта Много.ру</span> <!-- что бы убрать бордер можно удалить класс brb-dt -->
                                    <span id="bonusCardCode-<?= md5(json_encode(['mnogoru'])) ?>" class="bonusCnt_tx_code"><span class="brb-dt jsMnogoRuSpan"></span></span>
                                </span>
                            </div>

                            <div class="bonusCnt_it clearfix" style="display: none">
                                <div class="orderU_fld">
                                    <input class="orderU_tx textfield jsOrderV3MnogoRuCardField" type="text" name="user_info[mnogo_ru_number]" value="" placeholder="xxxx xxxx" data-mask="xxxx xxxx">
                                    <label class="orderU_lbl" for="">Номер</label>
                                    <span class="errTx" style="display: none">Неверный код карты Много.ру</span>
                                    <span class="orderU_inf jsShowBonusCardHint"></span>
                                </div>

                                <div class="bonusCnt_popup bonusCnt_popup--mnogoru" style="display: none">
                                    <div class="bonusCnt_descr">Получайте бонусы Много.ру за покупки в Enter (1 бонус за 33 руб.).<br/>
                                        Для этого введите восьмизначный номер, указанный на лицевой стороне карты и в письмах от Клуба Много.ру.</div>
                                    <img src="/css/skin/img/mnogo_ru.png" alt="mnogo.ru" />
                                </div>
                            </div>
                            <!-- Карта Много.ру -->
                        <? endif ?>

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

            <div class="orderCompl orderCompl-v2 clearfix">
                <button class="orderCompl_btn btnsubmit" type="submit">Далее</button>
            </div>

        </form>

    </section>

<? };

