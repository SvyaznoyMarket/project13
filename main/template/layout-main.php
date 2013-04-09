<?php
/**
 * @var $page           \View\Main\IndexPage
 */
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <?= $page->slotMeta() ?>
    <title><?= $page->getTitle() ?></title>
    <link rel="shortcut icon" href="/favicon.ico"/>
    <?= $page->slotStylesheet() ?>
    <?= $page->slotHeadJavascript() ?>
    <?= $page->slotRelLink() ?>
    <?= $page->slotGoogleAnalytics() ?>

</head>

<body id="mainPage" data-template="main" data-id="<?= \App::$id ?>"<? if (\App::config()->debug): ?> data-debug=true<? endif ?>>
<?= $page->slotConfig() ?>
<div class="bannersbox">
    <div class="bannersboxinner">
        <div class="banner banner3"><img class="rightImage" src="" alt=""/></div>
        <div class="banner banner4"><img class="leftImage" src="" alt=""/></div>
    </div>
</div>

<?= $page->slotBanner() ?>

<div class="allpage">
    <? if (\App::config()->adFox['enabled']): ?>
    <div class="adfoxWrapper" id="adfox980"></div>
    <? endif ?>

    <?= $page->slotHeader() ?>

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

    <?= $page->slotFooter() ?>

    <div class="clear"></div>
</div>

<?= $page->slotRegionSelection() ?>
<?= $page->slotAuth() ?>
<?= $page->slotYandexMetrika() ?>
<?= $page->slotBodyJavascript() ?>
<?= $page->slotInnerJavascript() ?>
<?= $page->slotMyThings() ?>
<?= $page->slotAdriver() ?>
<?= $page->slotPartnerCounter() ?>

<? if (\App::config()->analytics['enabled']): ?>
    <div id="yandexMetrika" class="jsanalytics"></div>
    <div id="heiasMain" class="jsanalytics"></div>
    <div id="luxupTracker" class="jsanalytics"></div>
    <div id="adblenderCommon" class="jsanalytics"></div>
<? endif ?>

</body>
</html>
