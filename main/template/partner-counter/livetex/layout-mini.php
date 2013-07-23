<?php
/**
 * @var $page \View\DefaultLayout
 */
?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
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
    <?= $page->slotMetaOg() ?>
</head>

<body class="<?= $page->slotBodyClassAttribute() ?>" data-template="<?= $page->slotBodyDataAttribute() ?>" data-id="<?= \App::$id ?>"<? if (\App::config()->debug): ?> data-debug=true<? endif ?>>
<?= $page->slotConfig() ?>
<div class="allpage" id="page">

    <div class="clearfix allpageinner<? if ('cart' == $page->slotBodyDataAttribute()): ?> buyingpage<? endif ?>" <? if ('product_card' == $page->slotBodyDataAttribute()): ?>itemscope itemtype="http://schema.org/Product"<? endif ?>>
        <?// $page->slotHeader() ?>
        <?// $page->slotContentHead() ?>

        <?= $page->slotContent() ?>

        <div class="clear"></div>
    </div>
    <div class="clear"></div>
</div>


<?// $page->slotFooter() ?>
<?// $page->slotUserbar() ?>

<?// $page->slotRegionSelection() ?>
<?= $page->slotBodyJavascript() ?>
<?= $page->slotInnerJavascript() ?>
<?// $page->slotAuth() ?>
<?// $page->slotYandexMetrika() ?>
<?// $page->slotAdvanceSeoCounter() ?>
<?// $page->slotMyThings() ?>
<?// $page->slotAdriver() ?>
<?// $page->slotPartnerCounter() ?>

<a id="upper" href="#">Наверх</a>

</body>
</html>