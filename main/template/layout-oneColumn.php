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

    <script type="text/javascript">
        window.htmlStartTime = new Date().getTime();
        document.documentElement.className = document.documentElement.className.replace("no-js","js");
    </script>

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
    <?= $page->slotKissMetrics() ?>
    <?= $page->slotMetaOg() ?>
</head>

<body class="<?= $page->slotBodyClassAttribute() ?>" data-template="<?= $page->slotBodyDataAttribute() ?>" data-id="<?= \App::$id ?>"<? if (\App::config()->debug): ?> data-debug=true<? endif ?>>
    <?= $page->slotConfig() ?>

    <?= $page->slotAdFoxBground() ?>

    <div class="wrapper<? if ('cart' == $page->slotBodyDataAttribute()): ?> buyingpage<? endif ?>" <? if ('product_card' == $page->slotBodyDataAttribute()): ?>itemscope itemtype="http://schema.org/Product"<? endif ?>>

        <header class="header">
            <?= $page->slotHeader() ?>
        </header><!--/ Шапка-->

        <div class="content clearfix">
            <?= $page->slotContentHead() ?>

            <?= $page->slotContent() ?>
        </div><!--/ Контент-->

    </div><!--/ Шаблон -->

    <footer class="footer">
        <div class="footer__inner clearfix">
            <ul class="aboutList clearfix">
                <li class="aboutList__item"><a class="aboutList__link" href="/about_company">О компании</a></li>
                <li class="aboutList__item"><a class="aboutList__link" href="/shops">Магазины Enter</a></li>
                <li class="aboutList__item"><a class="aboutList__link" href="http://feedback.enter.ru/">Напишите нам</a></li>
                <li class="aboutList__item"><a class="aboutList__link" href="/how_get_order">Условия доставки</a></li>
                <li class="aboutList__item"><a class="aboutList__link" href="/how_pay">Способы оплаты</a></li>
                <li class="aboutList__item"><a class="aboutList__link" href="/credit">Покупка в кредит</a></li>
                <li class="aboutList__item mLast"><a class="aboutList__link" href="http://my.enter.ru/community/job">Работа у нас</a></li>
            </ul>

            <div class="publicInfo">
                <ul class="publicInfo__list">
                    <li class="publicInfo__list__item"><a class="publicInfo__list__link" href="/legal">Правовая информация</a></li>
                    <li class="publicInfo__list__item"><a class="publicInfo__list__link" href="/terms">Условия продажи</a></li>
                    <li class="publicInfo__list__item"><a class="publicInfo__list__link" href="/media_info">Информация о СМИ</a></li>
                    <li class="publicInfo__list__item"><a class="publicInfo__list__link" href="/refurbished-sale">Уцененные товары оптом</a></li>
                </ul>

                <p class="publicInfo__text">Указанная стоимость товаров и условия их приобретения действительны по состоянию на текущую дату.</p>
            </div>

            <ul class="socLink">
                <li class="socLink__item mFb"><a class="socLink__link" target="_blank" href="https://www.facebook.com/enter.ru"></a></li>
                <li class="socLink__item mTw"><a class="socLink__link" target="_blank" href="https://twitter.com/enter_ru"></a></li>
                <li class="socLink__item mVk"><a class="socLink__link" target="_blank" href="http://vk.com/public31456119"></a></li>
                <li class="socLink__item mYt"><a class="socLink__link" target="_blank" href="https://www.youtube.com/user/EnterLLC"></a></li>
            </ul>

            <ul class="bannersList">
                <li class="bannersList__item"><img src="/styles/footer/img/prava-potreb.gif" /></li>
                <li class="bannersList__item"><a href="/akit"><img src="/styles/footer/img/akita.png" /></a></li>
                <li class="bannersList__item"><div class="teleportator" id="teleportator"></div></li>
            </ul>

            <ul class="applist">
                <li class="applist__item mTitle">Мобильные приложения</li>
                <li class="applist__item"><a target="_blank" href="https://itunes.apple.com/ru/app/enter/id486318342?mt=8"><img src="/styles/footer/img/apple.png" /></a></li>
                
                <li class="applist__item">
                    <a target="_blank" href="http://www.windowsphone.com/ru-ru/store/app/enter/6f4c5810-682f-47dc-87b2-aced84582787">
                        <img src="/styles/footer/img/wind.png" />
                    </a>
                </li>

                <li class="applist__item">
                    <a target="_blank" href="https://play.google.com/store/apps/details?id=ru.enter">
                      <img alt="Get it on Google Play" src="/styles/footer/img/google.png" />
                    </a>
                </li>
            </ul>
        </div>

        <div class="footer__insert">
            <p class="footer__copy clearfix">&copy; ООО «Энтер» 2011–2014. ENTER® ЕНТЕР® Enter®. Все права защищены. <a id="jira" class="footer__copy__link" href="javascript:void(0)">Сообщить об ошибке</a></p>
        </div>

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
    </footer><!--/ Подвал-->

    <a id="upper" class="upper" href="#">Наверх</a>

    <?= $page->slotUserbar() ?>

    <?= $page->slotAuth() ?>
    <?= $page->slotRegionSelection() ?>

    <div style="position:absolute; height: 0; top:0; z-index:-1;">
        <?= $page->slotBodyJavascript() ?>
        <?= $page->slotInnerJavascript() ?>
        <?= $page->slotYandexMetrika() ?>
        <?= $page->slotAdvanceSeoCounter() ?>
        <?= $page->slotMyThings() ?>
        <?= $page->slotAdriver() ?>
        <?= $page->slotPartnerCounter() ?>
        <?= $page->slotEnterprizeConfirmJs() ?>
        <?= $page->slotEnterprizeCompleteJs() ?>

        <? if (\App::config()->analytics['enabled']): ?>
            <div id="adblenderCommon" class="jsanalytics"></div>
        <? endif ?>
    </div>
</body>
</html>
