<?php

/**
 * @param \Helper\TemplateHelper $helper
 * @param \Model\OrderDelivery\Entity $orderDelivery
 * @param $bonusCards \Model\Order\BonusCard\Entity[]
 * @param bool $hasProductsOnlyFromPartner
 * @param null $error
 */
$f = function(
    \Helper\TemplateHelper $helper,
    \Model\OrderDelivery\Entity $orderDelivery,
    array $bonusCards,
    $hasProductsOnlyFromPartner,
    $error = null
) {
    $orderCount = count($orderDelivery->orders);
    $region = \App::user()->getRegion();
    $firstOrder = reset($orderDelivery->orders);

    $isCoordsValid = $region && $region->getLatitude() != null && $region->getLongitude() != null;

    $initialMapCords = [
        'latitude' => $isCoordsValid ? $region->getLatitude() : 55.76,
        'longitude' => $isCoordsValid ? $region->getLongitude() : 37.64,
        'zoom' => $isCoordsValid ? 10 : 4
    ];

    /** @var $bonusCards \Model\Order\BonusCard\Entity[] */
    $userEntity = \App::user()->getEntity();

    $userBonusCards = $userEntity ? $userEntity->getBonusCard() : null;
    $userBonusCard = null;

    /** @var \Model\OrderDelivery\Entity\Order|null $order */
    $order = reset($orderDelivery->orders) ?: null;

    $oauthEnabled = \App::config()->oauthEnabled;
?>
    <div class="order__wrap">
        <section id="js-order-content" class="order-page orderCnt jsOrderV3PageDelivery">
            <div class="pagehead"><h1 class="orderCnt_t">Самовывоз и доставка</h1></div>

            <? if ($orderCount != 1) : ?>
                <div class="order-error order-error--success">Товары будут оформлены как <strong><?= $orderCount ?> <?= $helper->numberChoice($orderCount, ['отдельный заказ', 'отдельных заказа', 'отдельных заказов']) ?></strong><i class="order-error__closer js-order-err-close"></i></div>
            <? endif; ?>

            <?= $helper->render('order-v3-new/partial/error', ['error' => $error, 'orderDelivery' => $orderDelivery]) ?>

            <?= $helper->render('order-v3-new/partial/order-list', ['error' => $error, 'orderDelivery' => $orderDelivery]) ?>

            <div class="pagehead"><h1 class="orderCnt_t">Получатель</h1></div>

            <form id="js-orderForm" class="js-form" action="<?= $helper->url('orderV3.create') ?>" method="post">
                <div class="order-receiver">
                    <div class="order-receiver__login">
                        <div class="order-ctrl-wrapper">
                            <div class="order-ctrl required" data-field-container="phone">
                                <label class="order-ctrl__txt js-order-ctrl__txt" data-message="">*Телефон</label>
                                <input name="user_info[phone]" class="order-ctrl__input js-order-ctrl__input js-order-phone" name="user_info[phone]" data-field="phone" data-text-default="*Телефон" value="<?= $userEntity ? preg_replace('/^8/', '+7', $userEntity->getMobilePhone()) : $orderDelivery->user_info->phone ?>" data-mask="+7 (xxx) xxx-xx-xx" <? if (!$userEntity): ?> data-event="true"<? endif ?> required="required" />
                            </div>

                            <div class="order-receiver__hint">Для смс о состоянии заказа</div>
                        </div>
                        
                        <div class="order-ctrl-wrapper">
                            <div class="order-ctrl required" data-field-container="email">
                                <label class="order-ctrl__txt js-order-ctrl__txt" data-message="">*E-mail</span>
                                </label>
                                <input name="user_info[email]" class="order-ctrl__input js-order-ctrl__input js-order-email" data-field="email" data-text-default="*E-mail" value="<?= $userEntity ? $userEntity->getEmail() : $orderDelivery->user_info->email ?>" required="required" />
                            </div>
                            <? if (!\App::user()->isSubscribed(1)): ?>
                            <div class="order-receiver__subscribe">
                                <input type="checkbox" class="customInput customInput-checkbox" id="sale" name="user_info[subscribe]" value="">
                                <label class="customLabel customLabel-checkbox" for="sale">
                                    <img class="order-receiver__chip" src="/styles/order-new/img/chip-s.png" alt="">
                                    <span class="order-receiver__subscribe-txt">Подпишись на рассылку и получи скидку<br/>на следующую покупку</span>
                                </label>
                            </div>
                            <? endif ?>
                        </div>

                        <div class="order-ctrl-wrapper">
                            <div class="order-ctrl" data-field-container="first_name">
                                <label class="order-ctrl__txt js-order-ctrl__txt">Имя</label>
                                <input name="user_info[first_name]" class="order-ctrl__input js-order-ctrl__input" data-field="first_name" value="<?= $userEntity ? $userEntity->getFirstName() : $orderDelivery->user_info->first_name ?>" />
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

                <div class="order-wishes">
                    <span class="order-wishes__lk jsOrderV3Comment">Дополнительные пожелания</span>

                    <textarea name="order[comment]" class="orderComment_fld order-wishes__field" style="display: <?= $firstOrder->comment == '' ? 'none': 'block' ?>"><?= $firstOrder->comment ?></textarea>
                </div>
                <div class="order-total">
                    <span class="order-total__txt">Итого <?= $orderCount ?> <?= $helper->numberChoice($orderCount, ['заказ', 'заказа', 'заказов']) ?> на общую сумму</span> <span class="order-total__sum"><?= $helper->formatPrice($orderDelivery->total_cost) ?> <span class="rubl">p</span>
                </div>


                <div class="order-agreement">
                    <?= \App::templating()->render('order-v3/common/_blackfriday', ['version' => 2]) ?>
                    <div class="order-agreement__check" data-field-container="accept">
                        <input type="checkbox" class="customInput customInput-checkbox js-customInput jsAcceptAgreement" id="accept" name="" data-field="accept" value="" required="required" />

                        <label  class="customLabel customLabel-checkbox jsAcceptTerms" for="accept">
                            Я ознакомлен и согласен<br><span class="<? if ($orderCount == 1) { ?>order-agreement__oferta<? } ?> js-order-oferta-popup-btn" data-value="<?= $order->seller->offer ?>" >с информацией о продавце и его офертой</span>
                        </label>
                    </div>
                    <br/>
                    <button class="btn-type btn-type--buy btn-type--order" type="submit" form="js-orderForm">Оформить</button>
                </div>
            </form>

            <? if (\App::abTest()->isOrderMinSumRestriction() && \App::config()->minOrderSum > $orderDelivery->getProductsSum()) : ?>
                <div class="popup popup-simple deliv-free-popup jsMinOrderSumPopup" style="display: none;">
                    <a class="to-cart-lnk" href="/">
                    <div class="popup_inn">
                        <span class="info">До оформления заказа осталось</span>
                        <span class="remain-sum"><?= \App::config()->minOrderSum - $orderDelivery->getProductsSum() ?>&thinsp;<span class="rubl">p</span></span>
                        <span class="to-cart-lnk">Продолжить покупки</span>
                    </div>
                    </a>
                </div>
            <? endif ?>

        </section>

        <div id="yandex-map-container" class="selShop_r" style="display: none;" data-options="<?= $helper->json($initialMapCords)?>"></div>
        <div id="kladr-config" data-value="<?= $helper->json(\App::config()->kladr ); ?>"></div>
        <div id="region-name" data-value=<?= json_encode($region->getName(), JSON_UNESCAPED_UNICODE); ?>></div>
        <?= App::config()->debug ? $helper->jsonInScriptTag($orderDelivery, 'initialOrderModel') : '' ?>
        <div id="jsUserAddress" data-value="<?= $helper->json($orderDelivery->user_info->address) ?>"></div>

        <div class="popup popup-simple js-order-oferta-popup">
            <a href="" class="close"></a>

            <div class="popup_inn">
                <div class="orderOferta">
                    <div class="orderOferta_tabs">
                        <div class="orderOferta_tabs_i orderOferta_tabs_i-cur js-oferta-tab" data-tab="tab-1">Условия продажи</div>
                        <div class="orderOferta_tabs_i js-oferta-tab" data-tab="tab-2">Правовая информация</div>
                    </div>

                    <div class="orderOferta_tabcnt orderOferta_tabcnt-cur js-tab-oferta-content" id="tab-1">
                        <div class="orderOferta_tl">Условия продажи</div>
                    </div>

                    <div class="orderOferta_tabcnt js-tab-oferta-content" id="tab-2">
                        <div class="orderOferta_tl">Правовая информация для пользователей сайта ООО «Энтер»</div>

                        <p>ООО «Энтер» оставляет за собой право изменять публикуемые на данном сайте материалы в любое время без предварительного уведомления.</p>

                        <div class="orderOferta_tl">Авторские права</div>
                        <p>Информация сайта ООО «Энтер» защищена авторским правом и действующим законодательством о защите интеллектуальной собственности. ООО «Энтер» предоставляет право посетителям сайта использовать опубликованные материалы в любых личных и некоммерческих целях. Любое копирование, изменение или использование данных материалов в коммерческих целях допускается с письменного согласия ООО «Энтер».</p>

                        <div class="orderOferta_tl">Содержание материалов</div>
                        <p>ООО «Энтер» приняты все разумные меры к тому, чтобы обеспечить точность и актуальность размещенной на этом сайте информации. ООО «Энтер» оставляет за собой право вносить изменение в содержание материалов этого сайта в любое время по собственному усмотрению. Продукты или услуги, не относящиеся напрямую к ООО «Энтер», упомянуты исключительно в информационных целях. Вся информация об ООО «Энтер» и третьих сторонах на этом сайте представлена в том виде, в котором она существует в распоряжении ООО «Энтер».</p>

                        <div class="orderOferta_tl">Товарные знаки</div>
                        <p>Товарные знаки, торговые марки, а также другие средства индивидуализации, помещенные на данном сайте, являются собственностью ООО «Энтер» и третьих лиц. Информация, опубликованная на сайте, не предоставляет никаких лицензионных прав на использование любых товарных знаков без получения предварительного письменного согласия владельца.</p>

                        <div class="orderOferta_tl">Ссылки</div>
                        <p>На данном сайте Вы можете найти ссылки на другие (внешние) сайты, не принадлежащие ООО «Энтер». Информация с этих сайтов не является продолжением или дополнением материалов ООО «Энтер».</p>

                        <div class="orderOferta_tl">Реквизиты</div>
                        <p>Наименование: ООО «Энтер» Место нахождения: 115419, г. Москва, улица Орджоникидзе, дом 11, строение 10. Государственный регистрационный номер записи о создании юридического лица: 1117746043381 ИНН: 7710881860 КПП: 772501001 Наименование банка: Связной Банк ЗАО г. Москва БИК: 044583139 Расчетный счет: 40702810400000002751</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
<? }; return $f;
