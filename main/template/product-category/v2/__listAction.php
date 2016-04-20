<?php

/**
 * @param \Helper\TemplateHelper $helper
 * @param \Model\Product\Sorting $productSorting
 * @param \Iterator\EntityPager $pager
 * @param \Model\Product\Category\Entity|null $category
 */
return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Sorting $productSorting,
    \Iterator\EntityPager $pager,
    \Model\Product\Category\Entity $category = null
) {
    if ($category) {
        $chosenView = $category->getChosenView();
        $availableViews = $category->getAvailableForSwitchingViews();
    } else {
        $chosenView = '';
        $availableViews = [];
    }
?>

    <div class="sorting sorting-top clearfix js-category-sortingAndPagination">
        <?= $helper->render('product-category/v2/__sorting', ['productSorting' => $productSorting]) ?>

        <? if ($availableViews): ?>
            <div class="lstn-type__choose">
                <? foreach ($availableViews as $availableView): ?>
                    <div
                        class="
                            lstn-type-btn
                            <? if ($availableView === \Model\Product\Category\Entity::VIEW_EXPANDED): ?>lstn-type-btn--list<? else: ?>lstn-type-btn--bar<? endif ?>
                            <? if ($availableView === $chosenView): ?>active<? endif ?>
                            js-category-viewSwitcher-link
                        "
                        data-category-view="<?= $helper->escape($availableView) ?>"
                    ><i class="lstn-type-icon <? if ($availableView === \Model\Product\Category\Entity::VIEW_EXPANDED): ?>list<? else: ?>bar<? endif ?>"></i></div>
                <? endforeach ?>
            </div>
        <? endif ?>

        <?= $helper->render('product-category/v2/__pagination', ['pager' => $pager, 'category' => $category]) ?>
    </div>

<? };