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
        <div class="content mContentOrder clearfix">
            <?= $page->slotContent() ?>
        </div><!--/ Контент -->
    </div><!--/ Шаблон -->

    <?= $page->render('order/_footer') ?>

    <?= $page->slotRegionSelection() ?>
    <?= $page->slotBodyJavascript() ?>
    <?= $page->slotInnerJavascript() ?>
    <?= $page->slotAuth() ?>
    <?= $page->slotYandexMetrika() ?>
    <?= $page->slotAdvanceSeoCounter() ?>
    <?= $page->slotPartnerCounter() ?>
    <?= $page->slotAdblender() ?>
    <?= $page->slotAdriver() ?>
    <?= $page->slotKissMetrics() ?>
  
</body>
</html>
