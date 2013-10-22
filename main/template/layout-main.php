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
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="robots" content="noyaca"/>
    
    <script type="text/javascript">
        window.htmlStartTime = new Date().getTime();
        document.documentElement.className = document.documentElement.className.replace("no-js","js");
    </script>
    
    <?= $page->slotMeta() ?>
    <title><?= $page->getTitle() ?></title>
    <link rel="shortcut icon" href="/favicon.ico"/>
    <link rel="apple-touch-icon" sizes="57x57" href="/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/apple-touch-icon.png">
    <?= $page->slotMobileModify() ?>
    <?= $page->slotStylesheet() ?>
    <?= $page->slotHeadJavascript() ?>
    <?= $page->slotRelLink() ?>
    <?= $page->slotGoogleAnalytics() ?>
    <?= $page->slotKissMetrics() ?>

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

<div class="wrapper mWrapperMain">
    <div class="content mContentMain clearfix">
        <? if (\App::config()->adFox['enabled']): ?>
        <div class="adfoxWrapper" id="adfox980"></div>
        <? endif ?>

        <?= $page->slotHeader() ?><!--/ шапка -->

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
        <?= $page->slotSurveybar() ?>
    </div><!--/контент -->
</div><!--/шаблон -->

<div class="footer__main">
    <?= $page->slotFooter() ?>
</div><!--/подвал -->

<?//= $page->slotRegionSelection() ?>
<?//= $page->slotAuth() ?>
<?//= $page->slotYandexMetrika() ?>
<?= $page->slotBodyJavascript() ?>
<?//= $page->slotInnerJavascript() ?>
<?//= $page->slotMyThings() ?>
<?//= $page->slotAdriver() ?>
<?//= $page->slotPartnerCounter() ?>

<? if (\App::config()->analytics['enabled']): ?>
    <div id="yandexMetrika" class="jsanalytics"></div>
    <div id="adblenderCommon" class="jsanalytics"></div>
<? endif ?>

</body>
</html>
