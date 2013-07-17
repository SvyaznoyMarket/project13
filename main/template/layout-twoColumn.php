<?php



/**
 * @var $page   \View\DefaultLayout
 * @var $user   \Session\User
 */
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <?= $page->slotMeta() ?>
    <title><?= $page->getTitle() ?></title>
    <link rel="shortcut icon" href="/favicon.ico"/>
    <link rel="apple-touch-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" sizes="57x57" href="/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/apple-touch-icon.png">
    <?= $page->slotMobileModify() ?>
    <?= $page->slotStylesheet() ?>
    <?= $page->slotHeadJavascript() ?>
    <?= $page->slotRelLink() ?>
    <?= $page->slotGoogleAnalytics() ?>
    <?= $page->slotMetaOg() ?>
</head>
<body class="<?= $page->slotBodyClassAttribute() ?>" data-template="<?= $page->slotBodyDataAttribute() ?>" data-id="<?= \App::$id ?>"<? if (\App::config()->debug): ?> data-debug=true<? endif ?>>
    <?= $page->slotConfig() ?>
    <div class="allpage" id="page">
    <? if (\App::config()->adFox['enabled']): ?>
    <div class="adfoxWrapper" id="adfoxbground"></div>
    <? endif ?>

        <div class="allpageinner clearfix">
            <?= $page->slotHeader() ?>

            <?= $page->slotContentHead() ?>

            <div class="float100">
                <div class="column685">
                    <?= $page->slotContent() ?>
                </div>
            </div>
            <div class="column215">
                <?= $page->slotSidebar() ?>
            </div>

            <div class="clear"></div>

            <?= $page->slotSeoContent() ?>
            
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
    <?= $page->slotMyThings() ?>
    <?= $page->slotAdriver() ?>
    <?= $page->slotPartnerCounter() ?>

    <?= $page->slotLiveTex() ?>


    <? if (\App::config()->analytics['enabled']): ?>
        <div id="luxupTracker" class="jsanalytics"></div>
        <div id="adblenderCommon" class="jsanalytics"></div>
    <? endif ?>
	
	<a id="upper" href="#">Наверх</a>

</body>
</html>
