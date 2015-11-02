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
    <title><?= $page->escape($page->getTitle()) ?></title>
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

<body class="main jsMainNew <?= $page->slotBodyClassAttribute() ?>" data-template="main" data-id="<?= \App::$id ?>" data-debug=<?= $page->json(\App::config()->debug) ?>>

<?= $page->slotConfig() ?>

    <div class="wrapper">
        <!-- шапка -->
        <div class="header">
            <?= $page->render('main/banner.pickup') ?>

            <div class="header__inn">
                <?= $page->slotTopbar() ?>
            </div>

            <?= $page->slotSearchBar() ?>

            <div class="header__inn">
                <?= $page->slotNavigation() ?>
            </div>

        </div>
        <!--/ шапка -->

        <div class="content">

            <div class="inn">

            <?= $page->render('main/_banner2', ['banners' => $page->getParam('banners'), []]) ?>

            <?= $page->render('main/_infoBlocks') ?>

            <div class="clearfix jsDivForRecommend js-showTopBar">
                <?= $page->slotRecommendations() ?>
            </div>

            <?= $page->slotInfoBox() ?>

            <?= $page->render('main/_slidesBoxWide') ?>

            <?= $page->render('main/_popularBrands') ?>

            </div>

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

    <? if (\App::config()->analytics['enabled']): ?>
        <div id="yandexMetrika" class="jsanalytics"></div>
    <? endif ?>
</body>
</html>
