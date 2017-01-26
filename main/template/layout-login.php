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

    <div class="wrapper">
        <div class="content mContentOrder clearfix">
            <?= $page->slotContent() ?>
        </div><!--/ Контент -->
    </div><!--/ Шаблон -->

    <footer class="footer__order">
        <div class="footer__insert">
            <p class="footer__copy clearfix">&copy; ООО «Энтер» 2011–<?= date('Y') ?>. ENTER® ЕНТЕР® Enter®. Все права защищены. <a id="jira" class="footer__copy__link" href="javascript:void(0)">Сообщить об ошибке</a></p>
        </div>
    </footer>

    <?= (new \View\Layout())->render('_regionSelection') /* Данный html код здесь нужен для SEO (см. SITE-6765) */ ?>
    <?= $page->slotUserConfig() ?>
    <?= $page->slotBodyJavascript() ?>
    <?= $page->slotInnerJavascript() ?>
    <?= $page->slotAuth() ?>
    <?= $page->slotMustacheTemplates() ?>
    <?= $page->slotYandexMetrika() ?>
    <?= $page->slotPartnerCounter() ?>

</body>
</html>
