<?php
/**
 * @var $page \View\DefaultLayout
 */
?><!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]> <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]> <html class="no-js lt-ie9"> <![endif]-->
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
    </head>

    <body class="<?= $page->slotBodyClassAttribute() ?>" data-template="<?= $page->slotBodyDataAttribute() ?>" data-id="<?= \App::$id ?>"<? if (\App::config()->debug): ?> data-debug=true<? endif ?>>

        <?= $page->slotConfig() ?>

        <?= $page->slotAdFoxBground() ?>

        <div class="wrapper wrapper--ep<? if ('cart' == $page->slotBodyDataAttribute()): ?> buyingpage<? endif ?>" <? if ('product_card' == $page->slotBodyDataAttribute()): ?>itemscope itemtype="http://schema.org/Product"<? endif ?>>
            <div class="header header--ep">
                <div class="header__inn">
                    <?= $page->slotHeader() ?>
                    <?= $page->slotTopbar() ?>
                </div>

                <?= $page->slotSearchBar() ?>
            </div>

            <div class="content content--ep clearfix">
                <div class="inn">
                    <?= $page->slotContent() ?>
                </div>
            </div><!--/ Контент-->
        </div><!--/ Шаблон -->

        <?= $page->render('common/_footer-ep') ?>

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
            <?= $page->slotEnterprizeConfirmJs() ?>
            <?= $page->slotEnterprizeCompleteJs() ?>
            <?= $page->slotEnterprizeRegJS() ?>
        </div>
    </body>
</html>
