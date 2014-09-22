<?php
/**
 * @var $page       \View\Layout
 */
?>

<script type="text/javascript">
    $(function() {
        $('.jsAnswerToggle').click( function() {
            $(this).toggleClass('mShow');
        });

        var w = $(window),
            top = $('.jsNavFixed').offset().top,
            nav = $('.jsNavFixed'),
            navI = $('.jsNavItemScroll');

        w.scroll( function (event) {
            var scrollY = $(this).scrollTop();
            if ( scrollY >= top ) {
              nav.addClass('mFixed');
            } else {
              nav.removeClass('mFixed');
            }
        });

        navI.click( function() {
            navI.removeClass('state_active');
            var target = $(this).attr('href');
            $(this).addClass('state_active');
            $('html, body').animate({scrollTop: $(target).offset().top}, 300);
            return false; 
        }); 
    });
</script>

<div class="landing"> 
    <div class="landing_hd landing_hdn"> 
        <div class="landing_hdn-h">
            <div class="landing_hd_top clearfix"> 
                <span class="landing_hd_top_lk mod_tele2"></span> 

                <div class="landing_hd_top-rside"> 
                    <span class="landing_hd_top_lk mod_mastercard">MasterCard</span> 
                    <a href="/" class="landing_hd_top_lk mod_enter">Enter</a> 
                </div> 
            </div> 

            <div class="landing_hd-land">
                <div class="landing_hd-land-bg"></div>
                <a class="landing_hd-land-gift jsNavItemScroll" href="#card"></a> 
                <div class="landing_hd-land_tx"> 
                    <div class="landing_hd-land_tx-1">Боитесь покупок онлайн? Напрасно. Теперь есть способ платить безопасно.</div>
                    <div class="landing_hd-land_tx-2">Кстати, еще и выгодно — оплатите покупку виртуальной картой Tele2<br/>MasterCard и получите купон на 500 рублей</div>
                    <a href="#terms" class="g-button mod_header">Получить купон на 500 руб.</a>
                </div> 
            </div> 

<!--            <div class="landing_hd_socnet js-tele2Page-child js-widget action_share">
                <div class="landing_hd_socnet_ic_i js-widget"> 
                    <div class="landing_hd_socnet_ic mod_vk"></div> 
                </div> 
                <div class="landing_hd_socnet_ic_i js-widget"> 
                    <div class="landing_hd_socnet_ic mod_fb"></div> 
                </div>
                <div class="landing_hd_socnet_ic_i js-widget"> 
                    <div class="landing_hd_socnet_ic mod_gp"></div> 
                </div> 
                <div class="landing_hd_socnet_ic_i js-widget"> 
                    <div class="landing_hd_socnet_ic mod_twt"></div> 
                </div>
                <div class="landing_hd_socnet_ic_i js-widget"> 
                    <div class="landing_hd_socnet_ic mod_ok"></div> 
                </div> 
            </div> -->
        </div>
    </div>

    <div class="landing_nav landing_hdn jsNavFixed"> 
        <div class="landing_nav_lst">
            <div class="landing_nav_lst-list-h">
                <a href="#terms" class="landing_nav_lst_i jsNavItemScroll">Получить купон на 500 руб.</a>
                <a href="#card" class="landing_nav_lst_i jsNavItemScroll">Выпустить карту Tele2 MasterCard<sup>&copy;</sup></a>
                <a href="#faq" class="landing_nav_lst_i jsNavItemScroll">Вопросы и ответы</a> 
                <a href="#video" class="landing_nav_lst_i jsNavItemScroll">Видеопрезентация</a>
            </div>
        </div> 
    </div> 

    <div class="landing_terms landing_hdn landing_res scheme_lgray" id="terms"> 
        <div class="g-columns mod_terms"> 
            <div class="g-columns-cell">
                <div class="landing_terms-how"> 
                    <div class="landing_ttl landing_ttl-23"><strong>Как получить купон<br />на 500 руб.</strong></div>
                    <ol class="b-ordered-list mod_terms">
                        <li><p class="b-ordered-list_i">Необходимо выпустить <span class="g-pseudo g_lk jsg_lk"><span class="g-pseudo-h">виртуальную карту Tele2 MasterCard<sup>&copy;</sup></span></span>. Процедура занимает не более 2&nbsp;минут.</p></li>
                        <li><p class="b-ordered-list_i">Вы получите код купона на номер мобильного телефона.</p></li>
                        <li><p class="b-ordered-list_i">Сделайте заказ на сайте <a href="/">www.enter.ru</a> на сумму от 1000&nbsp;руб.</p></li>
                        <li><p class="b-ordered-list_i">Введите код купона в разделе «Оформление заказа».</p></li>
                        <li><p class="b-ordered-list_i">Оплатите заказ виртуальной картой Tele2 MasterCard<sup>©</sup>.</p></li>
                    </ol>
                </div>
            </div> 
            <div class="g-columns-cell"> 
                <div class="landing_terms-form"> 
                    <div class="landing_terms-form-cell side_left"> 
                        <div class="landing_terms-form-terms">Уже есть виртуальная карта <strong>Tele2 MasterCard<sup>©</sup></strong>? Тогда укажите номер своего мобильного телефона и&nbsp;получите купон на&nbsp;500&nbsp;руб. прямо сейчас.
                        </div> 
                    </div> 

                    <div class="landing_terms-form-cell side_right">
                        <form action="#" class="landing_form jsMobiDengiForm">
                            <label class="landing_form_lbl">Номер вашего мобильного</label>

                            <div class="landing_form_fld">
                                <input class="landing_form_it jsMobiDengiPhoneInput" type="text" name="phone" value="" placeholder="+7 950 027 74 96" />

                                <div class="b-input-action">   
                                    <div class="b-input-error"></div>   
                                    <div class="b-input-valid"></div>
                                    <div class="b-input-reset"></div>
                                </div>
                            </div>

                            <div class="landing_form_hint">Например, +7 950 027 74 96</div>

                            <button type="submit" class="g-button scheme_dblue size_5 box_block js-form-button js-widget" style="font-size: 18px">Получить купон</button>
                        </form>
                    </div> 
                </div> 
            </div> 
        </div> 
        <p class="g-ui align_center" style="margin-top: 20px;"><a href="/" class="g-button scheme_dblue size_5 view_wide">Начать покупки</a></p>
    </div> 

    <div id="card" class="landing_video landing_hdn landing_res"> 
        <div class="landing_hdn-h">
            <div class="landing_card-iframe js-tele2Page-child js-widget">
                <iframe src="https://mycard.tele2.ru/iframe" width="960" height="330" frameborder="0" scrolling="no" class="js-tele2Page-cardIframe"></iframe>  
            </div>
        </div>
    </div>

    <div class="landing_card landing_hdn landing_res" id="faq"> 
        <div class="landing_hdn-h">
            <p class="landing_ttl"><strong>Вопросы и ответы</strong></p>

            <div class="landing_faq_col landing_faq_col-l"> 
                <div class="landing_faq_i jsAnswerToggle">
                    <div class="landing_faq_q"> 
                        <span>Что такое Виртуальная карта Tele2 MasterCard?</span>
                    </div>

                    <div class="landing_faq_a">
                        <p><strong>Виртуальная карта Tele2 MasterCard</strong> - это электронное средство платежа международной платежной системы MasterCard. Карта выпускается для Абонентов Tele2 физических лиц, использующих авансовую систему расчетов (тарифный план с предоплатной системой расчетов), которым доступны услуги мобильной коммерции.</p><p>Виртуальную карту можно использовать только в сети Интернет, где для осуществления оплаты не требуется ее физического наличия, а нужны только реквизиты карты: номер карты, срок действия карты, CVC2-код, Имя и Фамилия владельца карты.<br>Даже если у Вас нет обычной банковской карты, у Вас всегда есть под рукой <strong>Виртуальная карта Tele2 MasterCard</strong> и значит Вам доступна покупка любых товаров и услуг в Интернет-магазинах, билетов на мероприятия и авиабилетов, оплата услуг связи и многое другое.</p>
                    </div>
                </div> 

                <div class="landing_faq_i jsAnswerToggle"> 
                    <div class="landing_faq_q">
                        <span>Какой банк эмитирует виртуальную карту Tele2 MasterCard?</span>
                    </div>

                    <div class="landing_faq_a">
                        <p>Карту международной платежной системы MasterCard выпускает Банк «Таврический» (ОАО), Санкт-Петербург; тел. (812)329-55-11, факс (812)275-88-43, <a class="" target="_blank" href="http://www.tavrich.ru/">http://www.tavrich.ru/</a>. Генеральная лицензия ЦБ РФ № 2304 от 15.09.2004 г.</p>
                    </div> 
                </div>

                <div class="landing_faq_i jsAnswerToggle">
                    <div class="landing_faq_q"> 
                        <span>Кому доступна карта?
                    </div> 

                    <div class="landing_faq_a">
                        <p><strong>Виртуальная карта Tele2 MasterCard</strong> доступна для абонентов Tele2 физических лиц, использующих авансовую систему расчетов (тарифный план с предоплатной системой расчетов). Услуга не предоставляется абонентам корпоративных тарифных планов и абонентам, у которых с момента активации SIM-карты в сети Tele2 не прошло 60 дней.</p>
                    </div>
                </div>

                <div class="landing_faq_i jsAnswerToggle">
                    <div class="landing_faq_q">
                        <span>Как выпустить Виртуальную карту Tele2 MasterCard?</span>
                    </div>

                    <div class="landing_faq_a">
                        <p>Существует несколько способов заказа карты:</p>
                        <ul class="b-unordered-list mod_mdash">
                            <li>Мобильный портал <strong>*338#</strong> (далее цифра 1- выпуск карты) или прямой запрос <strong>*338*1#</strong>;</li>
                            <li>SMS с текстом Сard, Карта или Karta (регистр неважен) на номер <strong>338</strong>;</li>
                            <li>Через мобильное приложение для Android и iOS;</li> 
                            <li>На Web-caйте услуги <a class="" target="_blank" href="https://mycard.tele2.ru/">mycard.tele2.ru</a>.</li>
                        </ul>
                        <p>В ответ на запрос придет 2 сообщения:</p>
                        <ul class="b-unordered-list mod_mdash">
                            <li>C номером и сроком действия карты;</li>
                            <li>CVC2-кодом.</li>
                        </ul>
                        <p>Номер карты генерируется автоматически на основании абонентского номера и не может быть изменен. Номер карты легко запомнить, он состоит из серии 338, номера телефона (11 цифр, начиная с 7) и замыкает номер карты контрольная цифра.</p>
                    </div>
                </div>

                <div class="landing_faq_i jsAnswerToggle"> 
                    <div class="landing_faq_q">
                        <span>Что такое CVC2 и как его получить?</span>
                    </div>

                    <div class="landing_faq_a"><p>Код CVC2 необходим для обеспечения безопасности платежей. Он заканчивает действие через 60 минут после выпуска или сразу после проведения платежа. Заказать CVC2-код можно в любой момент любым удобным способом:</p>
                        <ul class="b-unordered-list mod_mdash">
                            <li>Мобильный портал <strong>*338#</strong> (далее цифра 2- выпуск карты) или прямой запрос <strong>*338*2#</strong>;</li> 
                            <li>SMS с текстом Код, Code, Cod или Kod на номер <strong>338</strong>;</li>
                            <li>Через мобильное приложение для Android и iOS;</li> 
                            <li>На Web-caйте услуги <a class="" target="_blank" href="https://mycard.tele2.ru/">mycard.tele2.ru</a>.</li>
                        </ul>
                    </div> 
                </div> 

                <div class="landing_faq_i jsAnswerToggle"> 
                    <div class="landing_faq_q">
                        <span>Как тарифицируется услуга?</span>
                    </div>

                    <div class="landing_faq_a">
                        <table class="landing_faq_a-table">
                            <thead>
                                <tr>
                                    <td>Действие</td>
                                    <td>Стоимость</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Отправка сообщений на короткий номер 338 и запросы на *338#</td>
                                    <td>Бесплатно</td>
                                </tr>
                                <tr>
                                    <td>Выпуск карты и подключение услуги</td>
                                    <td>Бесплатно</td></tr><tr><td>Комиссия за платеж по карте</td>
                                    <td>3% от стоимости товара или услуги</td>
                                </tr>
                                <tr>
                                    <td>Обслуживание Виртуальной карты Tele2 MasterCard</td><td>0 руб.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="landing_faq_i jsAnswerToggle">
                    <div class="landing_faq_q">
                        <span>Как пользоваться картой для покупок в Интернете?</span>
                    </div> 

                    <div class="landing_faq_a">
                        <p>Если в интернет-магазине в способах оплаты присутствует логотип MasterCard, можно смело оплачивать покупку <strong>Виртуальной картой Tele2 MasterCard</strong>. Для совершения платежа в сети Интернет нужны следующие реквизиты карты: номер карты, срок действия карты, CVC2-код, Имя и Фамилия владельца карты. Эти данные вводятся в соответствующие поля электронной платежной формы.</p>
                    </div>
                </div>

                <div class="landing_faq_i jsAnswerToggle">
                    <div class="landing_faq_q">
                        <span>Как правильно вводить Имя и Фамилию владельца карты?</span>
                    </div>

                    <div class="landing_faq_a">
                        <p>Для ввода данных в платежной форме используйте свое имя и фамилию латинскими буквами, например как в загранпаспорте или водительском удостоверении.</p>
                    </div> 
                </div>

                <div class="landing_faq_i jsAnswerToggle">
                    <div class="landing_faq_q">
                        <span>Как пополнить Виртуальную карту Tele2 MasterCard?</span>
                    </div>

                    <div class="landing_faq_a">
                        <p>Для оплаты с помощью виртуальной карты Вам доступен остаток средств на лицевом счете мобильного телефона за минусом не снижаемого остатка, равного <strong>10 руб.</strong> (для абонентов Санкт-Петербурга – <strong>20 руб.</strong>). Фактически, баланс телефона равен балансу виртуальной карты. Поэтому нет необходимости отдельно пополнять карту, Вы просто пополняете счет телефона, как обычно.</p>
                        <p>При совершении платежа с помощью виртуальной карты, средства в автоматическом режиме списываются с баланса номера, происходит пополнение виртуальной карты Tele2.</p>
                        <p>Количество покупок может быть ограничено максимальной суммой, которую вы тратите в течение дня: это <strong>15 000 руб.</strong>, в эту сумму в т.ч. входит и комиссия 3%. Это относится и к максимальной единовременной сумме платежа. В течение месяца совокупно сумма покупок с учетом комиссии не может превысить <strong>40 000 руб.</strong></p>
                    </div>
                </div>

                <div class="landing_faq_i jsAnswerToggle">
                    <div class="landing_faq_q"> 
                        <span>Как узнать баланс своей Виртуальной карты Tele2 MasterCard?</span>
                    </div> 

                    <div class="landing_faq_a"><p>Остаток средств для совершения платежей можно узнать на счете телефона, для этого нужно набрать команду <strong>*104#</strong>.
                        </p>
                    </div> 
                </div>

                <div class="landing_faq_i jsAnswerToggle"> 
                    <div class="landing_faq_q">
                        <span>Как обеспечивается безопасность сервиса?</span>
                    </div>

                    <div class="landing_faq_a">
                        <p>В первую очередь предусмотрено использование одноразового кода CVC2, который обеспечивает безопасность платежей. Один CVC2-код действует для одной покупки. Срок действия CVC2–кода 1 час.</p>
                        <p>Кроме этого предусмотрена автоматическая блокировка карты после 3-х не успешных попыток ввода СVС2-кода. Это позволяет обезопасить карту от возможных действий злоумышленников.</p>
                        <p>Срок действия карты 6 месяцев. По истечению этого времени данный параметр карты обновляется автоматически, что также придает дополнительную надежность сервису в случае, если Вы решите сменить номер телефона.</p>
                    </div> 
                </div> 
                <div class="landing_faq_i jsAnswerToggle"> 
                    <div class="landing_faq_q">
                        <span>Как отменить платеж по виртуальной карте Tele2?</span>
                    </div> 

                    <div class="landing_faq_a">
                        <p>Если платеж уже совершен, отменить его невозможно и сумма платежа будет списана с лицевого счета Tele2. Вы можете обратиться с заявлением об опротестовании платежа в магазин, на сайте которого производили оплату.</p>
                    </div>
                </div> 
                <div class="landing_faq_i jsAnswerToggle"> 
                    <div class="landing_faq_q"> 
                        <span>Есть ли ограничения на количество покупок с помощью Виртуальной карты Tele2 MasterCard?</span>
                    </div> 

                    <div class="landing_faq_a">
                        <p>Таких ограничений не предусмотрено. Карту можно многократно использовать для оплаты товаров и услуг в Интернете в пределах остатка денежных средств на балансе мобильного счета, а также в пределах срока действия карты (6 месяцев). Однако существуют законодательные ограничения максимальной суммы списания с баланса мобильного счета – <strong>15 000 руб.</strong> за одну операцию и суммарно операций в течение суток с учетом комиссии. Сумма покупок в течение месяца не должна превышать <strong>40 000 руб.</strong> с учетом комиссии.</p>
                    </div>
                </div>
                <div class="landing_faq_i jsAnswerToggle"> 
                    <div class="landing_faq_q"> 
                        <span>Я выпустил карту и не пользуюсь ей, могу ли я отказаться от виртуальной карты? Как это сделать?</span>
                    </div> 

                    <div class="landing_faq_a">
                        <p>Если вы не пользуетесь картой или хотите ее заблокировать по разным причинам, можно воспользоваться различными способами блокировки карты:</p>
                        <ul class="b-unordered-list mod_mdash">
                            <li>Мобильный портал <strong>*338#</strong> (цифра 3 - заблокировать карту) короткая команда <strong>*338*3#</strong>;</li> 
                            <li>SMS с текстом Заблокировать, Блокировка, Блок, Стоп, Blok, Block (регистр не важен) на номер <strong>338</strong>;</li>
                            <li>Через мобильное приложение для Android и iOS.</li>
                        </ul>
                        <p>Для продолжения пользования картой требуется заказать карту повторно. По истечении 6 месяцев с момента выпуска виртуальная карта будет заблокирована автоматически. Чтобы воспользоваться ей снова, необходимо ее выпустить заново, воспользовавшись одним из доступных способов.</p>
                    </div>
                </div> 
            </div>                    

            <div class="landing_faq_col"> 
                <div class="landing_faq_i jsAnswerToggle"> 
                    <div class="landing_faq_q"> 
                        <span>Я выпускал карту и не помню ее реквизиты. Как их узнать?</span>
                    </div>

                    <div class="landing_faq_a">
                        <p>Для того, чтобы узнать реквизиты карты используйте следующие интерфейсы:</p>
                        <ul class="b-unordered-list mod_mdash">
                            <li>Мобильный портал <strong>*338#</strong> (далее цифра 1- выпуск карты) или прямой запрос <strong>*338*1#</strong>;</li> 
                            <li>SMS с любым текстом на номер 338;</li> <li>Через мобильное приложение для Android и iOS;</li>
                            <li>На Web-caйте услуги <a class="" target="_blank" href="https://mycard.tele2.ru/">mycard.tele2.ru</a>.</li>
                        </ul>
                    </div> 
                </div>

                <div class="landing_faq_i jsAnswerToggle"> 
                    <div class="landing_faq_q"> 
                        <span>Где я могу получить выписку по операциям по карте?</span>
                    </div> 

                    <div class="landing_faq_a"><p>Все операции по Виртуальной карте доступны в детализации абонентского номера Tele2, которую можно заказать любым удобным способом, например, через Личный кабинет <a class="" target="_blank" href="https://my.tele2.ru/">my.tele2.ru</a>.
                        </p>
                        <p>История платежей по карте доступна пользователям мобильных приложений <strong>«Виртуальная карта Tele2 MasterCard»</strong> для смартфонов на платформах Android и iOS.</p>
                    </div> 
                </div> 

                <div class="landing_faq_i jsAnswerToggle"> 
                    <div class="landing_faq_q">
                        <span>Можно ли снять наличные с Виртуальной карты Tele2 MasterCard?</span>
                    </div> 

                    <div class="landing_faq_a">
                        <p>Снятие наличных с виртуальной карты не предусмотрено. Виртуальная карта используется исключительно для осуществления безналичных расчетов в Интернете.</p>
                    </div>
                </div>

                <div class="landing_faq_i jsAnswerToggle"> 
                    <div class="landing_faq_q"> 
                        <span>Могу ли я пополнить виртуальную карту наличными или с банковской карты?</span>
                    </div>

                    <div class="landing_faq_a">
                        <p>Да, для этого нужно пополнить счет Tele2.</p>
                    </div> 
                </div>

                <div class="landing_faq_i jsAnswerToggle"> 
                    <div class="landing_faq_q">
                        <span>Я решил отказаться от услуг Оператора Tele2, у меня есть виртуальная карта Tele2, могу ли я оставить карту?</span>
                    </div> 

                    <div class="landing_faq_a">
                        <p>К сожалению, нет. При расторжении контракта с Оператором, виртуальная карта блокируется.</p>
                    </div>
                </div>

                <div class="landing_faq_i jsAnswerToggle"> 
                    <div class="landing_faq_q">
                        <span>Нужно ли блокировать карту при смене номера телефона?</span>
                    </div> 

                    <div class="landing_faq_a">
                        <p>При смене номера телефона у Оператора Tele2 Ваша старая карта блокируется и будет не доступна, Чтобы продолжить пользоваться виртуальной картой на новом номере, можно выпустить ее заново, при помощи стандартных команд.</p>
                    </div>
                </div>

                <div class="landing_faq_i jsAnswerToggle">
                    <div class="landing_faq_q">
                        <span>Я потерял телефон, у меня есть виртуальная карта. Что мне делать?</span>
                    </div>

                    <div class="landing_faq_a">
                        <p>В этой ситуации вы можете заблокировать SIM- карту*, позвонив по номеру 611 или в офисе обслуживания абонентов Tele2. В этом случае никто не сможет воспользоваться вашим номером, а также любыми подключенными услугами, включая виртуальную карту Tele2.</p>
                        <p>В дальнейшем всегда можно восстановить SIM-карту в любом из офисов Tele2.</p>
                        <p><i>* Для блокировки номера Вам понадобятся паспортные данные.</i></p>
                    </div>
                </div>

                <div class="landing_faq_i jsAnswerToggle"> 
                    <div class="landing_faq_q">
                        <span>Я делаю покупки на иностранных сайтах. Сумма покупки там указывается в иностранной валюте. Какой курс конвертации Банка эмитента карты?</span>
                    </div> 

                    <div class="landing_faq_a">
                        <p><strong>Виртуальную карту Tele2 MasterCard</strong> международной платежной системы MasterCard выпускает ОАО Банк «Таврический», лицензия Банка России № 2304.</p>
                        <p>Счет виртуальной карты в рублях. Банк производит конвертацию суммы в рубли по курсу ЦБ+3,5%.</p>
                        <p>Более подробную консультацию по вопросам конвертации можно получить только в Call-Center Банка «Таврический» 8-800-555-3115.</p>
                    </div>
                </div> 

                <div class="landing_faq_i jsAnswerToggle">
                    <div class="landing_faq_q">
                        <span>При покупке товара в иностранной валюте, в какой момент взимается комиссия за пополнение виртуальной карты Tele2?</span>
                    </div>

                    <div class="landing_faq_a">
                        <p>Все расчеты на территории РФ ведутся в рублях. При оплате покупки, стоимость которой выставлена магазином в иностранной валюте, Банк получает информацию о сконвертированной в рубли сумме (конвертацию произвела международная платежная система), либо сумму платежа Банк самостоятельно конвертирует в рубли по курсу ЦБ+ 3,5%.</p>
                        <p>Комиссия с Клиента удерживается именно от этой, сконвертированной в рубли суммы также в рублях.</p>
                        <p>Более подробную консультацию по вопросам конвертации можно получить только в Call-Center Банка «Таврический» 8-800-555-3115.</p>
                    </div> 
                </div>

                <div class="landing_faq_i jsAnswerToggle"> 
                    <div class="landing_faq_q"> 
                        <span>Я совершил покупку, но хочу отказаться от нее. Как я смогу получить обратно платеж? Будет ли возвращена в т.ч. удержанная комиссия 3%? Сколько времени займет возврат?</span>
                    </div> 

                    <div class="landing_faq_a">
                        <p>Для решения вопроса о возврате стоимости покупки, необходимо обратиться к продавцу для написания заявления на возврат денежных средств. После этого, ожидать зачисления средств на Ваш лицевой счет. При возврате средств на лицевой счет, комиссия за совершение платежа не возвращается. Продавец вернет стоимость покупки.</p>
                        <p>Срок возврата денежных средств зависит от четкости соблюдения процедур возврата продавцом.</p>
                    </div>
                </div> 
            </div> 
        </div>
    </div> 

    <div class="landing_video landing_hdn landing_res" id="video">
        <div class="landing_hdn-h"> 
            <div class="landing_ttl"><strong>Видеопрезентация Tele2 MasterCard</strong></div> 
            <div class="landing_video"> 
                <iframe width="100%" height="488" src="//www.youtube.com/embed/9bheT8JR2KU?wmode=opaque" frameborder="0" allowfullscreen=""></iframe>
            </div>
        </div>
    </div> 

    <div class="landing_buynow landing_hdn landing_res">
        <div class="landing_hdn-h">
            <p class="landing_ttl landing_ttl-white">Оплачивайте покупки виртуальной картой Tele2 MasterCard<sup>©</sup></p>
            <p class="g-ui align_center" style="margin-top: 20px;"><a href="/" class="g-button scheme_dblue size_5 box_block" style="width: 260px; margin: 0 auto;">Начать покупки</a></p>
        </div>
    </div>

    <div class="landing_f landing_hdn landing_res"> 
        <div class="g-columns mod_footer"> 
            <div class="g-columns-cell">
                <p style="margin: 0;">Служба клиентской поддержки: 611 (круглосуточно, звонок бесплатный), <a href="mailto:t2info@tele2.ru">t2info@tele2.ru</a></p>
                <p style="margin: 0;"><a href="http://img2.sotmarket.ru/2014_05_12_rules/tele2_rules.pdf" target="_blank">Правила акции</a> PDF, 160 Кб</p>
            </div> 
            <div class="g-columns-cell"> 
                <div class="g-ui align_right">
                    <span class="landing_f_lk mod_tele2"></span> 
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span class="landing_f_lk mod_mastercard"></span> 
                </div> 
            </div> 
        </div> 
    </div> 
</div>