<div class="footer">
    <div class="footer_t clearfix">
        <div class="footer-menu-wrap">
            <ul class="footer_cmpn clearfix">
                <li class="footer_cmpn_i"><a class="footer_cmpn_lk nowrap" href="/about_company">О компании</a></li>
                <li class="footer_cmpn_i"><a class="footer_cmpn_lk" href="/how_pay">Способы оплаты</a></li>
                <? if (\App::config()->payment['creditEnabled']) : ?>
                    <li class="footer_cmpn_i"><a class="footer_cmpn_lk" href="/credit">Покупка в кредит</a></li>
                <? endif ?>
                <li class="footer_cmpn_i"><a class="footer_cmpn_lk" href="/supplier/new">Поставщикам</a></li>
                <li class="footer_cmpn_i footer_cmpn_i-last"><a class="footer_cmpn_lk" href="/research">ЦСИ</a></li>
            </ul>
        </div>

        <div class="footer-wrap clearfix">
            <ul class="footer-discounts clearfix">
                <? if (\App::config()->partners['sberbankSpasibo']['enabled']): ?><li><a class="footer-discounts__link" href="/sberbank_spasibo"><i class="i-discount i-discount-sb"></i></a></li><? endif ?>
                <? if (\App::config()->partners['MnogoRu']['enabled']): ?><li><a class="footer-discounts__link" href="/mnogo-ru"><i class="i-discount i-discount-mnogoru"></i></a></li><? endif ?>
            </ul>
            <a class="footer__referral" href="/partner">
                Зарабатывайте с Enter
            </a>
            <noindex>
                <ul class="footer_socnet clearfix">
                    <li class="footer_socnet_i"><a class="footer_socnet_lk" target="_blank" rel="nofollow" href="https://www.facebook.com/enter.ru"><i class="i-share i-share-fb"></i></a></li>
                    <li class="footer_socnet_i"><a class="footer_socnet_lk" target="_blank" rel="nofollow" href="https://twitter.com/enter_ru"><i class="i-share i-share-tw"></i></a></li>
                    <li class="footer_socnet_i"><a class="footer_socnet_lk" target="_blank" rel="nofollow" href="https://vk.com/public31456119"><i class="i-share i-share-vk"></i></a></li>
                    <li class="footer_socnet_i"><a class="footer_socnet_lk" target="_blank" rel="nofollow" href="https://www.youtube.com/user/EnterLLC"><i class="i-share i-share-yt"></i></a></li>
                    <li class="footer_socnet_i"><a class="footer_socnet_lk" target="_blank" rel="nofollow" href="https://ok.ru/enterllc"><i class="i-share i-share-od"></i></a></li>
                </ul>
            </noindex>
            <div class="clearfix">
                <ul class="footer_bnnr">
                    <li class="footer_bnnr_i"><img src="/styles/footer/img/prava-potreb.png" /></li>
                    <noindex>
                        <li class="footer_bnnr_i"><a href="http://www.akit.ru/enter/" rel="nofollow"><img src="/styles/footer/img/akit.png" /></a></li>
                    </noindex>
                    <li class="footer_bnnr_i"><div class="teleportator" id="teleportator"></div></li>
                </ul>

                <noindex>
                    <ul class="footer_app">
                        <li class="footer_app_i"><a target="_blank" rel="nofollow" href="https://itunes.apple.com/ru/app/enter/id486318342?mt=8"><img class="footer_app_img" src="/styles/footer/img/apple.png" /></a></li>

                        <li class="footer_app_i">
                            <a target="_blank" rel="nofollow" href="https://play.google.com/store/apps/details?id=ru.enter">
                                <img class="footer_app_img" alt="Get it on Google Play" src="/styles/footer/img/google.png" />
                            </a>
                        </li>
                    </ul>
                </noindex>
            </div>

            <div class="footer_inf">
                <ul class="footer_inf_lst">
                    <li class="footer_inf_lst_i"><a class="footer_inf_lst_lk" href="/legal">Правовая информация</a></li>
                    <li class="footer_inf_lst_i"><a class="footer_inf_lst_lk" href="/terms-sordex">Условия продажи</a></li>
                    <li class="footer_inf_lst_i"><a class="footer_inf_lst_lk" href="/media_info">Информация о СМИ</a></li>
                </ul>

                <p class="footer_inf_tx">Указанная стоимость товаров и условия их приобретения действительны по состоянию на текущую дату.</p>
            </div>
        </div>


        <footer class="footer_b">
            <div class="footer-wrap">
                <div class="footer_cpy clearfix">
                    <a href="#" class="footer_cpy_r js-g-jira">Сообщить об ошибке</a>
                    <div class="footer_cpy_l">&copy; ООО «Энтер» 2011–<?= date('Y') ?>. ENTER® ЕНТЕР® Enter®. Все права защищены.</div>
                    <div class="footer_cpy_c"><a href="//<?= \App::config()->mobileHost ?>" class="footer_cpy_mbl js-siteVersionSwitcher" data-config="{&quot;cookieName&quot;:&quot;mobile&quot;,&quot;cookieLifetime&quot;:630720000}">Мобильная версия</a></div>
                </div>
            </div>
        </footer>
    </div>
</div><!--/ Подвал -->