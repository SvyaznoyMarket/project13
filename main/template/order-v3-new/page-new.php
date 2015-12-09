<?php

return function(
    \Helper\TemplateHelper $helper,
    \Session\User $user,
    $hasProductsOnlyFromPartner,
    array $bonusCards,
    $error = null
) {
    /** @var $bonusCards \Model\Order\BonusCard\Entity[] */
    $userEntity = $user->getEntity();

    $userBonusCards = $userEntity ? $userEntity->getBonusCard() : null;
    $userBonusCard = null;

    $oauthEnabled = \App::config()->oauthEnabled;
?>

    <div class="order__wrap">
        <section class="orderCnt jsOrderV3PageNew">
            <h1 class="orderCnt_t">Получатель</h1>

            <?= $helper->render('order-v3-new/__error', ['error' => $error, 'orderDelivery' => null]) ?>

            <form id="js-orderForm" class="js-form" action="<?= $helper->url('orderV3') ?>" method="post">
                <div class="order-receiver">
                    <div class="order-receiver__login">
                        <div class="order-ctrl-wrapper">
                            <div class="order-ctrl required" data-field-container="phone">
                                <label class="order-ctrl__txt js-order-ctrl__txt" data-message="">*Телефон</label>
                                <input name="user_info[phone]" class="order-ctrl__input order-ctrl__input_float-label js-order-ctrl__input js-order-phone" name="user_info[phone]" data-field="phone" data-text-default="*Телефон" value="<?= $userEntity ? preg_replace('/^8/', '+7', $userEntity->getMobilePhone()) : $orderDelivery->user_info->phone ?>" data-mask="+7 (xxx) xxx-xx-xx" <? if (!$userEntity): ?> data-event="true"<? endif ?> required="required" />
                            </div>

                            <div class="order-receiver__hint">Для смс о состоянии заказа</div>
                        </div>

                        <div class="order-ctrl-wrapper">
                            <div class="order-ctrl required" data-field-container="email">
                                <label class="order-ctrl__txt js-order-ctrl__txt" data-message="">*E-mail</span>
                                </label>
                                <input name="user_info[email]" class="order-ctrl__input order-ctrl__input_float-label js-order-ctrl__input js-order-email" data-field="email" data-text-default="*E-mail" value="<?= $userEntity ? $userEntity->getEmail() : $orderDelivery->user_info->email ?>" required="required" />
                            </div>
                            <div class="order-receiver__hint order-receiver__hint_double">Получать эксклюзивные предложения <br>и информацию о заказе</div>
                        </div>

                        <div class="order-ctrl-wrapper">
                            <div class="order-ctrl" data-field-container="first_name">
                                <label class="order-ctrl__txt js-order-ctrl__txt">Имя</label>
                                <input name="user_info[first_name]" class="order-ctrl__input order-ctrl__input_float-label js-order-ctrl__input" data-field="first_name" value="<?= $userEntity ? $userEntity->getFirstName() : $orderDelivery->user_info->first_name ?>" />
                            </div>
                        </div>

                        <!-- Берем из старой верстки - бонусные карты -->
                        <div class="order__bonus-cards bonusCnt bonusCnt-v2">

                            <? if ($bonusCards) : // Бонусные карты от ядра ?>

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

                            <? endif ?>

                            <? if (\App::config()->partners['MnogoRu']['enabled'] && !$hasProductsOnlyFromPartner) : ?>
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
                        <!-- END Берем из старой верстки - бонусные карты -->
                    </div>

                    <? if (!$userEntity) : ?>
                        <div class="order-receiver__social social">
                            <div class="social__head">Войти через</div>
                            <ul class="social__list">
                                <li class="social__item"><a class="js-login-opener" href="<?= $helper->url('user.login') ?>"><img src="/styles/order-new/img/social1.png"></a></li>

                                <? if ($oauthEnabled['facebook']): ?>
                                    <li class="social__item"><a href="<?= $helper->url('user.login.external', ['providerName' => 'facebook' ]) ?>"><img src="/styles/order-new/img/social2.png"></a></li>
                                <? endif ?>

                                <? if ($oauthEnabled['vkontakte']): ?>
                                    <li class="social__item"><a href="<?= $helper->url('user.login.external', ['providerName' => 'vkontakte' ]) ?>"><img src="/styles/order-new/img/social3.png"></a></li>
                                <? endif ?>
                            </ul>

                            <div class="social__register"><a class="js-login-opener" data-state="register" href="<?= $helper->url('user.register') ?>">Регистрация</a></div>

                        </div>
                    <? endif ?>
                </div>

                <div class="order-agreement">
                    <br/>
                    <button class="btn-type btn-type--buy btn-type--order" type="submit" form="js-orderForm">Оформить</button>
                </div>
            </form>
        </section>
    </div>
<? };

