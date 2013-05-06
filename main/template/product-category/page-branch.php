<?php
/**
 * @var $page                    \View\ProductCategory\BranchPage
 * @var $category                \Model\Product\Category\Entity
 * @var $productFilter           \Model\Product\Filter
 * @var $productPagersByCategory \Iterator\EntityPager[]
 * @var $productVideosByProduct  array
 */
?>

<? if (\App::config()->adFox['enabled']): ?>
<div class="adfoxWrapper" id="adfox683sub"></div>
<? endif ?>

<div class="clear"></div>

<? foreach ($category->getChild() as $child) { ?>
    <?
    $pager = $productPagersByCategory[$child->getId()];
    if (!$pager->count()) continue;
    ?>
    <?= $page->render('product/_slider-inCategory', array(
        'category'               => $child,
        'pager'                  => $pager,
        'itemsInSlider'          => ceil($pager->getMaxPerPage() / 2),
        'productVideosByProduct' => $productVideosByProduct,
    )) ?>
<? } ?>
