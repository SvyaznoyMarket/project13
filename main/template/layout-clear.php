<?php
/**
 * @var $page \View\DefaultLayout
 */
?>
<html>
<head>
    <title><?= $page->escape($page->getTitle()) ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="robots" content="noyaca"/>

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
    <?= $page->slotGoogleAnalytics() ?>
</head>

<body
    class="<?= $page->slotBodyDataAttribute() ?>"
    data-template="<?= $page->slotBodyDataAttribute()?>"
    data-id="<?= \App::$id ?>"
    <? if (\App::config()->debug): ?>data-debug=true<? endif ?>
    >

    <?= (new \View\Layout())->render('_regionSelection') /* Данный html код здесь нужен для SEO (см. SITE-6765) */ ?>
    <?= $page->slotConfig() ?>
    <?= $page->slotUserConfig() ?>
    <?= $page->slotContent() ?>
    <?= $page->slotMustacheTemplates() ?>
</body>
</html>