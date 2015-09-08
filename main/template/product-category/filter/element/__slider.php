<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter,
    \Model\Product\Filter\Entity $filter,
    array $promoStyle = []
) {
    $data = [
        'min'  => $helper->clearZeroValue($filter->getMin()),
        'max'  => $helper->clearZeroValue($filter->getMax()),
        'step' => $filter->isPrice() ? 10 : $filter->getStepByFilter()
    ];
?>


    <div class="bRangeSlider js-category-filter-rangeSlider <? if ($filter->isPrice()): ?>js-category-filter-element-price<? endif ?>" <? if(!empty($promoStyle['bRangeSlider'])): ?> style="<?= $promoStyle['bRangeSlider'] ?>"<? endif ?>>
        <? if ($filter->isPrice()): ?>
            <span class="bRangeSlider__eTitle"><?= $filter->getName() ?></span>
        <? endif ?>
        <input class="bRangeSlider__eInput mFromRange js-category-filter-rangeSlider-from" name="<?= \View\Name::productCategoryFilter($filter, 'from') ?>" value="<?= $helper->clearZeroValue($productFilter->getValueMin($filter)) ?>" type="text" />

        <div class="bFilterSlider js-category-filter-rangeSlider-slider" data-config="<?= $helper->json($data) ?>">
            <a class="ui-slider-handle ui-state-default ui-corner-all" href="#"></a>
            <a class="ui-slider-handle ui-state-default ui-corner-all" href="#"></a>
        </div>

        <input class="bRangeSlider__eInput mLast mToRange js-category-filter-rangeSlider-to" name="<?= \View\Name::productCategoryFilter($filter, 'to') ?>" value="<?= $helper->clearZeroValue($productFilter->getValueMax($filter)) ?>" type="text" />

        <? if ($filter->isPrice()): ?>
            <span class="bRangeSlider__eRub rubl">p</span>
        <? else: ?>
            <span class="bRangeSlider__eRub"><?= $filter->getUnit() ?></span>
        <? endif ?>
    </div>


<? };