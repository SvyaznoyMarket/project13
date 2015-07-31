<div class="content mContentOrder clearfix">

<!-- шапка оформления заказа -->
<header class="orderHd orderHd-v2">
    <div class="orderHd_l">
        <img class="orderHd_lg" src="/styles/order/img/logo.png">
        <div class="orderHd_t">Оформление заказа</div>
    </div>

    <!-- если шаг пройден то orderHd_stps_i-pass, текущий шаг orderHd_stps_i-act -->
    <ul class="orderHd_stps">
            <li class="orderHd_stps_i orderHd_stps_i-act">
            Получатель        </li>
            <li class="orderHd_stps_i">
            Самовывоз и доставка        </li>
            <li class="orderHd_stps_i">
            Способы оплаты        </li>
        </ul>
</header>
<!--/ шапка оформления заказа -->


    <section class="orderCnt jsOrderV3PageNew">
        <h1 class="orderCnt_t">Получатель</h1>


    <div id="OrderV3ErrorBlock" class="errtx" style="display: none">
            </div>


        <form class="orderU orderU-v2 clearfix" action="" method="POST" accept-charset="utf-8">
            <input type="hidden" value="changeUserInfo" name="action">

            <fieldset class="orderU_flds">
                <div>
                    <div class="orderU_fld">
                        <input class="orderU_tx textfield jsOrderV3PhoneField textfield-err" type="text" name="user_info[phone]" value="" placeholder="+7 (___) ___-__-__" data-mask="+7 (xxx) xxx-xx-xx" data-event="true">
                        <label class="orderU_lbl orderU_lbl-str" for="">Телефон</label>
                        <span class="errTx" style="">Неверный формат телефона</span>
                        <span class="orderU_hint">Для смс о состоянии заказа</span>
                    </div>

                    <div class="orderU_fld">
                        <input class="orderU_tx textfield jsOrderV3EmailField jsOrderV3EmailRequired textfield-err" type="text" name="user_info[email]" value="" placeholder="mail@domain.com">
                        <label class="orderU_lbl orderU_lbl-str" for="">E-mail</label>
                        <span class="errTx" style="">Не указан email</span>
                                                                                <span class="orderU_hint">
                                                                <input class="customInput customInput-defcheck jsCustomRadio js-customInput jsOrderV3SubscribeCheckbox" type="checkbox" name="subscribe" value="" id="orderV3Subscribe" checked="">
                                <label class="customLabel customLabel-defcheck mChecked jsOrderV3SubscribeLabel" for="orderV3Subscribe">Подписаться на рассылку и получить купон со скидкой 300 рублей на следующую покупку</label>
                            </span>
                                                                        </div>

                    <div class="orderU_fld">
                        <label class="orderU_lbl" for="">Имя</label>
                        <input class="orderU_tx textfield jsOrderV3NameField" type="text" name="user_info[first_name]" value="" placeholder="">
                        <span class="orderU_hint">Как к вам обращаться?</span>
                    </div>
                </div>

                <div>
                    <div class="bonusCnt bonusCnt-v2">


                                                    <!-- Карта Много.ру -->
                            <div class="bonusCnt_i" data-eq="0">
                                <img class="bonusCnt_img" src="/styles/order/img/mnogoru-mini.png" alt="mnogo.ru">
                                <span class="bonusCnt_tx">
                                    <span id="bonusCardLink-2da0a160e0cdab12035ad0ba2722c8e0" class="brb-dt">Карта Много.ру</span> <!-- что бы убрать бордер можно удалить класс brb-dt -->
                                    <span id="bonusCardCode-2da0a160e0cdab12035ad0ba2722c8e0" class="bonusCnt_tx_code"><span class="brb-dt jsMnogoRuSpan"></span></span>
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
                                    <div class="bonusCnt_descr">Получайте бонусы Много.ру за покупки в Enter (1 бонус за 33 руб.).<br>
                                        Для этого введите восьмизначный номер, указанный на лицевой стороне карты и в письмах от Клуба Много.ру.</div>
                                    <img src="/css/skin/img/mnogo_ru.png" alt="mnogo.ru">
                                </div>
                            </div>
                            <!-- Карта Много.ру -->

                    </div>
                </div>
            </fieldset>


                <div class="orderAuth">
                    <div class="orderAuth_t">Уже заказывали у нас?</div>
                    <a class="orderAuth_btn btnLightGrey bAuthLink jsOrderV3AuthLink" href="/login">Войти с паролем</a>
                </div>


            <div class="orderCompl orderCompl-v2 clearfix">
                <button class="orderCompl_btn btnsubmit" type="submit">Далее</button>
            </div>

        </form>

    </section>

    </div>