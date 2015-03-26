<?php
/**
 * @var $page           \View\Main\IndexPage
 */
?><!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]> <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]> <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <title><?= $page->getTitle() ?></title>
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

<body id="mainPage" class="<?= $page->slotBodyClassAttribute() ?>" data-template="main" data-id="<?= \App::$id ?>"<? if (\App::config()->debug): ?> data-debug=true<? endif ?>>
    <?= $page->slotConfig() ?>
    <div class="bannersbox">
        <div class="bannersboxinner">
            <div class="banner banner3"><img class="rightImage" src="" alt=""/></div>
            <div class="banner banner4"><img class="leftImage" src="" alt=""/></div>
        </div>
    </div>

    <?= $page->slotBanner() ?>

    <div class="wrapper mWrapperMain">
        <div class="content mContentMain clearfix">
            <? if (\App::config()->adFox['enabled']): ?>
                <div class="adfoxWrapper" id="adfox980"></div>
            <? endif ?>

            <div class="header">
                <?= $page->slotHeader() ?>

                <!-- Topbar -->
                <?= $page->slotTopbar() ?>
                <!-- /Topbar -->

                <!-- Header -->
                <div id="header" class="clearfix">
                    <?= $page->slotNavigation() ?>
                </div>
                <!-- /Header -->

            </div><!--/ Шапка-->

            <?= $page->slotContentHead() ?>

            <div class="bigbanner">
                <div class='bCarouselWrap'>
                    <div class='bCarousel'>
                        <div class='bCarousel__eBtnL leftArrow'></div>
                        <div class='bCarousel__eBtnR rightArrow'></div>
                        <img class="centerImage" src="" alt=""/>
                    </div>
                </div>
            </div>
        </div><!--/ Контент -->
    </div><!--/ Шаблон -->

    <div class="footer__main">
        <?= $page->slotFooter() ?>
        <a href="http://<?= \App::config()->mobileHost ?>/" class="siteVersionSwitcher js-siteVersionSwitcher" data-config="<?= $page->escape(json_encode(\App::config()->siteVersionSwitcher)) ?>">Мобильная версия</a>
    </div><!--/ Подвал -->

    <?= $page->slotAuth() ?>
    <?= $page->slotYandexMetrika() ?>
    <?= $page->slotBodyJavascript() ?>
    <?= $page->slotInnerJavascript() ?>
    <?= $page->slotPartnerCounter() ?>

    <? if (\App::config()->analytics['enabled']): ?>
        <div id="yandexMetrika" class="jsanalytics"></div>
    <? endif ?>
</body>
</html>