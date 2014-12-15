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

    <div class="bRangeSlider js-category-filter-rangeSlider">
        <input class="bRangeSlider__eInput mFromRange js-category-filter-rangeSlider-from" name="<?= \View\Name::productCategoryFilter($property, 'from') ?>" value="<?= $helper->clearZeroValue($productFilter->getValueMin($property)) ?>" type="text" />

        <div class="bFilterSlider js-category-filter-rangeSlider-slider" data-config="<?= $helper->json($data) ?>">
            <a class="ui-slider-handle ui-state-default ui-corner-all" href="#"></a>
            <a class="ui-slider-handle ui-state-default ui-corner-all" href="#"></a>
        </div>

        <input class="bRangeSlider__eInput mLast mToRange js-category-filter-rangeSlider-to" name="<?= \View\Name::productCategoryFilter($property, 'to') ?>" value="<?= $helper->clearZeroValue($productFilter->getValueMax($property)) ?>" type="text" />

        <? if ($property->isPrice()): ?>
            <span class="bRangeSlider__eRub rubl">p</span>
        <? else: ?>
            <span class="bRangeSlider__eRub"><?= $property->getUnit() ?></span>
        <? endif ?>
    </div>


<? };