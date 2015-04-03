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
</head>

<body id="mainPage" data-template="<?= $page->slotBodyDataAttribute() ?>" data-id="<?= \App::$id ?>"<? if (\App::config()->debug): ?> data-debug=true<? endif ?>>
<?= $page->slotConfig() ?>
<?= $page->slotBanner() ?>

<div class="wrapper mWrapperMain">
    <div class="content clearfix">
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
                <a id="topLogo" href="/">Enter Связной</a>
                <?= $page->slotMainMenu() ?>
            </div>
            <!-- /Header -->

        </div><!--/ Шапка-->

        <?= $page->slotContent() ?>
    </div><!--/ Контент -->
</div><!--/ Шаблон -->

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
