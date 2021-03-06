<?php
/**
 * @var $page \View\DefaultLayout
 */
?><!DOCTYPE html>
<!--[if lte IE 9]> <html class="no-js lte-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <title><?= $page->escape($page->getTitle()) ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
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
    <?= $page->slotMicroformats() ?>
</head>

<body class="<?= $page->slotBodyClassAttribute() ?>" data-template="<?= $page->slotBodyDataAttribute() ?>" data-id="<?= \App::$id ?>"<? if (\App::config()->debug): ?> data-debug=true<? endif ?>>

    <?= $page->slotConfig() ?>

    <?= $page->slotAdFoxBground() ?>

    <div class="wrapper<? if ('cart' == $page->slotBodyDataAttribute()): ?> order-page<? endif ?>">
        <div class="header <?= $page->isMenuHamburger() ? 'header-ddnav jsMenuHamburger' : '' ?> clearfix">
            <?= $page->render('main/banner.pickup') ?>
            <div style="position: relative;">
                <div class="header__inn">
                    <?= $page->slotTopbar() ?>
                </div>

                <?= $page->slotSearchBar() ?>

                <div class="header__inn">
                    <?= $page->slotNavigation() ?>
                </div>
            </div>
        </div>

        <div class="content clearfix">
            <div class="inn">
                <?= $page->slotContentHead() ?>

                <?= $page->slotContent() ?>
            </div>
        </div><!--/ Контент-->

        <?= $page->slotCallback() ?>

    </div><!--/ Шаблон -->

    <?= $page->render('common/_footer-new') ?>

    <?= (new \View\Layout())->render('_regionSelection') /* Данный html код здесь нужен для SEO (см. SITE-6765) */ ?>
    <?= $page->slotUpper() ?>
    <?= $page->slotUserbar() ?>
    <?= $page->slotAuth() ?>
    <?= $page->slotUserConfig() ?>
    <?= $page->slotMustacheTemplates() ?>

    <div style="position:absolute; height: 0; top:0; z-index:-1;">
        <?= $page->slotBodyJavascript() ?>
        <?= $page->slotInnerJavascript() ?>
        <?= $page->slotYandexMetrika() ?>
        <?= $page->slotPartnerCounter() ?>
    </div>
</body>
</html>