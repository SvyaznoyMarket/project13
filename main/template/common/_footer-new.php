<div class="footer">
    <div class="footer_t clearfix">
        <div class="footer_cmpn clearfix">
            <ul class="inn">
                <li class="footer_cmpn_i"><a class="footer_cmpn_lk" href="/about_company">О компании</a></li>
                <li class="footer_cmpn_i"><a class="footer_cmpn_lk" href="/how_pay">Способы оплаты</a></li>
                <li class="footer_cmpn_i"><a class="footer_cmpn_lk" href="/credit">Покупка в кредит</a></li>
                <li class="footer_cmpn_i"><a class="footer_cmpn_lk" href="http://my.enter.ru/job">Работа у нас</a></li>
                <li class="footer_cmpn_i"><a class="footer_cmpn_lk" href="/b2b">Корпоративным клиентам</a></li>
                <li class="footer_cmpn_i footer_cmpn_i-last"><a class="footer_cmpn_lk" href="/research">ЦСИ</a></li>
            </ul>
        </div>

        <div class="inn">
            <ul class="footer-discounts clearfix">
                <li><a class="footer-discounts__link" href="/sberbank_spasibo"><i class="i-discount i-discount-sb"></i></a></li>
                <li><a class="footer-discounts__link" href="/mnogo-ru"><i class="i-discount i-discount-mnogoru"></i></a></li>
                <li><a class="footer-discounts__link" href="/sclub"><i class="i-discount i-discount-svyaznoy"></i></a></li>
            </ul>

        <ul class="footer-discounts clearfix">
            <li><a class="footer-discounts__link" href="/sberbank_spasibo"><i class="i-discount i-discount-sb"></i></a></li>
            <li><a class="footer-discounts__link" href="/mnogo-ru"><i class="i-discount i-discount-mnogoru"></i></a></li>
            <li><a class="footer-discounts__link" href="/sclub"><i class="i-discount i-discount-svyaznoy"></i></a></li>
        </ul>
        <ul class="footer_socnet clearfix">
            <li class="footer_socnet_i"><a class="footer_socnet_lk" target="_blank" href="https://www.facebook.com/enter.ru"><i class="i-share i-share-fb"></i></a></li>
            <li class="footer_socnet_i"><a class="footer_socnet_lk" target="_blank" href="https://twitter.com/enter_ru"><i class="i-share i-share-tw"></i></a></li>
            <li class="footer_socnet_i"><a class="footer_socnet_lk" target="_blank" href="https://vk.com/public31456119"><i class="i-share i-share-vk"></i></a></li>
            <li class="footer_socnet_i"><a class="footer_socnet_lk" target="_blank" href="https://www.youtube.com/user/EnterLLC"><i class="i-share i-share-yt"></i></a></li>
            <li class="footer_socnet_i"><a class="footer_socnet_lk" target="_blank" href="http://ok.ru/enterllc"><i class="i-share i-share-od"></i></a></li>
        </ul>

            <ul class="footer_bnnr clearfix">
                <li class="footer_bnnr_i"><img src="/styles/footer/img/prava-potreb.png" /></li>
                <li class="footer_bnnr_i"><a href="/akit"><img src="/styles/footer/img/akit.png" /></a></li>
                <li class="footer_bnnr_i"><div class="teleportator" id="teleportator"></div></li>
            </ul>

            <ul class="footer_app">
                <li class="footer_app_i"><a target="_blank" href="https://itunes.apple.com/ru/app/enter/id486318342?mt=8"><img class="footer_app_img" src="/styles/footer/img/apple.png" /></a></li>

                <li class="footer_app_i">
                    <a target="_blank" href="https://play.google.com/store/apps/details?id=ru.enter">
                        <img class="footer_app_img" alt="Get it on Google Play" src="/styles/footer/img/google.png" />
                    </a>
                </li>
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
        </div>
    </div>

    <footer class="footer_b">
        <div class="inn">
            <div class="footer_cpy clearfix">
                <a id="jira" class="footer_cpy_r" href="javascript:void(0)">Сообщить об ошибке</a>
                <div class="footer_cpy_l">&copy; ООО «Энтер» 2011–<?= date('Y') ?>. ENTER® ЕНТЕР® Enter®. Все права защищены.</div>
                <div class="footer_cpy_c"><a href="http://<?= \App::config()->mobileHost ?>" class="footer_cpy_mbl js-siteVersionSwitcher" data-config="{&quot;cookieName&quot;:&quot;mobile&quot;,&quot;cookieLifetime&quot;:630720000}">Мобильная версия</a></div>
            </div>
        </div>
    </footer>

    <?= $page->render('common/__script-krible') ?>
</div><!--/ Подвал -->