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
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="robots" content="noyaca"/>

    <script type="text/javascript">
        window.htmlStartTime = new Date().getTime();
        document.documentElement.className = document.documentElement.className.replace("no-js","js");
    </script>

    <?= $page->slotMeta() ?>
    <title><?= $page->getTitle() ?></title>
    <link rel="shortcut icon" href="/favicon.ico"/>
    <link rel="apple-touch-icon" sizes="57x57" href="/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/apple-touch-icon.png">
    <?= $page->slotMobileModify() ?>
    <?= $page->slotStylesheet() ?>
    <?= $page->slotHeadJavascript() ?>
    <?= $page->slotRelLink() ?>
    <?= $page->slotGoogleAnalytics() ?>
    <?= $page->slotKissMetrics() ?>
    <?= $page->slotMetaOg() ?>
</head>

<body class="<?= $page->slotBodyClassAttribute() ?>" data-template="<?= $page->slotBodyDataAttribute() ?>" data-id="<?= \App::$id ?>"<? if (\App::config()->debug): ?> data-debug=true<? endif ?>>
    <?= $page->slotConfig() ?>
    <div class="allpage" id="page">
    <? if (\App::config()->adFox['enabled']): ?>
    <div class="adfoxWrapper" id="adfoxbground"></div>
    <? endif ?>

        <div class="clearfix allpageinner<? if ('cart' == $page->slotBodyDataAttribute()): ?> buyingpage<? endif ?>" <? if ('product_card' == $page->slotBodyDataAttribute()): ?>itemscope itemtype="http://schema.org/Product"<? endif ?>>
            <?= $page->slotHeader() ?>

            <?= $page->slotContentHead() ?>

            <?= $page->slotContent() ?>

            <div class="clear"></div>
        </div>
    </div>



    <?//= $page->slotUserbar() ?>
    <?= $page->slotSurveybar() ?>

    <?= $page->slotRegionSelection() ?>
    <?= $page->slotBodyJavascript() ?>
    <?= $page->slotInnerJavascript() ?>
    <?= $page->slotAuth() ?>
    <?= $page->slotYandexMetrika() ?>
    <?= $page->slotAdvanceSeoCounter() ?>
    <?= $page->slotMyThings() ?>
    <?= $page->slotAdriver() ?>
    <?= $page->slotPartnerCounter() ?>
    <?//= $page->slotEnterleads() ?>

    <? if (\App::config()->analytics['enabled']): ?>
        <div id="adblenderCommon" class="jsanalytics"></div>
    <? endif ?>
	
	<a id="upper" href="#">Наверх</a>

    <footer class="footer">
        <div class="footer__inner clearfix">
            <ul class="aboutList">
                <li class="aboutList__item"><a class="aboutList__link" href="/about_company">О компании</a></li>
                <li class="aboutList__item"><a class="aboutList__link" href="/shops">Магазины Enter</a></li>
                <li class="aboutList__item"><a class="aboutList__link" href="http://feedback.enter.ru/">Напишите нам</a></li>
                <li class="aboutList__item"><a class="aboutList__link" href="/how_get_order">Условия доставки</a></li>
                <li class="aboutList__item"><a class="aboutList__link" href="/how_pay">Способы оплаты</a></li>
                <li class="aboutList__item"><a class="aboutList__link" href="/credit">Покупка в кредит</a></li>
                <li class="aboutList__item"><a class="aboutList__link" href="http://my.enter.ru/community/job">Работа у нас</a></li>
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
                <li class="bannersList__item"><div id="teleportator"></div></li>
            </ul>

            <ul class="applist">
                <li class="applist__item">
                    <a href="https://play.google.com/store/apps/details?id=ru.enter">
                      <img alt="Get it on Google Play" src="https://developer.android.com/images/brand/ru_generic_rgb_wo_45.png" />
                    </a>
                </li>
                <li class="applist__item"><a href=""><img src="/styles/footer/img/appstore.png" /></a></li>
                <li class="applist__item mTitle">Мобильные приложения</li>
            </ul>
        </div>

        <div class="footer__insert">
            <p class="footer__copy clearfix">&copy; ООО «Энтер» 2011–2013. ENTER® ЕНТЕР® Enter®. Все права защищены. <a class="footer__copy__link" href="">Сообщить об ошибке</a></p>
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
</body>
</html>
