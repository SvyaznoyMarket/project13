<?php
/**
 * @var $page     \View\ProductCategory\RootPage
 * @var $category \Model\Product\Category\Entity
 */
?>

<? require __DIR__ . '/_banner.php' ?>

<div class="clear"></div>

<div class="goodslist">
<? foreach ($category->getChild() as $child): ?>
    <?= $page->render('product-category/_preview', array('category' => $child, 'rootCategory' => $category)) ?>
<? endforeach ?>
</div>
