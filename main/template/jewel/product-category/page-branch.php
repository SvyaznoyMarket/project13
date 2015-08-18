<?php
/**
 * @var $page                    \View\Jewel\ProductCategory\BranchPage
 * @var $category                \Model\Product\Category\Entity
 * @var $promoContent
 */
?>

<?
$helper = new \Helper\TemplateHelper();
?>

<? if (\App::config()->adFox['enabled']): ?>
<div class="adfoxWrapper" id="adfox683sub"></div>
<? endif ?>

<?= $page->tryRender('product-category/_categoryData', array('page' => $page, 'category' => $category)) ?>

<? $isBranchPage = true ?>
<? require __DIR__ . '/_branch.php' ?>

<?= $promoContent ?>

<div class="clear"></div>

<? if (\App::config()->product['pullRecommendation'] && \App::config()->product['viewedEnabled']): ?>
    <?= $helper->render('product/__slider', [
        'type'      => 'viewed',
        'title'     => 'Вы смотрели',
        'products'  => [],
        'limit'     => \App::config()->product['itemsInSlider'],
        'page'      => 1,
        'url'       => $page->url('product.recommended'),
        'sender'    => [
            'name'     => 'enter',
            'position' => 'Viewed',
            'from'     => 'categoryPage'
        ],
    ]) ?>
<? endif ?>

<div class="clear"></div>
