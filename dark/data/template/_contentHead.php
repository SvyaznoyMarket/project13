<?php
/**
 * @var $page        \View\Layout
 * @var $title       string|null
 * @var $breadcrumbs array('url' => null, 'name' => null)[]
 * @var $hasSearch   bool
 */
?>

<?
$hasSearch = isset($hasSearch) ? (bool)$hasSearch : true;
?>

<div class="pagehead">

    <?php echo $page->render('_breadcrumbs', array('breadcrumbs' => $breadcrumbs, 'class' => 'breadcrumbs')) ?>

    <div class="clear"></div>

    <? if ($title): ?><h1><?= $title ?></h1><? endif ?>

    <? if ($hasSearch): ?>
    <noindex>
        <div class="searchbox">
            <?= $page->render('search/form-default') ?>
        </div>
    </noindex>
    <? endif ?>

    <div class="clear"></div>
</div>
