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

<div itemscope itemtype="http://schema.org/Product" class="bProductSection clearfix<? if ('product.line' == \App::request()->attributes->get('route')): ?> mProductSectionSet<? endif ?>">
    

    <? if ($hasSearch): ?>
    <noindex>
        <div class="searchbox">
            <?= $page->render('search/form-default') ?>
            <div id="searchAutocomplete"></div>
        </div>
    </noindex>
    <? endif ?>

    <?php echo $page->render('_breadcrumbs', array('breadcrumbs' => $breadcrumbs, 'class' => 'breadcrumbs')) ?>

    <div class="bPageHead">
        <? if ($product->getPrefix()): ?>
        <div class="bPageHead__eSubtitle"><?= $product->getPrefix() ?></div>
        <? endif ?>
        <div class="bPageHead__eTitle clearfix">
            <h1 itemprop="name"><?= $product->getWebName() ?></h1>
            <? if (isset($trustfactors)): ?>
                <?= \App::closureTemplating()->render('product/__trustfactors', ['trustfactors' => $trustfactors, 'type' => 'top']) ?>
            <? endif ?>
        </div>
        <span class="bPageHead__eArticle">Артикул: <?= $product->getArticle() ?></span>
    </div><!--/head section -->