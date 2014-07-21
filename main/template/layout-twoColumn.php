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
    <link rel="apple-touch-icon" href="/favicon.ico">
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

    <div class="wrapper">
        <header class="header">
            <?= $page->slotHeader() ?>
        </header><!--/ Шапка-->

        <div class="content mContentOrder clearfix">
            <?= $page->slotContentHead() ?>

            <div class="float100">
                <div class="column685">
                    <?= $page->slotContent() ?>
                </div>
            </div>

            <div class="column215">
                <?= $page->slotSidebar() ?>
            </div>
            
            <?= $page->slotSeoContent() ?>
        </div><!--/ Контент -->
    </div><!--/ Шаблон -->

    <? if (!(bool)\App::exception()->all()) echo $page->render('order/_footer') ?>

    <a class="upper" id="upper" href="#">Наверх</a>

    <?= $page->slotUserbar() ?>
    <?= $page->slotRegionSelection() ?>
    <?= $page->slotAuth() ?>
    
    <div style="position:absolute; height: 0; z-index:-1; top: 0;">
        <?= $page->slotBodyJavascript() ?>
        <?= $page->slotInnerJavascript() ?>
        <?= $page->slotYandexMetrika() ?>
        <?= $page->slotAdriver() ?>
        <?= $page->slotPartnerCounter() ?>
        <?= $page->slotEnterprizeConfirmJs() ?>
        <?= $page->slotEnterprizeCompleteJs() ?>
        <?= $page->slotAdblender() ?>
        <?= $page->slotKissMetrics() ?>
        <?= $page->slotFlocktoryEnterprizeJs() ?>
    </div>
</body>
</html>
