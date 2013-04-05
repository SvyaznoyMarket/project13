<?php
/**
 * @var $page     \View\ProductCategory\RootPage
 * @var $category \Model\Product\Category\Entity
 */
?>

<? if (\App::config()->adFox['enabled']): ?>
<div class="adfoxWrapper" id="adfox683"></div>
<? endif ?>

<div class="clear"></div>

<div class="goodslist">
<? foreach ($category->getChild() as $child): ?>
    <?= $page->render('product-category/_preview', array('category' => $child, 'rootCategory' => $category)) ?>
<? endforeach ?>
</div>
