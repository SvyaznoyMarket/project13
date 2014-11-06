<?php
/**
 * @var $page \View\DefaultLayout
 */
?><!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]> <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]> <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <title><?= $page->getTitle() ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="robots" content="noyaca"/>

    <?= $page->slotMeta() ?>
    <link rel="shortcut icon" href="/favicon.ico"/>
    <link rel="apple-touch-icon" sizes="57x57" href="/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/apple-touch-icon.png">

    <meta name="viewport" content="width=1000" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="HandheldFriendly" content="true" />
    <meta name="format-detection" content="telephone=no" />

    <?= $page->slotStylesheet() ?>
    <?= $page->slotHeadJavascript() ?>
    <?= $page->slotRelLink() ?>
    <?= $page->slotGoogleAnalytics() ?>
    <?= $page->slotMetaOg() ?>
</head>

<body class="<?= $page->slotBodyClassAttribute() ?>" data-template="<?= $page->slotBodyDataAttribute() ?>" data-id="<?= \App::$id ?>"<? if (\App::config()->debug): ?> data-debug=true<? endif ?>>

    <?= $page->slotConfig() ?>

    <?= $page->slotAdFoxBground() ?>
    <script>
    $(function(){
        var $bodybarBtn = $('.js-hide-bodybar');
            $bodybar = $('.js-bodybar');
            var 

            bodybarHide = function bodybarHide() {
                $self = $(this),
                $bodybar = $self.closest('.js-bodybar');

                $bodybar.addClass('bodybar-hide');
            },

            bodybarShow = function bodybarShow() {
                $self = $(this);

                $self.removeClass('bodybar-hide');
            };

        $bodybarBtn.on('click', bodybarHide);
        $bodybar.on('mouseenter', bodybarShow)
    });
    </script>

    <div class="bodybar js-bodybar">
        <form action="" class="sbscrBar">
            <label for="" class="sbscrBar_lbl">Сообщайте мне об акциях и специальных ценах</label>
            <div class="sbscrBar_itw">
                <input type="text" name="" id="" class="sbscrBar_it" placeholder="Ваш e-mail">
                <div class="sbscrBar_errtx">Неверно введен email</div>
            </div>
            <input type="submit" value="Подписаться" class="sbscrBar_is btn3">
            <div class="sbscrBar_tx">и получить купон на 300 руб.</div>
        </form>

        <div class="bodybar_clsr js-hide-bodybar">&#215;</div>
    </div>

    <div class="wrapper<? if ('cart' == $page->slotBodyDataAttribute()): ?> buyingpage<? endif ?>" <? if ('product_card' == $page->slotBodyDataAttribute()): ?>itemscope itemtype="http://schema.org/Product"<? endif ?>>

        <header class="header">
            <?= $page->slotHeader() ?>
        </header><!--/ Шапка-->

        <div class="content clearfix">
            <?= $page->slotContentHead() ?>

            <?= $page->slotContent() ?>
        </div><!--/ Контент-->

    </div><!--/ Шаблон -->

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
            <form action="" class="sbscrBar sbscrBar-foot">
                <label for="" class="sbscrBar_lbl">Сообщайте мне об акциях и специальных ценах</label>
                <div class="sbscrBar_itw">
                    <input type="text" name="" id="" class="sbscrBar_it" placeholder="Ваш e-mail">
                    <div class="sbscrBar_errtx">Неверно введен email</div>
                </div>
                <input type="submit" value="Подписаться" class="sbscrBar_is">
                <div class="sbscrBar_tx">и получить купон на 300 руб.</div>
            </form>

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
    </div><!--/ Подвал-->

    <a id="upper" class="upper" href="#">Наверх</a>

    <?= $page->slotUserbar() ?>

    <?= $page->slotAuth() ?>
    <?= $page->slotRegionSelection() ?>

    <div style="position:absolute; height: 0; top:0; z-index:-1;">
        <?= $page->slotBodyJavascript() ?>
        <?= $page->slotInnerJavascript() ?>
        <?= $page->slotYandexMetrika() ?>
        <?= $page->slotAdvanceSeoCounter() ?>
        <?= $page->slotAdriver() ?>
        <?= $page->slotPartnerCounter() ?>
        <?= $page->slotEnterprizeConfirmJs() ?>
        <?= $page->slotEnterprizeCompleteJs() ?>
        <?= $page->slotAdblender() ?>
        <?= $page->slotKissMetrics() ?>
        <?= $page->slotFlocktoryEnterprizeJs() ?>
        <?= $page->slotEnterprizeRegJS() ?>
    </div>
</body>
</html>
