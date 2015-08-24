<?php
/**
 * @var $page     \View\Jewel\ProductCategory\RootPage
 * @var $category \Model\Product\Category\Entity
 */
?>

<?
$helper = new \Helper\TemplateHelper();
?>

<? if (\App::config()->adFox['enabled']): ?>
    <div class="adfoxWrapper" id="adfox683"></div>
<? endif ?>

<div class="clear"></div>


<? if(!empty($promoContent)): ?>
    <?= $promoContent ?>
<? else: ?>
	<div class="goodslist clearfix">
	<? foreach ($category->getChild() as $child): ?>
	    <?= $page->render('product-category/_preview', array('category' => $child, 'rootCategory' => $category)) ?>
	<? endforeach ?>
	</div>
<? endif ?>

    <div class="clear"></div>

    <div style="margin: 0 auto 30px; width: 940px;">
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
    </div>

    <div class="clear"></div>


<?= $page->tryRender('product-category/_categoryData', array('page' => $page, 'category' => $category)) ?>

<? if(!empty($seoContent)): ?>
    <div class="bSeoText">
        <?= $seoContent ?>
    </div>
<? endif ?>