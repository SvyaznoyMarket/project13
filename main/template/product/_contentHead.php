<?php
/**
 * @var $page        \View\Layout
 * @var $title       string|null
 * @var $breadcrumbs array('url' => null, 'name' => null)[]
 * @var $hasSearch   bool
 * @var $product     \Model\Product\Entity
 */
?>

<?
$hasSearch = isset($hasSearch) ? (bool)$hasSearch : true;
if (!isset($titlePrefix)) $titlePrefix = null;
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
        <? if ($product->getPrefix()): ?>
        <div class="bMainContainer__eHeader-subtitle"><?= $product->getPrefix() ?></div>
        <? endif ?>
        <h1 class="bMainContainer__eHeader-title"><?= $product->getWebName() ?></h1>
        <span class="bMainContainer__eHeader-article">Артикул: <?= $product->getArticle() ?></span>
    </div><!--/head section -->

