<?php
/**
 * @var $page                    \View\Jewel\ProductCategory\BranchPage
 * @var $category                \Model\Product\Category\Entity
 * @var $promoContent
 */
?>

<? if (\App::config()->adFox['enabled']): ?>
<div class="adfoxWrapper" id="adfox683sub"></div>
<? endif ?>

<div class="clear"></div>
<?= $page->tryRender('product-category/_categoryData', array('page' => $page, 'category' => $category)) ?>

<?= $promoContent ?>
