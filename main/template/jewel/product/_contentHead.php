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

<div class="brandSection brandSectionPandora brandSectionPandora__product">

<div class="pagehead">

    <?php echo $page->render('_breadcrumbs', array('breadcrumbs' => $breadcrumbs, 'class' => 'breadcrumbs')) ?>

    <? if ($hasSearch): ?>
    <noindex>
        <div class="searchbox">
            <?= $page->render('search/form-default') ?>
            <div id="searchAutocomplete"></div>
        </div>
    </noindex>
    <? endif ?>

    <div class="clear"></div>

    <div class="category-name">ПОДВЕСКА-ШАРМ PANDORA</div>
    <? if ($title): ?><h1 itemprop="name"><?= $title ?></h1><? endif ?>

    <div class="clear<? if ($extendedMargin): ?> pb20<? endif ?>"></div>
    <? if ($hasSeparateLine): ?>
    <div class="line"></div>
    <? endif ?>
</div>
