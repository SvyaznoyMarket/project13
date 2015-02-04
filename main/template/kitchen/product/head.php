<?php
/**
 * @var $page        \View\DefaultLayout
 * @var $title       string|null
 * @var $breadcrumbs array('url' => null, 'name' => null)[]
 * @var $hasSearch   bool
 * @var $product     \Model\Product\Entity
 * @var $reviewsData []|null
 */
?>

<?
if (!isset($title)) {
    $title = null;
}

if (!isset($breadcrumbs)) {
    $breadcrumbs = [];
}

$hasSearch = isset($hasSearch) ? (bool)$hasSearch : true;
if (!isset($titlePrefix)) $titlePrefix = null;
?>

<div class="product-container product-container--kitchen clearfix">


    <? if ($hasSearch && !$page->new_menu): ?>
    <noindex>
        <div class="searchbox">
            <?= $page->render('search/form-default') ?>
            <div id="searchAutocomplete"></div>
        </div>
    </noindex>
    <? endif ?>

    <?php echo $page->render('_breadcrumbs', array('breadcrumbs' => $breadcrumbs, 'class' => 'breadcrumbs')) ?>