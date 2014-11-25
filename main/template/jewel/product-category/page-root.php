<?php
/**
 * @var $page     \View\Jewel\ProductCategory\RootPage
 * @var $category \Model\Product\Category\Entity
 */
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

<?= $page->tryRender('product-category/_categoryData', array('page' => $page, 'category' => $category)) ?>

<? if(!empty($seoContent)): ?>
    <div class="bSeoText">
        <?= $seoContent ?>
    </div>
<? endif ?>