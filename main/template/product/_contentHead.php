<?php
/**
 * @var $page            \View\Layout
 * @var $title           string|null
 * @var $breadcrumbs     array('url' => null, 'name' => null)[]
 * @var $hasSearch       bool
 * @var $hasSeparateLine bool
 * @var $extendedMargin  bool
 */
?>

<?
$hasSearch = isset($hasSearch) ? (bool)$hasSearch : true;
$hasSeparateLine = isset($hasSeparateLine) ? (bool)$hasSeparateLine : false;
$extendedMargin = isset($extendedMargin) ? (bool)$extendedMargin : false;
?>

<div class="bMainContainer bProductSection clearfix">
    <?php echo $page->render('_breadcrumbs', array('breadcrumbs' => $breadcrumbs, 'class' => 'breadcrumbs')) ?>

    <? if ($hasSearch): ?>
    <noindex>
        <div class="searchbox">
            <?= $page->render('search/form-default') ?>
            <div id="searchAutocomplete"></div>
        </div>
    </noindex>
    <? endif ?>

    <div class="bMainContainer__eHeader">
        <div class="bMainContainer__eHeader-subtitle">Планшетный компьютер</div>
        <h1 class="bMainContainer__eHeader-title">Prestigio MultiPad PMP5880C 8.0 Ultra Duo</h1>
        <span class="bMainContainer__eHeader-article">Артикул: 461-6459</span>
    </div><!--/head section -->

<? /*    
<div class="pagehead">

    

    <div class="clear"></div>

    <? if ($title): ?><h1 itemprop="name"><?= $title ?></h1><? endif ?>

    <div class="clear<? if ($extendedMargin): ?> pb20<? endif ?>"></div>
    <? if ($hasSeparateLine): ?>
    <div class="line"></div>
    <? endif ?>
</div>

*/?>