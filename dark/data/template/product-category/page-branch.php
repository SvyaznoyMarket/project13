<?php
/**
 * @var $page                    \View\ProductCategory\BranchPage
 * @var $category                \Model\Product\Category\Entity
 * @var $productFilter           \Model\Product\Filter
 * @var $productPagersByCategory \Iterator\EntityPager[]
 */
?>

<? require __DIR__ . '/_banner.php' ?>

<div class="clear"></div>

<? foreach ($category->getChild() as $child) { ?>
    <?
    $pager = $productPagersByCategory[$child->getId()];
    if (!$pager->count()) continue;
    ?>
    <?= $page->render('product/_slider-inCategory', array(
        'category'      => $child,
        'pager'         => $pager,
        'itemsInSlider' => ceil($pager->getMaxPerPage() / 2),
    )) ?>
<? } ?>