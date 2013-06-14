<?php
/**
 * @var $page                    \View\ProductCategory\BranchPage
 * @var $category                \Model\Product\Category\Entity
 * @var $productFilter           \Model\Product\Filter
 * @var $productPagersByCategory \Iterator\EntityPager[]
 * @var $productVideosByProduct  array
 */
$count = 0;
?>

<? if (\App::config()->adFox['enabled']): ?>
<div class="adfoxWrapper" id="adfox683sub"></div>
<? endif ?>

<div class="clear"></div>
<?= $page->tryRender('product-category/_categoryData', array('page' => $page, 'category' => $category)) ?>

<? // в зависимости от настроек категории в json показываем иконки или линейки (линейки по умолчанию) ?>
<? if(!empty($catalogJson['category_layout_type']) && $catalogJson['category_layout_type'] == 'icons'): ?>
    <div class="goodslist clearfix">
    <? foreach ($category->getChild() as $child): ?>
        <?= $page->render('product-category/_preview', array('category' => $child, 'rootCategory' => $category)) ?>
    <? endforeach ?>
    </div>
<? elseif(!empty($promoContent)): ?>
    <?= $promoContent ?>
<? else: ?>
    <? foreach ($category->getChild() as $child) { ?>
        <?
        $pager = $productPagersByCategory[$child->getId()];
        if (!$pager->count()) continue;
        $count += $pager->count();
        ?>
        <?= $page->render('product/_slider-inCategory', array(
            'category'               => $child,
            'pager'                  => $pager,
            'itemsInSlider'          => ceil($pager->getMaxPerPage() / 2),
            'productVideosByProduct' => $productVideosByProduct,
        )) ?>
    <? }
    if (!$count) print "нет товаров";
    $page->setGlobalParam('productCount', $count);
    ?>
<? endif ?>

