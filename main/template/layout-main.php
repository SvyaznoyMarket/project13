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
    <?= $page->slotHeadJavascript() ?>
    <?= $page->slotRelLink() ?>
    <?= $page->slotGoogleAnalytics() ?>

</head>

<body id="mainPage" data-template="main" data-id="<?= \App::$id ?>"<? if (\App::config()->debug): ?> data-debug=true<? endif ?>>

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
    <div class="bPromoCategoryWrap clearfix">
        <div id="ogorod_1feb" class="bPromoCategory fl">
            <div class="bPromoCategory_eArrow"></div>
            <a class="bPromoCategory_eIcon fl" href="/catalog/do_it_yourself/tovari-dlya-sada-311/?from=enter_internal_promo_season"><img src="/css/mainPage/img/bPromoCategory_ogorodGreen.gif"/></a>
            <div class="fl">
                <h2 class="bPromoCategory_eTitle">Скоро дачный сезон!</h2>
                <a class="bPromoCategory_eLink" href="/catalog/do_it_yourself/tovari-dlya-sada-311/?from=enter_internal_promo_season">Товары для сада и огорода</a>
                <a class="bPromoCategory_eLink" href="catalog/doityourself/ruchnie-sadovie-instrumenti-tovari-dlya-rassadi-1564/?from=enter_internal_promo_season">Товары для рассады</a>
                <a class="bPromoCategory_eLink" href="/catalog/household/dekor-interera-gorshki-i-kashpo-1130/?from=enter_internal_promo_season">Горшки и кашпо</a>
            </div>
        </div>
        <div id="jew_1feb" class="bPromoCategory fl">
            <div class="bPromoCategory_eArrow"></div>
            <a class="bPromoCategory_eIcon fl" href="/catalog/jewel/ukrasheniya-ko-dnyu-vlyublennih-2826/?from=enter_internal_promo_season"><img src="/css/mainPage/img/bPromoCategory_jewelPink.gif"/></a>
            <div class="fl">
                <h2 class="bPromoCategory_eTitle">Подарки к весенним праздникам</h2>
                <a class="bPromoCategory_eLink" href="/catalog/ukrasheniya-k-8-marta-2922/?from=enter_internal_promo_season">Украшения к 8 марта</a>
                <p class="bPromoCategory_eLink"><a href="/catalog/podarki-na-8-marta-1522/?from=enter_internal_promo_season">Подарки на 8 марта</a></p>
                <a class="bPromoCategory_eLink" href="/catalog/parfyumeriya-i-kosmetika/podarochnie-nabori-2591/?from=enter_internal_promo_season">Подарочные наборы парфюмерии</a>
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
<?= $page->slotBodyJavascript() ?>
<?= $page->slotInnerJavascript() ?>
<?= $page->slotMyThings() ?>

<? if (\App::config()->analytics['enabled']): ?>
    <div id="adblender" class="jsanalytics"></div>
    <div id="yandexMetrika" class="jsanalytics"></div>
    <div id="heiasMain" class="jsanalytics"></div>
    <div id="luxupTracker" class="jsanalytics"></div>
<? endif ?>

</body>
</html>
