<?php
return function($footerbar = '') { ?>
    
    <div class="footer">
        <div class="footer_t clearfix">
            <ul class="footer_cmpn clearfix">
                <li class="footer_cmpn_i"><a class="footer_cmpn_lk" href="/about_company">О компании</a></li>
                <li class="footer_cmpn_i"><a class="footer_cmpn_lk" href="/shops">Магазины Enter</a></li>
                <li class="footer_cmpn_i"><a class="footer_cmpn_lk" href="http://feedback.enter.ru/">Напишите нам</a></li>
                <li class="footer_cmpn_i"><a class="footer_cmpn_lk" href="/how_get_order">Условия доставки</a></li>
                <li class="footer_cmpn_i"><a class="footer_cmpn_lk" href="/how_pay">Способы оплаты</a></li>
                <li class="footer_cmpn_i footer_cmpn_i-last"><a class="footer_cmpn_lk" href="/credit">Покупка в кредит</a></li>
                <li class="footer_cmpn_i footer_cmpn_i-last fl-r"><a class="footer_cmpn_lk" href="http://my.enter.ru/community/job">Работа у нас</a></li>
            </ul>

            <div class="footer_inf">
                <ul class="footer_inf_lst">
                    <li class="footer_inf_lst_i"><a class="footer_inf_lst_lk" href="/legal">Правовая информация</a></li>
                    <li class="footer_inf_lst_i"><a class="footer_inf_lst_lk" href="/terms">Условия продажи</a></li>
                    <li class="footer_inf_lst_i"><a class="footer_inf_lst_lk" href="/media_info">Информация о СМИ</a></li>
                    <li class="footer_inf_lst_i"><a class="footer_inf_lst_lk" href="/refurbished-sale">Уцененные товары оптом</a></li>
                </ul>

                <p class="footer_inf_tx">Указанная стоимость товаров и условия их приобретения действительны по состоянию на текущую дату.</p>
            </div>

            <ul class="footer_socnet">
                <li class="footer_socnet_i footer_socnet_i-fb"><a class="footer_socnet_lk" target="_blank" href="https://www.facebook.com/enter.ru"></a></li>
                <li class="footer_socnet_i footer_socnet_i-tw"><a class="footer_socnet_lk" target="_blank" href="https://twitter.com/enter_ru"></a></li>
                <li class="footer_socnet_i footer_socnet_i-vk"><a class="footer_socnet_lk" target="_blank" href="http://vk.com/public31456119"></a></li>
                <li class="footer_socnet_i footer_socnet_i-ytb"><a class="footer_socnet_lk" target="_blank" href="https://www.youtube.com/user/EnterLLC"></a></li>
                <li class="footer_socnet_i footer_socnet_i-odnk"><a class="footer_socnet_lk" target="_blank" href="http://www.odnoklassniki.ru/group/53202890129511"></a></li>
            </ul>

            <ul class="footer_bnnr">
                <li class="footer_bnnr_i"><img src="/styles/footer/img/prava-potreb.gif" /></li>
                <li class="footer_bnnr_i"><a href="/akit"><img src="/styles/footer/img/akita.png" /></a></li>
                <li class="footer_bnnr_i"><div class="teleportator" id="teleportator"></div></li>
            </ul>

            <ul class="footer_app">
                <li class="footer_app_i footer_app_i-t">Мобильные приложения</li>
                <li class="footer_app_i"><a target="_blank" href="https://itunes.apple.com/ru/app/enter/id486318342?mt=8"><img class="footer_app_img" src="/styles/footer/img/apple.png" /></a></li>
                
                <li class="footer_app_i">
                    <a target="_blank" href="http://www.windowsphone.com/ru-ru/store/app/enter/6f4c5810-682f-47dc-87b2-aced84582787">
                        <img class="footer_app_img" src="/styles/footer/img/wind.png" />
                    </a>
                </li>

                <li class="footer_app_i">
                    <a target="_blank" href="https://play.google.com/store/apps/details?id=ru.enter">
                      <img class="footer_app_img" alt="Get it on Google Play" src="/styles/footer/img/google.png" />
                    </a>
                </li>
            </ul>
        </div>

        <footer class="footer_b">
            <?= $footerbar ?>

            <div class="footer_cpy clearfix">
                <a id="jira" class="footer_cpy_r" href="javascript:void(0)">Сообщить об ошибке</a>
                <div class="footer_cpy_l">&copy; ООО «Энтер» 2011–2014. ENTER® ЕНТЕР® Enter®. Все права защищены.</div>
                <div class="footer_cpy_c"><a href="" class="footer_cpy_mbl">Мобильный сайт</a></div>
            </div>
        </footer>

        <!-- krible.ru Teleportator -->
        <script type="text/javascript">
        var kribleCode = '5e14662e854af6384a9a84af28874dd8';
        var kribleTeleportParam = {'text': '#ffffff', 'button': '#ffa901', 'link':'#000000'};
        (function (d, w) {
            var n = d.getElementsByTagName("script")[0],
                s = d.createElement("script"),
                f = function() {
                    n.parentNode.insertBefore(s, n);
                };
            s.type = "text/javascript";
            s.async = true;
            s.src = 'http://chat.krible.ru/arena/'+
              kribleCode.substr(0,2)+'/'+kribleCode+'/teleport.js';
            if (w.opera == "[object Opera]") {
                d.addEventListener("DOMContentLoaded", f);
            } else {
                f();
            }
        })(document, window);
        </script>
        <!-- /krible.ru Teleportator end -->
    </div>
    
<? };