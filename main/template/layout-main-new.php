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
    <title><?= $page->getTitle() ?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="robots" content="noyaca"/>

    <?= $page->slotMeta() ?>
    <link rel="shortcut icon" href="/favicon.ico"/>
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

<body class="main main-new jsMainNew <?= $page->slotBodyClassAttribute() ?>" data-template="main" data-id="<?= \App::$id ?>" data-debug=<?= $page->json(\App::config()->debug) ?>>

<?= $page->slotConfig() ?>

    <div class="wrapper">
        <!-- шапка -->
        <div class="header header-new <?= \App::abTest()->isMenuHamburger() ? 'header-ddnav jsMenuHamburger' : '' ?>">

            <a class="header__bann <?= \App::abTest()->isOrderMinSumRestriction() ? 'voronezh' : '' ?>" href="<?= \App::abTest()->isOrderMinSumRestriction() ? '/self-points' : '' ?>">
                <div class="close-btn jsMainOrderSumBannerCloser"></div>
            </a>

            <?= $page->slotTopbar() ?>

            <?= $page->slotSearchBar() ?>

            <?= $page->slotNavigation() ?>

        </div>
        <!--/ шапка -->

        <div class="content">

            <?= $page->render('main/_banner2', ['banners' => (array)$page->getParam('bannerData')]) ?>

            <?= $page->render('main/_infoBlocks') ?>

            <div class="clearfix jsDivForRecommend js-showTopBar">
                <?= $page->slotRecommendations() ?>
            </div>

            <?= $page->render('main/infoBox') ?>

            <?= $page->render('main/_slidesBoxWide') ?>

            <?= $page->render('main/_popularBrands') ?>

        </div><!--/ Контент -->
    </div><!--/ Шаблон -->

    <?= $page->render('common/_footer-new') ?>

    <?= $page->slotUpper() ?>
    <?= $page->slotUserbar() ?>
    <?= $page->slotAuth() ?>
    <?= $page->slotUserConfig() ?>
    <?= $page->slotMustacheTemplates() ?>
    <?= $page->slotYandexMetrika() ?>
    <?= $page->slotBodyJavascript() ?>
    <?= $page->slotInnerJavascript() ?>
    <?= $page->slotPartnerCounter() ?>
    <?= $page->slotGifteryJS() ?>

    <? if (\App::config()->analytics['enabled']): ?>
        <div id="yandexMetrika" class="jsanalytics"></div>
    <? endif ?>
</body>
</html>
