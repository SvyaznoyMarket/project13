<?php
/**
 * @var $page \View\DefaultLayout
 */
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <?= $page->slotMeta() ?>
    <title><?= $page->getTitle() ?></title>
    <link rel="shortcut icon" href="/favicon.ico"/>
    <?= $page->slotStylesheet() ?>
    <?= $page->slotRelLink() ?>
    <?= $page->slotGoogleAnalytics() ?>
    <?= $page->slotMetaOg() ?>
</head>
<body data-template="<?= $page->slotBodyDataAttribute() ?>">
    <div class="allpage" id="page">
        <div class="adfoxWrapper" id="adfoxbground"></div>

        <div class="allpageinner">
            <?= $page->slotHeader() ?>

            <?= $page->slotContentHead() ?>

            <?= $page->slotContent() ?>

            <div class="clear"></div>
        </div>
        <div class="clear"></div>
    </div>


    <?= $page->slotFooter() ?>

    <!-- Lightbox -->
    <div class="lightbox">
        <div class="lightboxinner">
            <div class="dropbox" style="left:733px; display:none;">
                <p>Перетащите сюда</p>
            </div>
            <!-- Flybox -->
            <ul class="lightboxmenu">
                <li class="fl">
                    <a href="<?= $page->url('user.login') ?>" class="point point1"><b></b>Личный кабинет</a>
                </li>
                <li>
                    <a href="<?=  $page->url('cart') ?>" class="point point2"><b></b>Моя корзина<span class="total" style="display:none;">
                        <span id="sum"></span> &nbsp;<span class="rubl">p</span></span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <!-- /Lightbox -->

    <?= $page->slotRegionSelection() ?>
    <?= $page->slotJavascript() ?>
    <?= $page->slotInnerJavascript() ?>
    <?= $page->slotAuth() ?>
    <?= $page->slotYandexMetrika() ?>
    <?= $page->slotAdvanceSeoCounter() ?>

    <div id="gooReMaCategories" class="jsanalytics"></div>
    <div id="luxupTracker" class="jsanalytics"></div>

    <?//= $page->slotAdriver() ?>

</body>
</html>
