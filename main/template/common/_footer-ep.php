<div class="footer footer--ep">
    <div class="footer_t clearfix">
        <ul class="footer_cmpn clearfix">
            <li class="footer_cmpn_i"><a class="footer_cmpn_lk" href="/about_company">О компании</a></li>
            <li class="footer_cmpn_i"><a class="footer_cmpn_lk" href="mailto:feedback@enter.ru">Обратная связь</a></li>
            <li class="footer_cmpn_i"><a class="footer_cmpn_lk" href="/how_pay">Способы оплаты</a></li>
            <? if (\App::config()->payment['creditEnabled']) : ?>
                <li class="footer_cmpn_i"><a class="footer_cmpn_lk" href="/credit">Покупка в кредит</a></li>
            <? endif ?>
            <li class="footer_cmpn_i"><a class="footer_cmpn_lk" href="http://my.enter.ru/job">Работа у нас</a></li>
            <li class="footer_cmpn_i"><a class="footer_cmpn_lk" href="/b2b">Корпоративным клиентам</a></li>
            <li class="footer_cmpn_i footer_cmpn_i-last"><a class="footer_cmpn_lk" href="/research">ЦСИ</a></li>
        </ul>

        <div class="footer_inf">
            <ul class="footer_inf_lst">
                <li class="footer_inf_lst_i"><a class="footer_inf_lst_lk" href="/legal">Правовая информация</a></li>
                <li class="footer_inf_lst_i"><a class="footer_inf_lst_lk" href="/terms">Условия продажи</a></li>
                <li class="footer_inf_lst_i"><a class="footer_inf_lst_lk" href="/media_info">Информация о СМИ</a></li>
                <li class="footer_inf_lst_i"><a class="footer_inf_lst_lk" href="/refurbished-sale">Уцененные товары оптом</a></li>
                <li class="footer_inf_lst_i"><a class="footer_inf_lst_lk" href="/adv">Рекламные возможности</a></li>
            </ul>

            <p class="footer_inf_tx">Указанная стоимость товаров и условия их приобретения действительны по состоянию на текущую дату.</p>
        </div>

        <ul class="footer_socnet">
            <li class="footer_socnet_i"><a class="footer_socnet_lk" target="_blank" href="https://www.facebook.com/enter.ru"><i class="i-share i-share-fb"></i></a></li>
            <li class="footer_socnet_i"><a class="footer_socnet_lk" target="_blank" href="https://twitter.com/enter_ru"><i class="i-share i-share-tw"></i></a></li>
            <li class="footer_socnet_i"><a class="footer_socnet_lk" target="_blank" href="http://vk.com/public31456119"><i class="i-share i-share-vk"></i></a></li>
            <li class="footer_socnet_i"><a class="footer_socnet_lk" target="_blank" href="https://www.youtube.com/user/EnterLLC"><i class="i-share i-share-yt"></i></a></li>
            <li class="footer_socnet_i"><a class="footer_socnet_lk" target="_blank" href="http://www.odnoklassniki.ru/group/53202890129511"><i class="i-share i-share-od"></i></a></li>
        </ul>
    </div>

    <footer class="footer_b">
        <div class="footer_cpy clearfix">
            <a id="jira" class="footer_cpy_r" href="javascript:void(0)">Сообщить об ошибке</a>
            <div class="footer_cpy_l">&copy; ООО «Энтер» 2011–2015. ENTER® ЕНТЕР® Enter®. Все права защищены.</div>
            <div class="footer_cpy_c"><a href="http://<?= \App::config()->mobileHost ?>" class="footer_cpy_mbl js-siteVersionSwitcher" data-config="{&quot;cookieName&quot;:&quot;mobile&quot;,&quot;cookieLifetime&quot;:630720000}">Мобильная версия</a></div>
        </div>
    </footer>
</div><!--/ Подвал -->