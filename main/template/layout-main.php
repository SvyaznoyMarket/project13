<?php
/**
 * @var $page           \View\Main\IndexPage
 */
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?= $page->slotMeta() ?>
    <title><?= $page->getTitle() ?></title>
    <link rel="shortcut icon" href="/favicon.ico"/>
    <?= $page->slotStylesheet() ?>
    <?= $page->slotRelLink() ?>
    <?= $page->slotGoogleAnalytics() ?>

</head>

<body id="mainPage" data-template="main" data-id="<?= \App::$id ?>">

<div class="bannersbox">
    <div class="bannersboxinner">
        <div class="banner banner3"><img class="rightImage" src="" alt=""/></div>
        <div class="banner banner4"><img class="leftImage" src="" alt=""/></div>
    </div>
</div>

<?= $page->slotBanner() ?>

<div class="allpage">
    <div class="adfoxWrapper" id="adfox980"></div>

    <?= $page->slotHeader() ?>
    <div class="bPromoCategoryWrap clearfix">
        <div class="bPromoCategory fl">
            <a class="bPromoCategory_eIcon fl" href="#"><img src=""/></a>
            <div class="fl">
                <h2 class="bPromoCategory_eTitle">Скоро дачный сезон!</h2>
                <a class="bPromoCategory_eLink" href="#">Товары для сада и огорода</a>
                <a class="bPromoCategory_eLink" href="#">Товары для рассады</a>
                <a class="bPromoCategory_eLink" href="#">Горшки и кашпо</a>
            </div>
        </div>
    </div>
    <noindex>
        <div id="mainPageSearch" class="searchbox">
            <?= $page->render('search/form-main') ?>
        </div>
    </noindex>

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
<?= $page->slotJavascript() ?>
<?= $page->slotInnerJavascript() ?>

<? if (\App::config()->analytics['enabled']): ?>
    <div id="adblender" class="jsanalytics"></div>
    <div id="yandexMetrika" class="jsanalytics"></div>
    <div id="heiasMain" class="jsanalytics"></div>
    <div id="luxupTracker" class="jsanalytics"></div>
<? endif ?>

</body>
</html>
