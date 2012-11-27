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
    <?//= $page->slotGoogleAnalytics() ?>

    <script type="text/javascript">
        var _gaq = _gaq || [];
        _gaq.push(['_setAccount', 'UA-25485956-1']);
        _gaq.push(['_addOrganic', 'nova.rambler.ru', 'query']);
        _gaq.push(['_addOrganic', 'go.mail.ru', 'q']);
        _gaq.push(['_addOrganic', 'nigma.ru', 's']);
        _gaq.push(['_addOrganic', 'webalta.ru', 'q']);
        _gaq.push(['_addOrganic', 'aport.ru', 'r']);
        _gaq.push(['_addOrganic', 'poisk.ru', 'text']);
        _gaq.push(['_addOrganic', 'km.ru', 'sq']);
        _gaq.push(['_addOrganic', 'liveinternet.ru', 'ask']);
        _gaq.push(['_addOrganic', 'quintura.ru', 'request']);
        _gaq.push(['_addOrganic', 'search.qip.ru', 'query']);
        _gaq.push(['_addOrganic', 'gde.ru', 'keywords']);
        _gaq.push(['_addOrganic', 'gogo.ru', 'q']);
        _gaq.push(['_addOrganic', 'ru.yahoo.com', 'p']);
        _gaq.push(['_addOrganic', 'images.yandex.ru', 'q', true]);
        _gaq.push(['_addOrganic', 'blogsearch.google.ru', 'q', true]);
        _gaq.push(['_addOrganic', 'blogs.yandex.ru', 'text', true]);
        _gaq.push(['_addOrganic', 'ru.search.yahoo.com','p']);
        _gaq.push(['_addOrganic', 'ya.ru', 'q']);
        _gaq.push(['_addOrganic', 'm.yandex.ru','query']);
        _gaq.push(['_trackPageview']);
        _gaq.push(['_trackPageLoadTime']);
        (function() { var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true; ga.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js'; var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s); })();
    </script>

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

    <div id="header">
        <a id="topLogo" href="/">Enter Связной</a>
        <?= $page->slotRootCategory() ?>
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

<?= $page->slotJavascript() ?>
<?= $page->slotInnerJavascript() ?>

<? if (\App::config()->analytics['enabled']): ?>
    <div id="myThingsMain" class="jsanalytics"></div>
    <div id="adblender" class="jsanalytics"></div>
    <div id="yandexMetrika" class="jsanalytics"></div>
    <div id="heiasMain" class="jsanalytics"></div>
    <div id="luxupTracker" class="jsanalytics"></div>
<? endif ?>

</body>
</html>
