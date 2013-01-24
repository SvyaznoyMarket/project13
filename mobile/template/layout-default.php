<?php
/**
 * @var $page \Mobile\View\DefaultLayout
 */
?><!DOCTYPE html>
<html>
<head>
    <?= $page->slotMeta() ?>
    <title><?= $page->getTitle() ?></title>
    <link rel="shortcut icon" href="/favicon.ico"/>
    <?= $page->slotStylesheet() ?>
    <?= $page->slotJavascript() ?>
</head>
<body>
<div class="bWrap">
    <?= $page->slotHeader() ?>

    <section class="bContent"><!-- блок контента страницы -->
        <?= $page->slotContent() ?>
    </section>

    <?= $page->slotFooter() ?>
</div>
</body>
</html>