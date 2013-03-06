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
<body class="<?= $page->slotBodyClassAttribute() ?>" data-template="<?= $page->slotBodyDataAttribute() ?>" data-id="<?= \App::$id ?>"<? if (\App::config()->debug): ?> data-debug=true<? endif ?>>
    <div class="allpage" id="page">
    <? if (\App::config()->adFox['enabled']): ?>
    <div class="adfoxWrapper" id="adfoxbground"></div>
    <? endif ?>

        <div class="allpageinner<? if ('cart' == $page->slotBodyDataAttribute()): ?> buyingpage<? endif ?>">
            <?= $page->slotHeader() ?>

            <?= $page->slotContentHead() ?>

            <?= $page->slotContent() ?>

            <div class="clear"></div>
        </div>
        <div class="clear"></div>
    </div>


    <?= $page->slotFooter() ?>
    <?= $page->slotUserbar() ?>

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
	
	<a id="upper" href="#">Наверх</a>

	
    <?= $page->slotAdriver() ?>
	
</body>
</html>
