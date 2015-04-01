<?php
/**
 * @var $page            \View\DefaultLayout
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

<div class="pagehead">

    <?php echo $page->render('_breadcrumbs', array('breadcrumbs' => $breadcrumbs, 'class' => 'breadcrumbs')) ?>

    <? if ($title): ?><h1><?= $title ?></h1><? endif ?>

    <div class="clear<? if ($extendedMargin): ?> pb20<? endif ?>"></div>
    <? if ($hasSeparateLine): ?>
    <div class="line"></div>
    <? endif ?>
</div>
