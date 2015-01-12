<?php
return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter,
    \Model\Product\Filter\Entity $property
) {
    $data = [
        'min'  => $helper->clearZeroValue($property->getMin()),
        'max'  => $helper->clearZeroValue($property->getMax()),
        'step' => $property->isPrice() ? 10 : $property->getStepByFilter()
    ];
?>

    <div class="fltrRange fltrRange-v3 js-category-filter-rangeSlider <? if ($property->isPrice()): ?>js-gift-category-filter-element-price<? endif ?>">
        <span class="fltrRange_lbl">от</span>
        <input class="fltrRange_it mFromRange js-category-filter-rangeSlider-from" name="<?= \View\Name::productCategoryFilter($property, 'from') ?>" value="<?= $helper->clearZeroValue($productFilter->getValueMin($property)) ?>" type="text" />
        &nbsp;<span class="fltrRange_val rubl">p</span>

        <div class="fltrRange_sldr js-category-filter-rangeSlider-slider" data-config="<?= $helper->json($data) ?>">
            <a class="ui-slider-handle ui-state-default ui-corner-all" href="#"></a>
            <a class="ui-slider-handle ui-state-default ui-corner-all" href="#"></a>
        </div>

        <span class="fltrRange_lbl">до</span>
        <input class="fltrRange_it mLast mToRange js-category-filter-rangeSlider-to" name="<?= \View\Name::productCategoryFilter($property, 'to') ?>" value="<?= $helper->clearZeroValue($productFilter->getValueMax($property)) ?>" type="text" />

        <? if ($property->isPrice()): ?>
            <span class="fltrRange_val rubl">p</span>
        <? else: ?>
            <span class="fltrRange_val"><?= $property->getUnit() ?></span>
        <? endif ?>
    </div>

<? };