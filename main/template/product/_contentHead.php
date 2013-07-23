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

<div class="bProductSection clearfix">
    <?php echo $page->render('_breadcrumbs', array('breadcrumbs' => $breadcrumbs, 'class' => 'breadcrumbs')) ?>

    <? if ($hasSearch): ?>
    <noindex>
        <div class="searchbox">
            <?= $page->render('search/form-default') ?>
            <div id="searchAutocomplete"></div>
        </div>
    </noindex>
    <? endif ?>

    <div class="bPageHead">
        <? if ($product->getPrefix()): ?>
        <div class="bPageHead__eSubtitle"><?= $product->getPrefix() ?></div>
        <? endif ?>
        <h1 class="bPageHead__eTitle"><?= $product->getWebName() ?></h1>
        <span class="bPageHead__eArticle">Артикул: <?= $product->getArticle() ?></span>
    </div><!--/head section -->

