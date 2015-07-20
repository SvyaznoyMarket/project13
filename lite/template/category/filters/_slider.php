<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter,
    \Model\Product\Filter\Entity $filter
) {
    $data = [
        'min'  => $helper->clearZeroValue($filter->getMin()),
        'max'  => $helper->clearZeroValue($filter->getMax()),
        'step' => $filter->isPrice() ? 10 : $filter->getStepByFilter()
    ];
    ?>
    <div class="fltrRange fltrRange-v3 js-category-filter-rangeSlider <? if ($filter->isPrice()): ?>js-category-v2-filter-element-price<? endif ?>">
        <span class="fltrRange_lbl">от</span>
        <input class="fltrRange_it mFromRange js-category-filter-rangeSlider-from <? if ($filter->isPrice()): ?>js-category-v2-filter-element-price-from<? endif ?>" name="<?= \View\Name::productCategoryFilter($filter, 'from') ?>" value="<?= $helper->clearZeroValue($productFilter->getValueMin($filter)) ?>" type="text" data-min="<?= $filter->getMin() ?>" />

        <div class="fltrRange_sldr js-category-filter-rangeSlider-slider" data-config="<?= $helper->json($data) ?>">
            <a class="ui-slider-handle ui-state-default ui-corner-all" href="#"></a>
            <a class="ui-slider-handle ui-state-default ui-corner-all" href="#"></a>
        </div>
        <span class="fltrRange_lbl">до</span>
        <input class="fltrRange_it mLast mToRange js-category-filter-rangeSlider-to <? if ($filter->isPrice()): ?>js-category-v2-filter-element-price-to<? endif ?>" name="<?= \View\Name::productCategoryFilter($filter, 'to') ?>" value="<?= $helper->clearZeroValue($productFilter->getValueMax($filter)) ?>" type="text" data-max="<?= $filter->getMax() ?>" />

        <? if ($filter->isPrice()): ?>
            <span class="fltrRange_val rubl"><span class="rubl-css">P</span></span>
        <? else: ?>
            <span class="fltrRange_val"><?= $filter->getUnit() ?></span>
        <? endif ?>
    </div>
<? };