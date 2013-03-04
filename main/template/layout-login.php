<?php
/**
 * @var $page \View\DefaultLayout
 */
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <?= $page->slotMeta() ?>
    <title><?= $page->getTitle() ?></title>
    <link rel="shortcut icon" href="/favicon.ico"/>
    <?= $page->slotStylesheet() ?>
    <?= $page->slotHeadJavascript() ?>
    <?= $page->slotRelLink() ?>
    <?= $page->slotGoogleAnalytics() ?>
    <?= $page->slotMetaOg() ?>
</head>
<body class="<?= $page->slotBodyClassAttribute() ?>" data-template="<?= $page->slotBodyDataAttribute() ?>" data-id="<?= \App::$id ?>">
    <div class="allpage" id="page">
    <? if (\App::config()->adFox['enabled']): ?>
    <div class="adfoxWrapper" id="adfoxbground"></div>
    <? endif ?>

        <div class="allpageinner">

            <?= $page->slotContent() ?>

            <div class="clear"></div>
        </div>
        <div class="clear"></div>
    </div>

    <?= $page->render('order/_footer') ?>
    <?= $page->slotFooter() ?>

    <?= $page->slotRegionSelection() ?>
    <?= $page->slotBodyJavascript() ?>
    <?= $page->slotInnerJavascript() ?>
    <?= $page->slotAuth() ?>
    <?= $page->slotYandexMetrika() ?>
    <?= $page->slotAdvanceSeoCounter() ?>
    <?= $page->slotMyThings() ?>

    <? if (\App::config()->analytics['enabled']): ?>
        <div id="luxupTracker" class="jsanalytics"></div>
    <? endif ?>
  
    <?= $page->slotAdriver() ?>
  
</body>
</html>
